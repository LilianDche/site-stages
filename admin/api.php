<?php
// ============================================================
//  WEB4ALL – admin/api.php  (toutes actions admin)
// ============================================================
require_once __DIR__.'/../php/config.php';
header('Content-Type: application/json; charset=utf-8');
// Les pilotes peuvent accéder à certaines actions, les admins à tout
$user = currentUser();
if (!$user) jsonResponse(['ok'=>false,'msg'=>'Non connecté.'], 401);
if ($user['statut'] !== 'actif') jsonResponse(['ok'=>false,'msg'=>'Compte inactif.'], 403);

// Actions réservées aux admins uniquement
$adminOnly = ['liste_utilisateurs','changer_role','supprimer_utilisateur','changer_statut_utilisateur',
              'liste_competences','creer_competence','supprimer_competence',
              'liste_promotions','creer_promotion','supprimer_promotion',
              'comptes_attente','valider_compte','offres_attente','valider_offre',
              'export_candidatures_csv','liste_candidatures'];

// Lire le body une seule fois
$rawBody = file_get_contents('php://input');
$data    = json_decode($rawBody, true) ?? $_POST;
$action  = $data['action'] ?? $_GET['action'] ?? '';

if (in_array($action, $adminOnly) && !isAdmin()) {
    jsonResponse(['ok'=>false,'msg'=>'Accès réservé aux administrateurs.'], 403);
}
if (!isPilote()) {
    jsonResponse(['ok'=>false,'msg'=>'Accès refusé.'], 403);
}
$db     = getDB();

