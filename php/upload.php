<?php
// ============================================================
//  WEB4ALL – API Upload CV + Lettre de motivation
//  POST multipart/form-data
//  Champs : cv (fichier), lm (fichier), offre_id
// ============================================================
require_once __DIR__.'/config.php';
header('Content-Type: application/json; charset=utf-8');

$user = currentUser();
if (!$user)          jsonResponse(['ok'=>false,'msg'=>'Vous devez être connecté.'], 401);
if (!isEtudiant())   jsonResponse(['ok'=>false,'msg'=>'Seuls les étudiants peuvent envoyer des fichiers.'], 403);

$offreId = (int)($_POST['offre_id'] ?? 0);
if (!$offreId) jsonResponse(['ok'=>false,'msg'=>'ID offre manquant.'], 400);

// Vérifier que l'offre existe
$db   = getDB();
$stmt = $db->prepare('SELECT id FROM offres WHERE id=? AND actif=1');
$stmt->execute([$offreId]);
if (!$stmt->fetch()) jsonResponse(['ok'=>false,'msg'=>'Offre introuvable.'], 404);

// Vérifier que la candidature existe (postuler d'abord)
$stmt = $db->prepare('SELECT id FROM candidatures WHERE utilisateur_id=? AND offre_id=?');
$stmt->execute([$user['id'], $offreId]);
$cand = $stmt->fetch();
if (!$cand) jsonResponse(['ok'=>false,'msg'=>'Soumettez d\'abord votre candidature.'], 400);

const MAX_SIZE   = 20 * 1024 * 1024; // 20 Mo
const ALLOWED    = ['application/pdf'];
const ALLOWED_EXT= ['pdf'];

$uploadDir = dirname(__DIR__).'/uploads/';
$results   = [];

foreach (['cv','lm'] as $field) {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) continue;

    $file = $_FILES[$field];

    // Erreur upload PHP
    if ($file['error'] !== UPLOAD_ERR_OK) {
        jsonResponse(['ok'=>false,'msg'=>"Erreur upload ($field) : code ".$file['error']], 400);
    }

    // Taille
    if ($file['size'] > MAX_SIZE) {
        jsonResponse(['ok'=>false,'msg'=>"Le fichier $field dépasse 2 Mo."], 400);
    }

    // Extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXT)) {
        jsonResponse(['ok'=>false,'msg'=>"Le fichier $field doit être un PDF."], 400);
    }

    // Type MIME réel (pas celui déclaré par le client)
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, ALLOWED)) {
        jsonResponse(['ok'=>false,'msg'=>"Le fichier $field n'est pas un PDF valide."], 400);
    }

    // Nom de fichier sécurisé : userId_offreId_type_timestamp.pdf
    $filename = sprintf('%d_%d_%s_%s.pdf', $user['id'], $offreId, $field, time());
    $dest     = $uploadDir.$filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        jsonResponse(['ok'=>false,'msg'=>"Impossible de sauvegarder le fichier $field."], 500);
    }

    $results[$field] = $filename;
}

if (empty($results)) {
    jsonResponse(['ok'=>false,'msg'=>'Aucun fichier reçu.'], 400);
}

// Sauvegarder les chemins en BDD
if (!empty($results['cv'])) {
    $db->prepare('UPDATE candidatures SET cv_path=? WHERE utilisateur_id=? AND offre_id=?')
       ->execute([$results['cv'], $user['id'], $offreId]);
}
if (!empty($results['lm'])) {
    $db->prepare('UPDATE candidatures SET lm_path=? WHERE utilisateur_id=? AND offre_id=?')
       ->execute([$results['lm'], $user['id'], $offreId]);
}

jsonResponse(['ok'=>true, 'msg'=>'Fichier(s) uploadé(s) avec succès.', 'files'=>$results]);
