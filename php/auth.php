<?php
require_once __DIR__.'/config.php';
header('Content-Type: application/json; charset=utf-8');
$data   = json_decode(file_get_contents('php://input'),true)??$_POST;
$action = $data['action']??$_GET['action']??'';

switch($action){

case 'login':
    $email=trim($data['email']??''); $pass=$data['password']??'';
    if(!$email||!$pass) jsonResponse(['ok'=>false,'msg'=>'Champs obligatoires.'],400);
    $db=getDB();
    $stmt=$db->prepare('SELECT u.*,p.nom AS promo_nom,e.nom AS entreprise_nom
                        FROM utilisateurs u
                        LEFT JOIN promotions  p ON p.id=u.promotion_id
                        LEFT JOIN entreprises e ON e.id=u.entreprise_id
                        WHERE u.email=?');
    $stmt->execute([strtolower($email)]);
    $user=$stmt->fetch();
    if(!$user||!password_verify($pass,$user['password']))
        jsonResponse(['ok'=>false,'msg'=>'Email ou mot de passe incorrect.'],401);
    if($user['statut']==='en_attente')
        jsonResponse(['ok'=>false,'msg'=>'Votre compte est en attente de validation par un administrateur.'],403);
    if($user['statut']==='suspendu')
        jsonResponse(['ok'=>false,'msg'=>'Votre compte a été suspendu. Contactez un administrateur.'],403);
    unset($user['password']);
    $_SESSION['user']=$user;
    jsonResponse(['ok'=>true,'user'=>$user]);

case 'register':
    $prenom  = trim($data['prenom']   ?? '');
    $nom     = trim($data['nom']      ?? '');
    $email   = trim($data['email']    ?? '');
    $pass    =      $data['password'] ?? '';
    $role    =      $data['role']     ?? 'etudiant';
    $promoId = !empty($data['promotion_id'])   ? (int)$data['promotion_id'] : null;
    // Pilote : nom libre de l'entreprise (pas un ID existant)
    $nomEntreprise = trim($data['nom_entreprise']  ?? '');
    $entrepriseId  = !empty($data['entreprise_id']) ? (int)$data['entreprise_id'] : null;

    if(!$prenom||!$nom||!$email||!$pass)
        jsonResponse(['ok'=>false,'msg'=>'Tous les champs sont obligatoires.'],400);
    if(!filter_var($email,FILTER_VALIDATE_EMAIL))
        jsonResponse(['ok'=>false,'msg'=>'Email invalide.'],400);
    if(strlen($pass)<6)
        jsonResponse(['ok'=>false,'msg'=>'Mot de passe trop court (min. 6 car.).'],400);
    if(!in_array($role,['etudiant','pilote']))
        jsonResponse(['ok'=>false,'msg'=>'Rôle invalide.'],400);
    if($role==='pilote'&&!$nomEntreprise&&!$entrepriseId)
        jsonResponse(['ok'=>false,'msg'=>'Sélectionnez ou saisissez votre entreprise.'],400);

    $db=getDB();
    $s=$db->prepare('SELECT id, statut FROM utilisateurs WHERE email=?');
    $s->execute([strtolower($email)]);
    $existing = $s->fetch();
    if($existing){
        if($existing['statut'] === 'suspendu')
            jsonResponse(['ok'=>false,'msg'=>'Cette adresse email est bloquée. Contactez un administrateur.'],403);
        jsonResponse(['ok'=>false,'msg'=>'Email déjà utilisé.'],409);
    }

    // Les pilotes sont en attente, les étudiants sont actifs
    $statut = ($role==='pilote') ? 'en_attente' : 'actif';

    $db->prepare(
        'INSERT INTO utilisateurs (prenom,nom,email,password,role,promotion_id,entreprise_id,statut,nom_entreprise_demande)
         VALUES (?,?,?,?,?,?,?,?,?)'
    )->execute([
        $prenom, $nom, strtolower($email),
        password_hash($pass, PASSWORD_BCRYPT),
        $role,
        $promoId,
        // Si entreprise existante sélectionnée, on la lie directement
        ($role==='pilote' && $entrepriseId) ? $entrepriseId : null,
        $statut,
        // Si "Autre", stocker le nom pour que l'admin crée l'entreprise
        ($role==='pilote' && !$entrepriseId) ? $nomEntreprise : null,
    ]);

    $msg = ($role==='pilote')
        ? 'Demande envoyée ! Votre compte sera activé par un administrateur sous 48h.'
        : 'Compte créé avec succès ! Vous pouvez vous connecter.';
    jsonResponse(['ok'=>true,'msg'=>$msg,'pending'=>($statut==='en_attente')]);

case 'logout':
    session_destroy();
    jsonResponse(['ok'=>true]);

case 'me':
    $user=currentUser();
    jsonResponse(['ok'=>(bool)$user,'user'=>$user]);

case 'update_profil':
    $prenom  = trim($data['prenom'] ?? '');
    $nom     = trim($data['nom']    ?? '');
    $email   = trim($data['email']  ?? '');
    $promoId = isset($data['promotion_id'])?(int)$data['promotion_id']:$user['promotion_id'];
    if(!$prenom||!$nom||!$email) jsonResponse(['ok'=>false,'msg'=>'Champs obligatoires.'],400);
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)) jsonResponse(['ok'=>false,'msg'=>'Email invalide.'],400);
    $db=getDB();
    $db->prepare('UPDATE utilisateurs SET prenom=?,nom=?,email=?,promotion_id=? WHERE id=?')
       ->execute([$prenom,$nom,strtolower($email),$promoId?:null,$user['id']]);
    if(isset($data['competences'])&&is_array($data['competences'])){
        $db->prepare('DELETE FROM etudiant_competences WHERE utilisateur_id=?')->execute([$user['id']]);
        $ins=$db->prepare('INSERT IGNORE INTO etudiant_competences VALUES (?,?)');
        foreach($data['competences'] as $cid) $ins->execute([$user['id'],(int)$cid]);
    }
    $_SESSION['user']=array_merge($user,['prenom'=>$prenom,'nom'=>$nom,'email'=>strtolower($email),'promotion_id'=>$promoId]);
    jsonResponse(['ok'=>true,'user'=>$_SESSION['user']]);

