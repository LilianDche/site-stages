<?php
require_once __DIR__.'/config.php';
header('Content-Type: application/json; charset=utf-8');
$db=getDB();

if(isset($_GET['id'])){
    $stmt=$db->prepare(
        'SELECT o.*,e.nom AS entreprise,e.ville AS entreprise_ville,e.secteur,
                e.site_web,e.email_contact,e.telephone,e.description AS entreprise_desc,e.nb_stagiaires,
                u.prenom AS pilote_prenom,u.nom AS pilote_nom,u.id AS pilote_id
         FROM offres o
         JOIN entreprises e ON e.id=o.entreprise_id
         LEFT JOIN utilisateurs u ON u.id=o.created_by AND u.role="pilote"
         WHERE o.id=? AND o.actif=1 AND o.statut_validation="validee"');
    $stmt->execute([(int)$_GET['id']]);
    $offre=$stmt->fetch();
    if(!$offre) jsonResponse(['error'=>'Introuvable'],404);
    $cs=$db->prepare('SELECT c.id,c.nom,c.categorie FROM competences c
                      JOIN offre_competences oc ON oc.competence_id=c.id WHERE oc.offre_id=?');
    $cs->execute([$offre['id']]);
    $offre['competences']=$cs->fetchAll();
    jsonResponse($offre);
}

$where=['o.actif=1','o.statut_validation="validee"']; $params=[];
if(!empty($_GET['type'])&&in_array($_GET['type'],['stage','alternance'])){ $where[]='o.type=?'; $params[]=$_GET['type']; }
if(!empty($_GET['lieu'])){ $where[]='o.lieu=?'; $params[]=$_GET['lieu']; }
if(!empty($_GET['duree'])){ $where[]='o.duree=?'; $params[]=$_GET['duree']; }
if(!empty($_GET['q'])){ $q='%'.$_GET['q'].'%'; $where[]='(o.titre LIKE ? OR o.description LIKE ? OR e.nom LIKE ?)'; $params=array_merge($params,[$q,$q,$q]); }
if(!empty($_GET['competence'])){ $where[]='EXISTS (SELECT 1 FROM offre_competences oc2 JOIN competences c2 ON c2.id=oc2.competence_id WHERE oc2.offre_id=o.id AND c2.nom=?)'; $params[]=$_GET['competence']; }
if(!empty($_GET['pilote_id'])){ $where[]='o.created_by=?'; $params[]=(int)$_GET['pilote_id']; }

$sql='SELECT o.*,e.nom AS entreprise,e.ville,e.secteur FROM offres o JOIN entreprises e ON e.id=o.entreprise_id WHERE '.implode(' AND ',$where).' ORDER BY o.created_at DESC';
$stmt=$db->prepare($sql); $stmt->execute($params);
$offres=$stmt->fetchAll();
$csStmt=$db->prepare('SELECT c.nom FROM competences c JOIN offre_competences oc ON oc.competence_id=c.id WHERE oc.offre_id=?');
foreach($offres as &$o){ $csStmt->execute([$o['id']]); $o['competences']=array_column($csStmt->fetchAll(),'nom'); }
jsonResponse($offres);
