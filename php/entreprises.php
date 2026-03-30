<?php
// API Entreprises – lecture publique, écriture pilote/admin
require_once __DIR__.'/config.php';
header('Content-Type: application/json; charset=utf-8');
$db     = getDB();
$data   = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$action = $data['action'] ?? $_GET['action'] ?? 'liste';

switch ($action) {

    case 'liste':
        $stmt = $db->query('SELECT e.*, COUNT(o.id) AS nb_offres
                            FROM entreprises e
                            LEFT JOIN offres o ON o.entreprise_id=e.id AND o.actif=1
                            GROUP BY e.id ORDER BY e.nom');
        jsonResponse($stmt->fetchAll());

    case 'detail':
        $id   = (int)($_GET['id'] ?? 0);
        $stmt = $db->prepare('SELECT * FROM entreprises WHERE id=?');
        $stmt->execute([$id]);
        $ent  = $stmt->fetch();
        if (!$ent) jsonResponse(['error'=>'Introuvable'], 404);
        // Offres de l'entreprise
        $os = $db->prepare('SELECT id,titre,type,lieu,duree,remuneration,actif FROM offres WHERE entreprise_id=? ORDER BY created_at DESC');
        $os->execute([$id]);
        $ent['offres'] = $os->fetchAll();
        jsonResponse($ent);

    case 'creer':
        // Seul l'admin peut créer une entreprise directement
        if (!isAdmin()) jsonResponse(['ok'=>false,'msg'=>'Seul un administrateur peut créer une entreprise.'], 403);
        // fall through
    case 'modifier':
        if (!isPilote()) jsonResponse(['ok'=>false,'msg'=>'Accès refusé.'], 403);
        $nom = trim($data['nom'] ?? '');
        if (!$nom) jsonResponse(['ok'=>false,'msg'=>'Nom obligatoire.'], 400);
        // Modifier : pilote ne peut modifier que son entreprise (sauf admin)
        if ($action==='modifier' && !isAdmin()) {
            $eid = (int)($data['id'] ?? 0);
            $u   = currentUser();
            if ((int)($u['entreprise_id'] ?? 0) !== $eid)
                jsonResponse(['ok'=>false,'msg'=>'Vous ne pouvez modifier que votre propre entreprise.'], 403);
        }

        $fields = ['nom','secteur','adresse','ville','code_postal','pays','email_contact','telephone','site_web','description'];
        $vals   = array_map(fn($f) => trim($data[$f] ?? ''), $fields);

        if ($action === 'creer') {
            $ph = implode(',', array_fill(0, count($fields), '?'));
            $db->prepare('INSERT INTO entreprises ('.implode(',',$fields).') VALUES ('.$ph.')')->execute($vals);
            jsonResponse(['ok'=>true, 'id'=>$db->lastInsertId(), 'msg'=>'Entreprise créée.']);
        } else {
            $id = (int)($data['id'] ?? 0);
            if (!$id) jsonResponse(['ok'=>false,'msg'=>'ID manquant.'], 400);
            $set = implode(',', array_map(fn($f) => "$f=?", $fields));
            $db->prepare("UPDATE entreprises SET $set WHERE id=?")->execute([...$vals, $id]);
            jsonResponse(['ok'=>true, 'msg'=>'Entreprise mise à jour.']);
        }

    case 'supprimer':
        if (!isPilote()) jsonResponse(['ok'=>false,'msg'=>'Accès refusé.'], 403);
        $id = (int)($data['id'] ?? 0);
        if (!$id) jsonResponse(['ok'=>false,'msg'=>'ID manquant.'], 400);
        // Pilote : ne peut supprimer que son entreprise. Admin : peut tout supprimer.
        if (!isAdmin()) {
            $u = currentUser();
            if ((int)($u['entreprise_id'] ?? 0) !== $id)
                jsonResponse(['ok'=>false,'msg'=>'Vous ne pouvez supprimer que votre propre entreprise.'], 403);
        }
        $db->prepare('DELETE FROM entreprises WHERE id=?')->execute([$id]);
        jsonResponse(['ok'=>true, 'msg'=>'Entreprise supprimée.']);

    default:
        jsonResponse(['error'=>'Action inconnue.'], 400);
}