case 'change_password':
    $user=currentUser(); if(!$user) jsonResponse(['ok'=>false,'msg'=>'Non connecté.'],401);
    $oldPass = $data['old_password'] ?? '';
    $newPass = $data['new_password'] ?? '';
    if(!$oldPass||!$newPass) jsonResponse(['ok'=>false,'msg'=>'Champs obligatoires.'],400);
    if(strlen($newPass)<6) jsonResponse(['ok'=>false,'msg'=>'Nouveau mot de passe trop court (min. 6 car.).'],400);
    $db=getDB();
    $stmt=$db->prepare('SELECT password FROM utilisateurs WHERE id=?');
    $stmt->execute([$user['id']]);
    $row=$stmt->fetch();
    if(!$row||!password_verify($oldPass,$row['password']))
        jsonResponse(['ok'=>false,'msg'=>'Mot de passe actuel incorrect.'],401);
    $db->prepare('UPDATE utilisateurs SET password=? WHERE id=?')
       ->execute([password_hash($newPass,PASSWORD_BCRYPT),$user['id']]);
    jsonResponse(['ok'=>true,'msg'=>'Mot de passe modifié avec succès.']);

case 'forgot_password':
    $email = strtolower(trim($data['email'] ?? ''));
    if(!$email || !filter_var($email, FILTER_VALIDATE_EMAIL))
        jsonResponse(['ok'=>false,'msg'=>'Email invalide.'], 400);
    $db = getDB();
    $stmt = $db->prepare('SELECT id, prenom, statut FROM utilisateurs WHERE email=?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    // Toujours répondre OK pour ne pas révéler si l'email existe
    if(!$user || $user['statut'] === 'suspendu'){
        jsonResponse(['ok'=>true,'msg'=>'Si cet email existe, un lien vous a été envoyé.']);
    }
    // Générer token sécurisé valable 1h
    $token   = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', time() + 3600);
    $db->prepare('UPDATE utilisateurs SET reset_token=?, reset_token_expires=? WHERE id=?')
       ->execute([$token, $expires, $user['id']]);
    $resetUrl = SITE_URL . '/reset-password.php?token=' . $token;

    $subject  = '[Web4All] Réinitialisation de votre mot de passe';
    $bodyText = "Bonjour " . $user['prenom'] . ",\n\n"
              . "Vous avez demandé à réinitialiser votre mot de passe.\n\n"
              . "Cliquez sur ce lien (valable 1 heure) :\n"
              . $resetUrl . "\n\n"
              . "Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.\n\n"
              . "L'équipe Web4All";
    $bodyHtml = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="font-family:Arial,sans-serif;background:#f5f5f5;padding:2rem">'
              . '<div style="max-width:520px;margin:auto;background:#fff;border-radius:12px;padding:2rem;box-shadow:0 2px 12px rgba(0,0,0,.08)">'
              . '<h2 style="color:#e91e8c;margin-top:0">Web4All</h2>'
              . '<p>Bonjour <strong>' . htmlspecialchars($user['prenom'], ENT_QUOTES, 'UTF-8') . '</strong>,</p>'
              . '<p>Vous avez demandé à réinitialiser votre mot de passe.</p>'
              . '<p style="margin:1.5rem 0">'
              . '<a href="' . htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8') . '" '
              . 'style="background:#e91e8c;color:#fff;padding:.75rem 1.5rem;border-radius:8px;text-decoration:none;font-weight:600">Réinitialiser mon mot de passe</a>'
              . '</p>'
              . '<p style="font-size:.85rem;color:#888">Ce lien est valable <strong>1 heure</strong>. Si vous n\'êtes pas à l\'origine de cette demande, ignorez cet email.</p>'
              . '<hr style="border:none;border-top:1px solid #eee;margin:1.5rem 0">'
              . '<p style="font-size:.8rem;color:#aaa">L\'équipe Web4All</p>'
              . '</div></body></html>';

    require_once __DIR__ . '/mailer.php';
    $mailer  = new Mailer();
    $sent    = $mailer->send($email, $subject, $bodyText, $bodyHtml);
    if (!$sent) {
        error_log("[Web4All] Échec envoi email reset à $email");
    }
    jsonResponse(['ok'=>true,'msg'=>'Si cet email existe, un lien vous a été envoyé.']);

case 'reset_password':
    $token   = trim($data['token']    ?? '');
    $newPass = trim($data['password'] ?? '');
    if(!$token || !$newPass) jsonResponse(['ok'=>false,'msg'=>'Données manquantes.'], 400);
    if(strlen($newPass) < 6)  jsonResponse(['ok'=>false,'msg'=>'Mot de passe trop court (min. 6 car.).'], 400);
    $db = getDB();
    $stmt = $db->prepare('SELECT id FROM utilisateurs WHERE reset_token=? AND reset_token_expires > NOW() AND statut IN ("actif","en_attente")');
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    if(!$user) jsonResponse(['ok'=>false,'msg'=>'Lien invalide ou expiré. Refaites une demande.'], 400);
    $db->prepare('UPDATE utilisateurs SET password=?, reset_token=NULL, reset_token_expires=NULL WHERE id=?')
       ->execute([password_hash($newPass, PASSWORD_BCRYPT), $user['id']]);
    jsonResponse(['ok'=>true,'msg'=>'Mot de passe réinitialisé ! Vous pouvez vous connecter.']);

default: jsonResponse(['error'=>'Action inconnue.'],400);
}
