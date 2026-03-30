<?php
// API publique pour les profils pilotes
require_once __DIR__.'/config.php';
header('Content-Type: application/json; charset=utf-8');
$db=getDB();
$action=$_GET['action']??'liste';

switch($action){
case 'liste':
    $stmt=$db->query(
        'SELECT u.id,u.prenom,u.nom,u.email,u.created_at,
                e.id AS entreprise_id,e.nom AS entreprise_nom,e.secteur,e.ville,e.site_web,e.description AS entreprise_desc,
                COUNT(DISTINCT o.id) AS nb_offres
         FROM utilisateurs u
         JOIN entreprises e ON e.id=u.entreprise_id
         LEFT JOIN offres o ON o.created_by=u.id AND o.actif=1 AND o.statut_validation="validee"
         WHERE u.role="pilote" AND u.statut="actif"
         GROUP BY u.id ORDER BY u.nom');
    jsonResponse($stmt->fetchAll());
    break;

case 'profil':
    $id=(int)($_GET['id']??0);
    $stmt=$db->prepare(
        'SELECT u.id,u.prenom,u.nom,u.email,u.created_at,
                e.id AS entreprise_id,e.nom AS entreprise_nom,e.secteur,e.ville,e.code_postal,
                e.email_contact,e.telephone,e.site_web,e.description AS entreprise_desc,e.nb_stagiaires
         FROM utilisateurs u
         JOIN entreprises e ON e.id=u.entreprise_id
         WHERE u.id=? AND u.role="pilote" AND u.statut="actif"');
    $stmt->execute([$id]);
    $pilote=$stmt->fetch();
    if(!$pilote) jsonResponse(['error'=>'Pilote introuvable'],404);

    // Offres publiées par ce pilote
    $os=$db->prepare(
        'SELECT o.*,e.nom AS entreprise FROM offres o JOIN entreprises e ON e.id=o.entreprise_id
         WHERE o.created_by=? AND o.actif=1 AND o.statut_validation="validee" ORDER BY o.created_at DESC');
    $os->execute([$id]);
    $offres=$os->fetchAll();
    $csStmt=$db->prepare('SELECT c.nom FROM competences c JOIN offre_competences oc ON oc.competence_id=c.id WHERE oc.offre_id=?');
    foreach($offres as &$o){ $csStmt->execute([$o['id']]); $o['competences']=array_column($csStmt->fetchAll(),'nom'); }
    $pilote['offres']=$offres;
    jsonResponse($pilote);
    break;

default: jsonResponse(['error'=>'Action inconnue.'],400);
}
