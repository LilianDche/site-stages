<?php
// API Promotions
require_once __DIR__.'/config.php';
header('Content-Type: application/json; charset=utf-8');
$db     = getDB();
$data   = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$action = $data['action'] ?? $_GET['action'] ?? 'liste';

switch ($action) {
    case 'liste':
        $stmt = $db->query('SELECT p.*, COUNT(u.id) AS nb_etudiants FROM promotions p
                            LEFT JOIN utilisateurs u ON u.promotion_id=p.id AND u.role="etudiant"
                            GROUP BY p.id ORDER BY p.annee DESC, p.nom');
        jsonResponse($stmt->fetchAll());

    case 'etudiants':
        if (!isPilote()) jsonResponse(['ok'=>false,'msg'=>'Accès refusé.'], 403);
        $id   = (int)($_GET['id'] ?? 0);
        $stmt = $db->prepare('SELECT u.id, u.prenom, u.nom, u.email,
                              (SELECT COUNT(*) FROM candidatures WHERE utilisateur_id=u.id) AS nb_candidatures,
                              (SELECT COUNT(*) FROM candidatures WHERE utilisateur_id=u.id AND statut="acceptee") AS nb_acceptees
                              FROM utilisateurs u WHERE u.promotion_id=? AND u.role="etudiant" ORDER BY u.nom');
        $stmt->execute([$id]);
        jsonResponse($stmt->fetchAll());

    case 'creer':
        requireAdmin();
        $nom   = trim($data['nom']   ?? '');
        $annee = (int)($data['annee'] ?? date('Y'));
        if (!$nom) jsonResponse(['ok'=>false,'msg'=>'Nom obligatoire.'], 400);
        $db->prepare('INSERT INTO promotions (nom,annee,description) VALUES (?,?,?)')->execute([$nom,$annee,$data['description']??null]);
        jsonResponse(['ok'=>true,'id'=>$db->lastInsertId(),'msg'=>'Promotion créée.']);

    case 'supprimer':
        requireAdmin();
        $id = (int)($data['id'] ?? 0);
        $db->prepare('DELETE FROM promotions WHERE id=?')->execute([$id]);
        jsonResponse(['ok'=>true,'msg'=>'Promotion supprimée.']);

    default:
        jsonResponse(['error'=>'Action inconnue.'], 400);
}
