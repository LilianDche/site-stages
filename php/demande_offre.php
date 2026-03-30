<?php
// Pilote soumet une nouvelle offre → statut_validation = en_attente
require_once __DIR__.'/config.php';
header('Content-Type: application/json; charset=utf-8');
$user=currentUser();
if(!$user||!isPilote()) jsonResponse(['ok'=>false,'msg'=>'Accès refusé.'],403);
if($user['statut']!=='actif') jsonResponse(['ok'=>false,'msg'=>'Votre compte n\'est pas encore validé.'],403);

$data=json_decode(file_get_contents('php://input'),true)??$_POST;
$action=$data['action']??'soumettre';

$db=getDB();

if($action==='soumettre'){
    $titre      =trim($data['titre']      ??'');
    $description=trim($data['description']??'');
    $type       =     $data['type']       ??'';
    $lieu       =trim($data['lieu']       ??'');
    $duree      =trim($data['duree']      ??'');
    $remun      =trim($data['remuneration']??'');

    if(!$titre||!$description||!$type||!$lieu)
        jsonResponse(['ok'=>false,'msg'=>'Titre, description, type et lieu sont obligatoires.'],400);
    if(!in_array($type,['stage','alternance']))
        jsonResponse(['ok'=>false,'msg'=>'Type invalide.'],400);

    // Récupérer l'entreprise du pilote
    $entId=$user['entreprise_id'];
    if(!$entId) jsonResponse(['ok'=>false,'msg'=>'Aucune entreprise associée à votre compte.'],400);

    $db->prepare('INSERT INTO offres (titre,entreprise_id,lieu,type,description,duree,remuneration,actif,statut_validation,created_by) VALUES (?,?,?,?,?,?,?,1,"en_attente",?)')
       ->execute([$titre,$entId,$lieu,$type,$description,$duree?:null,$remun?:null,$user['id']]);
    $newId=$db->lastInsertId();

    // Compétences
    if(!empty($data['competences'])&&is_array($data['competences'])){
        $ins=$db->prepare('INSERT IGNORE INTO offre_competences VALUES (?,?)');
        foreach($data['competences'] as $cid) $ins->execute([$newId,(int)$cid]);
    }
    jsonResponse(['ok'=>true,'msg'=>'Demande soumise ! Un administrateur va la valider sous 48h.','id'=>$newId]);
}

if($action==='mes_demandes'){
    $stmt=$db->prepare(
        'SELECT o.*,e.nom AS entreprise_nom FROM offres o JOIN entreprises e ON e.id=o.entreprise_id
         WHERE o.created_by=? ORDER BY o.created_at DESC');
    $stmt->execute([$user['id']]);
    jsonResponse($stmt->fetchAll());
}

jsonResponse(['error'=>'Action inconnue.'],400);