switch ($action) {

    // ── STATS ────────────────────────────────────────────────
    case 'stats':
        jsonResponse([
            'offres'              => $db->query('SELECT COUNT(*) FROM offres WHERE actif=1 AND statut_validation="validee"')->fetchColumn(),
            'entreprises'         => $db->query('SELECT COUNT(*) FROM entreprises')->fetchColumn(),
            'utilisateurs'        => $db->query('SELECT COUNT(*) FROM utilisateurs WHERE role="etudiant"')->fetchColumn(),
            'pilotes'             => $db->query('SELECT COUNT(*) FROM utilisateurs WHERE role="pilote" AND statut="actif"')->fetchColumn(),
            'candidatures'        => $db->query('SELECT COUNT(*) FROM candidatures')->fetchColumn(),
            'acceptees'           => $db->query('SELECT COUNT(*) FROM candidatures WHERE statut="acceptee"')->fetchColumn(),
            'en_attente_comptes'  => $db->query('SELECT COUNT(*) FROM utilisateurs WHERE statut="en_attente"')->fetchColumn(),
            'en_attente_offres'   => $db->query('SELECT COUNT(*) FROM offres WHERE statut_validation="en_attente"')->fetchColumn(),
        ]);
        break;

    // ── OFFRES ───────────────────────────────────────────────

    case 'stats_pilote':
        $pid = (int)currentUser()['id'];
        $s1=$db->prepare('SELECT COUNT(*) FROM offres WHERE created_by=? AND actif=1 AND statut_validation="validee"'); $s1->execute([$pid]);
        $s2=$db->prepare('SELECT COUNT(*) FROM candidatures c JOIN offres o ON o.id=c.offre_id WHERE o.created_by=?'); $s2->execute([$pid]);
        $s3=$db->prepare('SELECT COUNT(*) FROM offres WHERE created_by=? AND statut_validation="en_attente"'); $s3->execute([$pid]);
        jsonResponse(['offres'=>$s1->fetchColumn(),'candidatures'=>$s2->fetchColumn(),'en_attente'=>$s3->fetchColumn()]);
        break;

    case 'liste_offres_pilote':
        $pid = currentUser()['id'];
        $stmt = $db->prepare(
            'SELECT o.*, e.nom AS entreprise_nom, e.id AS eid
             FROM offres o
             JOIN entreprises e ON e.id=o.entreprise_id
             WHERE o.created_by=?
             ORDER BY o.created_at DESC'
        );
        $stmt->execute([$pid]);
        $rows = $stmt->fetchAll();
        $cs = $db->prepare('SELECT c.id,c.nom FROM competences c JOIN offre_competences oc ON oc.competence_id=c.id WHERE oc.offre_id=?');
        foreach ($rows as &$r) { $cs->execute([$r['id']]); $r['competences']=$cs->fetchAll(); }
        jsonResponse($rows);
        break;

    case 'liste_offres':
        $stmt = $db->query(
            'SELECT o.*, e.nom AS entreprise_nom, e.id AS eid
             FROM offres o
             JOIN entreprises e ON e.id=o.entreprise_id
             ORDER BY o.created_at DESC'
        );
        $rows = $stmt->fetchAll();
        $cs = $db->prepare('SELECT c.id,c.nom FROM competences c JOIN offre_competences oc ON oc.competence_id=c.id WHERE oc.offre_id=?');
        foreach ($rows as &$r) { $cs->execute([$r['id']]); $r['competences']=$cs->fetchAll(); }
        jsonResponse($rows);
        break;

    case 'creer_offre':
    case 'modifier_offre':
        $titre       = trim($data['titre']       ?? '');
        $entrepriseId= (int)($data['entreprise_id'] ?? 0);
        $lieu        = trim($data['lieu']        ?? '');
        $type        =      $data['type']        ?? '';
        $description = trim($data['description'] ?? '');
        if (!$titre)        jsonResponse(['ok'=>false,'msg'=>'Titre obligatoire.'],400);
        if (!$entrepriseId) jsonResponse(['ok'=>false,'msg'=>'Entreprise obligatoire.'],400);
        if (!$lieu)         jsonResponse(['ok'=>false,'msg'=>'Lieu obligatoire.'],400);
        if (!in_array($type,['stage','alternance'])) jsonResponse(['ok'=>false,'msg'=>'Type invalide.'],400);
        if (!$description)  jsonResponse(['ok'=>false,'msg'=>'Description obligatoire.'],400);

        if ($action === 'creer_offre') {
            $db->prepare('INSERT INTO offres (titre,entreprise_id,lieu,type,description,duree,remuneration,actif,statut_validation,created_by) VALUES (?,?,?,?,?,?,?,1,"validee",?)')
               ->execute([$titre,$entrepriseId,$lieu,$type,$description,$data['duree']?:null,$data['remuneration']?:null,currentUser()['id']]);
            $newId = $db->lastInsertId();
        } else {
            $newId = (int)($data['id'] ?? 0);
            if (!$newId) jsonResponse(['ok'=>false,'msg'=>'ID manquant.'],400);
            // Vérifier que l'utilisateur est le créateur de l'offre ou admin
            if (!isAdmin()) {
                $chk=$db->prepare('SELECT created_by FROM offres WHERE id=?');
                $chk->execute([$newId]);
                $row=$chk->fetch();
                if (!$row || (int)$row['created_by'] !== (int)currentUser()['id'])
                    jsonResponse(['ok'=>false,'msg'=>'Vous ne pouvez modifier que vos propres offres.'],403);
            }
            $db->prepare('UPDATE offres SET titre=?,entreprise_id=?,lieu=?,type=?,description=?,duree=?,remuneration=?,actif=? WHERE id=?')
               ->execute([$titre,$entrepriseId,$lieu,$type,$description,$data['duree']?:null,$data['remuneration']?:null,isset($data['actif'])?(int)$data['actif']:1,$newId]);
        }
        if (isset($data['competences'])&&is_array($data['competences'])) {
            $db->prepare('DELETE FROM offre_competences WHERE offre_id=?')->execute([$newId]);
            $ins=$db->prepare('INSERT IGNORE INTO offre_competences (offre_id,competence_id) VALUES (?,?)');
            foreach($data['competences'] as $cid) $ins->execute([$newId,(int)$cid]);
        }
        jsonResponse(['ok'=>true,'id'=>$newId,'msg'=>$action==='creer_offre'?'Offre créée.':'Offre modifiée.']);
        break;

    case 'supprimer_offre':
        $oid=(int)($data['id']??0);
        if (!isAdmin()) {
            $chk=$db->prepare('SELECT created_by FROM offres WHERE id=?'); $chk->execute([$oid]);
            $row=$chk->fetch();
            if (!$row || (int)$row['created_by'] !== (int)currentUser()['id'])
                jsonResponse(['ok'=>false,'msg'=>'Vous ne pouvez supprimer que vos propres offres.'], 403);
        }
        $db->prepare('DELETE FROM offres WHERE id=?')->execute([$oid]);
        jsonResponse(['ok'=>true]);
        break;

    // ── ENTREPRISES ──────────────────────────────────────────
    case 'liste_entreprises':
        $stmt=$db->query('SELECT e.*, COUNT(o.id) AS nb_offres FROM entreprises e LEFT JOIN offres o ON o.entreprise_id=e.id AND o.actif=1 GROUP BY e.id ORDER BY e.nom');
        jsonResponse($stmt->fetchAll());
        break;

    case 'creer_entreprise':
    case 'modifier_entreprise':
        $nom=trim($data['nom']??'');
        if (!$nom) jsonResponse(['ok'=>false,'msg'=>'Nom obligatoire.'],400);

        // Pour modifier : admin OU pilote lié à cette entreprise
        if ($action==='modifier_entreprise' && !isAdmin()) {
            $eid=(int)($data['id']??0);
            $u=currentUser();
            if ((int)($u['entreprise_id']??0) !== $eid)
                jsonResponse(['ok'=>false,'msg'=>'Vous ne pouvez modifier que votre propre entreprise.'],403);
        }

        $fields=['nom','secteur','adresse','ville','code_postal','pays','email_contact','telephone','site_web','description'];
        $vals=array_map(fn($f)=>trim($data[$f]??'')?:null,$fields);
        if ($action==='creer_entreprise') {
            $ph=implode(',',array_fill(0,count($fields),'?'));
            $db->prepare('INSERT INTO entreprises ('.implode(',',$fields).') VALUES ('.$ph.')')->execute($vals);
            jsonResponse(['ok'=>true,'id'=>$db->lastInsertId(),'msg'=>'Entreprise créée.']);
        } else {
            $id=(int)($data['id']??0);
            $set=implode(',',array_map(fn($f)=>"$f=?",$fields));
            $db->prepare("UPDATE entreprises SET $set WHERE id=?")->execute([...$vals,$id]);
            jsonResponse(['ok'=>true,'msg'=>'Entreprise mise à jour.']);
        }
        break;

    case 'supprimer_entreprise':
        if (!isAdmin()) {
            $eid=(int)($data['id']??0);
            $u=currentUser();
            if ((int)($u['entreprise_id']??0) !== $eid)
                jsonResponse(['ok'=>false,'msg'=>'Vous ne pouvez supprimer que votre propre entreprise.'],403);
        }
        $db->prepare('DELETE FROM entreprises WHERE id=?')->execute([(int)($data['id']??0)]);
        jsonResponse(['ok'=>true]);
        break;

    // ── PROMOTIONS ───────────────────────────────────────────
    case 'liste_promotions':
        $stmt=$db->query('SELECT p.*,COUNT(u.id) AS nb_etudiants FROM promotions p LEFT JOIN utilisateurs u ON u.promotion_id=p.id AND u.role="etudiant" GROUP BY p.id ORDER BY p.annee DESC');
        jsonResponse($stmt->fetchAll());
        break;

    case 'creer_promotion':
        $nom=trim($data['nom']??'');
        if (!$nom) jsonResponse(['ok'=>false,'msg'=>'Nom obligatoire.'],400);
        $db->prepare('INSERT INTO promotions (nom,annee,description) VALUES (?,?,?)')->execute([$nom,(int)($data['annee']??date('Y')),$data['description']?:null]);
        jsonResponse(['ok'=>true,'id'=>$db->lastInsertId(),'msg'=>'Promotion créée.']);
        break;

    case 'supprimer_promotion':
        $db->prepare('DELETE FROM promotions WHERE id=?')->execute([(int)($data['id']??0)]);
        jsonResponse(['ok'=>true]);
        break;

    // ── UTILISATEURS ─────────────────────────────────────────
    case 'liste_utilisateurs':
        $stmt=$db->query(
            'SELECT u.id,u.prenom,u.nom,u.email,u.role,u.statut,u.created_at,
                    u.nom_entreprise_demande,
                    p.nom AS promo_nom,
                    e.nom AS entreprise_nom,
                    (SELECT COUNT(*) FROM candidatures WHERE utilisateur_id=u.id) AS nb_cands
             FROM utilisateurs u
             LEFT JOIN promotions  p ON p.id=u.promotion_id
             LEFT JOIN entreprises e ON e.id=u.entreprise_id
             ORDER BY u.role, u.nom'
        );
        jsonResponse($stmt->fetchAll());
        break;

    case 'changer_role':
        $id=(int)($data['id']??0); $role=$data['role']??'';
        if (!in_array($role,['etudiant','pilote','admin'])) jsonResponse(['ok'=>false,'msg'=>'Rôle invalide.'],400);
        $db->prepare('UPDATE utilisateurs SET role=? WHERE id=?')->execute([$role,$id]);
        jsonResponse(['ok'=>true,'msg'=>'Rôle mis à jour.']);
        break;

    case 'supprimer_utilisateur':
        $db->prepare('DELETE FROM utilisateurs WHERE id=? AND role!="admin"')->execute([(int)($data['id']??0)]);
        jsonResponse(['ok'=>true]);
        break;

    case 'changer_statut_utilisateur':
        $id=(int)($data['id']??0); $statut=$data['statut']??'';
        if (!in_array($statut,['actif','suspendu'])) jsonResponse(['ok'=>false,'msg'=>'Statut invalide.'],400);
        $db->prepare('UPDATE utilisateurs SET statut=? WHERE id=? AND role!="admin"')->execute([$statut,$id]);
        jsonResponse(['ok'=>true,'msg'=>'Statut mis à jour.']);
        break;

    // ── COMPÉTENCES ──────────────────────────────────────────
    case 'liste_competences':
        jsonResponse($db->query('SELECT * FROM competences ORDER BY categorie,nom')->fetchAll());
        break;

    case 'creer_competence':
        $nom=trim($data['nom']??''); $cat=trim($data['categorie']??'');
        if (!$nom) jsonResponse(['ok'=>false,'msg'=>'Nom obligatoire.'],400);
        $db->prepare('INSERT IGNORE INTO competences (nom,categorie) VALUES (?,?)')->execute([$nom,$cat?:null]);
        jsonResponse(['ok'=>true,'msg'=>'Compétence ajoutée.']);
        break;

    case 'supprimer_competence':
        $db->prepare('DELETE FROM competences WHERE id=?')->execute([(int)($data['id']??0)]);
        jsonResponse(['ok'=>true]);
        break;

    // ── CANDIDATURES ─────────────────────────────────────────

    case 'liste_candidatures_pilote':
        $pid = (int)currentUser()['id'];
        $stmt = $db->prepare(
            'SELECT c.id, c.statut, c.created_at, c.message, c.cv_path, c.lm_path,
                    u.prenom, u.nom, u.email,
                    o.id AS offre_id, o.titre AS offre_titre, o.lieu, o.type,
                    e.nom AS entreprise
             FROM candidatures c
             JOIN utilisateurs u ON u.id = c.utilisateur_id
             JOIN offres o       ON o.id = c.offre_id
             JOIN entreprises e  ON e.id = o.entreprise_id
             WHERE o.created_by = ?
             ORDER BY c.created_at DESC'
        );
        $stmt->execute([$pid]);
        jsonResponse($stmt->fetchAll());
        break;

    case 'liste_candidatures':
        $stmt=$db->query(
            'SELECT c.id,c.statut,c.created_at,c.message,c.cv_path,c.lm_path,
                    c.utilisateur_id,u.prenom,u.nom,u.email,
                    o.id AS offre_id,o.titre AS offre_titre,o.lieu,o.type,o.created_by AS offre_created_by,
                    e.nom AS entreprise
             FROM candidatures c
             JOIN utilisateurs u ON u.id=c.utilisateur_id
             JOIN offres o       ON o.id=c.offre_id
             JOIN entreprises e  ON e.id=o.entreprise_id
             ORDER BY c.created_at DESC'
        );
        jsonResponse($stmt->fetchAll());
        break;

    case 'statut_candidature':
        $id=(int)($data['id']??0); $statut=$data['statut']??'';
        if (!in_array($statut,['envoyee','vue','acceptee','refusee'])) jsonResponse(['ok'=>false,'msg'=>'Statut invalide.'],400);
        // Vérifier que l'utilisateur est l'auteur de l'offre ou admin
        if (!isAdmin()) {
            $chk=$db->prepare('SELECT o.created_by FROM candidatures c JOIN offres o ON o.id=c.offre_id WHERE c.id=?');
            $chk->execute([$id]);
            $row=$chk->fetch();
            if (!$row || (int)$row['created_by'] !== (int)currentUser()['id'])
                jsonResponse(['ok'=>false,'msg'=>'Vous ne pouvez gérer que les candidatures de vos offres.'], 403);
        }
        $db->prepare('UPDATE candidatures SET statut=? WHERE id=?')->execute([$statut,$id]);
        jsonResponse(['ok'=>true]);
        break;

    // ── VALIDATIONS ──────────────────────────────────────────
    case 'comptes_attente':
        $stmt=$db->query(
            'SELECT u.id,u.prenom,u.nom,u.email,u.role,u.statut,u.created_at,
                    u.nom_entreprise_demande, e.nom AS entreprise_nom
             FROM utilisateurs u
             LEFT JOIN entreprises e ON e.id=u.entreprise_id
             WHERE u.statut="en_attente" ORDER BY u.created_at DESC'
        );
        jsonResponse($stmt->fetchAll());
        break;

    case 'valider_compte':
        $id=(int)($data['id']??0); $statut=$data['statut']??'actif';
        if (!in_array($statut,['actif','suspendu'])) jsonResponse(['ok'=>false,'msg'=>'Statut invalide.'],400);
        if ($statut==='actif') {
            $uS=$db->prepare('SELECT * FROM utilisateurs WHERE id=?'); $uS->execute([$id]);
            $u=$uS->fetch();
            if ($u && $u['role']==='pilote' && empty($u['entreprise_id']) && !empty($u['nom_entreprise_demande'])) {
                $db->prepare('INSERT INTO entreprises (nom) VALUES (?)')->execute([$u['nom_entreprise_demande']]);
                $entId=$db->lastInsertId();
                $db->prepare('UPDATE utilisateurs SET entreprise_id=?,statut="actif" WHERE id=?')->execute([$entId,$id]);
                jsonResponse(['ok'=>true,'msg'=>"Compte activé. Entreprise \"{$u['nom_entreprise_demande']}\" créée."]);
            }
        }
        $db->prepare('UPDATE utilisateurs SET statut=? WHERE id=?')->execute([$statut,$id]);
        jsonResponse(['ok'=>true,'msg'=>$statut==='actif'?'Compte activé.':'Compte refusé.']);
        break;

    case 'offres_attente':
        $stmt=$db->query(
            'SELECT o.*,e.nom AS entreprise_nom,
                    u.prenom AS pilote_prenom,u.nom AS pilote_nom
             FROM offres o
             JOIN entreprises e ON e.id=o.entreprise_id
             LEFT JOIN utilisateurs u ON u.id=o.created_by
             WHERE o.statut_validation="en_attente" ORDER BY o.created_at DESC'
        );
        jsonResponse($stmt->fetchAll());
        break;

    case 'valider_offre':
        $id=(int)($data['id']??0); $statut=$data['statut']??'';
        if (!in_array($statut,['validee','refusee'])) jsonResponse(['ok'=>false,'msg'=>'Statut invalide.'],400);
        $db->prepare('UPDATE offres SET statut_validation=? WHERE id=?')->execute([$statut,$id]);
        jsonResponse(['ok'=>true,'msg'=>$statut==='validee'?'Offre publiée.':'Offre refusée.']);
        break;


    case 'export_candidatures_csv':
        // Pas de header JSON pour ce cas : on retourne du CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="candidatures_'.date('Y-m-d').'.csv"');
        $stmt=$db->query(
            'SELECT c.id,u.prenom,u.nom,u.email,o.titre AS offre,e.nom AS entreprise,o.lieu,o.type,c.statut,c.created_at
             FROM candidatures c
             JOIN utilisateurs u ON u.id=c.utilisateur_id
             JOIN offres o       ON o.id=c.offre_id
             JOIN entreprises e  ON e.id=o.entreprise_id
             ORDER BY c.created_at DESC'
        );
        $rows=$stmt->fetchAll();
        $out=fopen('php://output','w');
        // BOM UTF-8 pour Excel
        fputs($out, "\xEF\xBB\xBF");
        fputcsv($out,['ID','Prénom','Nom','Email','Offre','Entreprise','Lieu','Type','Statut','Date'],';');
        foreach($rows as $r) fputcsv($out,array_values($r),';');
        fclose($out);
        exit;

    default:
        jsonResponse(['error'=>"Action inconnue : $action."],400);
        break;
}
