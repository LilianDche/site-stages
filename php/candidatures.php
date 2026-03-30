<?php
require_once __DIR__.'/config.php';
header('Content-Type: application/json; charset=utf-8');

$user   = currentUser();
if (!$user) jsonResponse(['ok'=>false,'msg'=>'Connectez-vous.'], 401);

$data   = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$action = $data['action'] ?? $_GET['action'] ?? '';
$db     = getDB();

switch ($action) {

    case 'postuler':
        if (!isEtudiant()) jsonResponse(['ok'=>false,'msg'=>'Seuls les étudiants peuvent postuler.'], 403);
        $offreId = (int)($data['offre_id'] ?? 0);
        $message = trim($data['message']   ?? '');
        if (!$offreId || !$message) jsonResponse(['ok'=>false,'msg'=>'Offre et message requis.'], 400);

        $stmt = $db->prepare('SELECT id FROM offres WHERE id=? AND actif=1');
        $stmt->execute([$offreId]);
        if (!$stmt->fetch()) jsonResponse(['ok'=>false,'msg'=>'Offre introuvable.'], 404);

        $stmt = $db->prepare('SELECT id FROM candidatures WHERE utilisateur_id=? AND offre_id=?');
        $stmt->execute([$user['id'], $offreId]);
        if ($stmt->fetch()) jsonResponse(['ok'=>false,'msg'=>'Vous avez déjà postulé.'], 409);

        $db->prepare('INSERT INTO candidatures (utilisateur_id,offre_id,message) VALUES (?,?,?)')
           ->execute([$user['id'], $offreId, $message]);
        jsonResponse(['ok'=>true,'msg'=>'Candidature envoyée !']);

    case 'mes_candidatures':
        $stmt = $db->prepare(
            'SELECT c.id, c.statut, c.created_at, c.message,
             o.id AS offre_id, o.titre, o.lieu, o.type, o.duree, e.nom AS entreprise,
             c.cv_path, c.lm_path
             FROM candidatures c
             JOIN offres o ON o.id=c.offre_id
             JOIN entreprises e ON e.id=o.entreprise_id
             WHERE c.utilisateur_id=? ORDER BY c.created_at DESC');
        $stmt->execute([$user['id']]);
        jsonResponse($stmt->fetchAll());

    default:
        jsonResponse(['error'=>'Action inconnue.'], 400);
}
