<?php
// ============================================================
//  WEB4ALL – API Messagerie interne
// ============================================================
require_once __DIR__.'/config.php';
header('Content-Type: application/json; charset=utf-8');

$user = currentUser();
if (!$user) jsonResponse(['ok'=>false,'msg'=>'Non connecté.'], 401);
if ($user['statut'] !== 'actif') jsonResponse(['ok'=>false,'msg'=>'Compte inactif.'], 403);

$data   = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$action = $data['action'] ?? $_GET['action'] ?? '';
$db     = getDB();
$myId   = (int)$user['id'];

switch ($action) {

    // ── Nombre de messages non lus ──────────────────────────
    case 'nb_non_lus':
        $stmt = $db->prepare(
            'SELECT COUNT(*) FROM messages m
             JOIN conversations c ON c.id = m.conversation_id
             WHERE m.lu = 0 AND m.expediteur_id != ?
               AND (c.user1_id = ? OR c.user2_id = ?)'
        );
        $stmt->execute([$myId, $myId, $myId]);
        jsonResponse(['nb' => (int)$stmt->fetchColumn()]);
        break;

    // ── Liste des conversations ─────────────────────────────
    case 'conversations':
        $stmt = $db->prepare(
            'SELECT c.id, c.updated_at,
                    u1.id AS u1_id, u1.prenom AS u1_prenom, u1.nom AS u1_nom, u1.role AS u1_role,
                    u2.id AS u2_id, u2.prenom AS u2_prenom, u2.nom AS u2_nom, u2.role AS u2_role,
                    (SELECT contenu FROM messages WHERE conversation_id=c.id ORDER BY created_at DESC LIMIT 1) AS dernier_msg,
                    (SELECT COUNT(*) FROM messages WHERE conversation_id=c.id AND lu=0 AND expediteur_id!=?) AS nb_non_lus
             FROM conversations c
             JOIN utilisateurs u1 ON u1.id = c.user1_id
             JOIN utilisateurs u2 ON u2.id = c.user2_id
             WHERE c.user1_id=? OR c.user2_id=?
             ORDER BY c.updated_at DESC'
        );
        $stmt->execute([$myId, $myId, $myId]);
        jsonResponse($stmt->fetchAll());
        break;

    // ── Messages d'une conversation ─────────────────────────
    case 'messages':
        $convId = (int)($data['conversation_id'] ?? $_GET['conversation_id'] ?? 0);
        if (!$convId) jsonResponse(['ok'=>false,'msg'=>'ID conversation manquant.'], 400);

        // Vérifier que l'utilisateur est bien dans cette conversation
        $chk = $db->prepare('SELECT id FROM conversations WHERE id=? AND (user1_id=? OR user2_id=?)');
        $chk->execute([$convId, $myId, $myId]);
        if (!$chk->fetch()) jsonResponse(['ok'=>false,'msg'=>'Accès refusé.'], 403);

        // Marquer les messages reçus comme lus
        $db->prepare('UPDATE messages SET lu=1 WHERE conversation_id=? AND expediteur_id!=?')
           ->execute([$convId, $myId]);

        $stmt = $db->prepare(
            'SELECT m.id, m.contenu, m.lu, m.created_at,
                    m.expediteur_id,
                    u.prenom, u.nom, u.role
             FROM messages m
             JOIN utilisateurs u ON u.id = m.expediteur_id
             WHERE m.conversation_id=?
             ORDER BY m.created_at ASC'
        );
        $stmt->execute([$convId]);
        jsonResponse($stmt->fetchAll());
        break;

    // ── Envoyer un message ──────────────────────────────────
    case 'envoyer':
        $destId = (int)($data['destinataire_id'] ?? 0);
        $contenu = trim($data['contenu'] ?? '');

        if (!$destId)  jsonResponse(['ok'=>false,'msg'=>'Destinataire manquant.'], 400);
        if (!$contenu) jsonResponse(['ok'=>false,'msg'=>'Message vide.'], 400);
        if (strlen($contenu) > 2000) jsonResponse(['ok'=>false,'msg'=>'Message trop long (max 2000 caractères).'], 400);
        if ($destId === $myId) jsonResponse(['ok'=>false,'msg'=>'Vous ne pouvez pas vous écrire à vous-même.'], 400);

        // Vérifier que le destinataire existe et est actif
        $dStmt = $db->prepare('SELECT id,prenom,nom,role FROM utilisateurs WHERE id=? AND statut="actif"');
        $dStmt->execute([$destId]);
        $dest = $dStmt->fetch();
        if (!$dest) jsonResponse(['ok'=>false,'msg'=>'Destinataire introuvable.'], 404);

        // Trouver ou créer la conversation
        // Les IDs sont toujours stockés dans l'ordre croissant pour éviter les doublons
        $uid1 = min($myId, $destId);
        $uid2 = max($myId, $destId);

        $cStmt = $db->prepare('SELECT id FROM conversations WHERE user1_id=? AND user2_id=?');
        $cStmt->execute([$uid1, $uid2]);
        $conv = $cStmt->fetch();

        if (!$conv) {
            $db->prepare('INSERT INTO conversations (user1_id, user2_id) VALUES (?,?)')->execute([$uid1, $uid2]);
            $convId = $db->lastInsertId();
        } else {
            $convId = $conv['id'];
        }

        // Insérer le message
        $db->prepare('INSERT INTO messages (conversation_id, expediteur_id, contenu) VALUES (?,?,?)')
           ->execute([$convId, $myId, $contenu]);
        $msgId = $db->lastInsertId();

        // Mettre à jour updated_at de la conversation
        $db->prepare('UPDATE conversations SET updated_at=NOW() WHERE id=?')->execute([$convId]);

        jsonResponse([
            'ok'              => true,
            'conversation_id' => $convId,
            'message_id'      => $msgId,
            'msg'             => 'Message envoyé.',
        ]);
        break;

    // ── Chercher un utilisateur à qui écrire ───────────────
    case 'chercher_utilisateur':
        $q = trim($data['q'] ?? $_GET['q'] ?? '');
        if (strlen($q) < 2) jsonResponse([]);
        $like = '%'.$q.'%';
        // Étudiants peuvent écrire aux pilotes/admins, pilotes aux étudiants/admins, admins à tout le monde
        $stmt = $db->prepare(
            'SELECT id, prenom, nom, role, entreprise_id,
                    (SELECT nom FROM entreprises WHERE id=utilisateurs.entreprise_id) AS entreprise_nom
             FROM utilisateurs
             WHERE statut="actif" AND id!=?
               AND (CONCAT(prenom," ",nom) LIKE ? OR email LIKE ?)
             ORDER BY role, nom
             LIMIT 10'
        );
        $stmt->execute([$myId, $like, $like]);
        jsonResponse($stmt->fetchAll());
        break;

    // ── Supprimer une conversation ──────────────────────────
    case 'supprimer_conversation':
        $convId = (int)($data['conversation_id'] ?? 0);
        $chk = $db->prepare('SELECT id FROM conversations WHERE id=? AND (user1_id=? OR user2_id=?)');
        $chk->execute([$convId, $myId, $myId]);
        if (!$chk->fetch()) jsonResponse(['ok'=>false,'msg'=>'Accès refusé.'], 403);
        $db->prepare('DELETE FROM conversations WHERE id=?')->execute([$convId]);
        jsonResponse(['ok'=>true]);
        break;

    default:
        jsonResponse(['error'=>'Action inconnue.'], 400);
        break;
}
