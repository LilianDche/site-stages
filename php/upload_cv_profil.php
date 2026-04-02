<?php
// ============================================================
//  WEB4ALL – Upload CV de profil (indépendant des candidatures)
//  POST multipart/form-data — champ : cv
// ============================================================
require_once __DIR__.'/config.php';
header('Content-Type: application/json; charset=utf-8');

$user = currentUser();
if (!$user) jsonResponse(['ok'=>false,'msg'=>'Vous devez être connecté.'], 401);

// ── SUPPRESSION du CV de profil ──────────────────────────────
if (($_POST['action']??'') === 'supprimer') {
    $db   = getDB();
    $stmt = $db->prepare('SELECT cv_path FROM utilisateurs WHERE id=?');
    $stmt->execute([$user['id']]);
    $row  = $stmt->fetch();
    if ($row && $row['cv_path']) {
        $path = dirname(__DIR__).'/uploads/'.$row['cv_path'];
        if (file_exists($path)) @unlink($path);
    }
    $db->prepare('UPDATE utilisateurs SET cv_path=NULL WHERE id=?')->execute([$user['id']]);
    $_SESSION['user']['cv_path'] = null;
    jsonResponse(['ok'=>true,'msg'=>'CV supprimé.']);
}

// ── UPLOAD ───────────────────────────────────────────────────
if (empty($_FILES['cv']) || $_FILES['cv']['error'] === UPLOAD_ERR_NO_FILE) {
    jsonResponse(['ok'=>false,'msg'=>'Aucun fichier reçu.'], 400);
}

$file = $_FILES['cv'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    jsonResponse(['ok'=>false,'msg'=>'Erreur upload : code '.$file['error']], 400);
}

const CV_MAX_SIZE = 20 * 1024 * 1024; // 20 Mo
if ($file['size'] > CV_MAX_SIZE) {
    jsonResponse(['ok'=>false,'msg'=>'Le fichier dépasse 20 Mo.'], 400);
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($ext !== 'pdf') {
    jsonResponse(['ok'=>false,'msg'=>'Le CV doit être un fichier PDF.'], 400);
}

// Vérification MIME réel
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);
if ($mimeType !== 'application/pdf') {
    jsonResponse(['ok'=>false,'msg'=>'Le fichier n\'est pas un PDF valide.'], 400);
}

$uploadDir = dirname(__DIR__).'/uploads/';

// Supprimer l'ancien CV s'il existe
$db   = getDB();
$stmt = $db->prepare('SELECT cv_path FROM utilisateurs WHERE id=?');
$stmt->execute([$user['id']]);
$row  = $stmt->fetch();
if ($row && $row['cv_path']) {
    $old = $uploadDir.$row['cv_path'];
    if (file_exists($old)) @unlink($old);
}

// Nom sécurisé
$filename = sprintf('profil_%d_%s.pdf', $user['id'], time());
$dest     = $uploadDir.$filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    jsonResponse(['ok'=>false,'msg'=>'Impossible de sauvegarder le fichier.'], 500);
}

$db->prepare('UPDATE utilisateurs SET cv_path=? WHERE id=?')->execute([$filename, $user['id']]);
$_SESSION['user']['cv_path'] = $filename;

jsonResponse(['ok'=>true,'msg'=>'CV mis à jour avec succès.','cv_path'=>$filename]);
