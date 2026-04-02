<?php
require_once 'php/config.php';
$piloteId=(int)($_GET['id']??0);
if(!$piloteId){ header('Location: index.php'); exit; }

$db=getDB();
$stmt=$db->prepare(
  'SELECT u.id,u.prenom,u.nom,u.email,u.created_at,
          e.id AS entreprise_id,e.nom AS entreprise_nom,e.secteur,e.ville,e.code_postal,
          e.email_contact,e.telephone,e.site_web,e.description AS entreprise_desc,e.nb_stagiaires
   FROM utilisateurs u JOIN entreprises e ON e.id=u.entreprise_id
   WHERE u.id=? AND u.role="pilote" AND u.statut="actif"');
$stmt->execute([$piloteId]);
$pilote=$stmt->fetch();
if(!$pilote){ header('Location: index.php'); exit; }

$os=$db->prepare('SELECT o.*,e.nom AS entreprise FROM offres o JOIN entreprises e ON e.id=o.entreprise_id
                  WHERE o.created_by=? AND o.actif=1 AND o.statut_validation="validee" ORDER BY o.created_at DESC');
$os->execute([$piloteId]);
$offres=$os->fetchAll();

$csStmt=$db->prepare('SELECT c.nom FROM competences c JOIN offre_competences oc ON oc.competence_id=c.id WHERE oc.offre_id=?');
foreach($offres as &$o){ $csStmt->execute([$o['id']]); $o['competences']=array_column($csStmt->fetchAll(),'nom'); }
$initiales=strtoupper(substr($pilote['prenom'],0,1).substr($pilote['nom'],0,1));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?=e($pilote['prenom'].' '.$pilote['nom'])?> – Pilote Web4All</title>
  <meta name="description" content="Profil pilote <?=e($pilote['prenom'].' '.$pilote['nom'])?> – <?=e($pilote['entreprise_nom'])?>. <?=count($offres)?> offre(s) disponible(s).">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script>(function(){var t=localStorage.getItem("w4a_theme")||"dark";document.documentElement.setAttribute("data-theme",t);})();</script>
  <script>window.SITE_URL="<?= SITE_URL ?>";</script>
</head>
<body>
<header class="big-header">
  <h1><span class="accent">Web4All</span> – Profil Pilote</h1>
  <p class="header-sub">Découvrez les offres de ce pilote</p>
</header>
<nav class="navbar" id="navbar">
  <button class="burger-btn" id="burgerBtn" aria-label="Menu" aria-expanded="false">
    <span></span><span></span><span></span>
  </button>
  <div class="nav-links" id="navLinks">
    <a href="index.php">Accueil</a>
    <a href="entreprises.php">Entreprises</a>
    <a href="profil.php">Profil</a>
    <a href="mention_legales.php">Mentions légales</a>
  </div>
  <div class="nav-right" id="navRight">
    <button id="themeToggle" class="nav-btn" onclick="toggleTheme()" title="Changer le thème">🌸 Mode clair</button>
  </div>
</nav>
<?php include 'php/modal_auth.php'; ?>

<main class="container main-content">
  <div style="display:flex;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap">
  <a href="index.php" class="back-link" style="margin-bottom:0">← Retour aux offres</a>

</div>

  <!-- Carte pilote -->
  <div class="pilote-profile-header">
    <div class="pilote-avatar"><?=e($initiales)?></div>
    <div class="pilote-profile-info">
      <h1><?=e($pilote['prenom'].' '.$pilote['nom'])?></h1>
      <div class="pilote-entreprise-tag">🏢 <?=e($pilote['entreprise_nom'])?></div>
      <div class="pilote-meta">
        <?php if($pilote['secteur']): ?>
        <span class="pilote-meta-item">💼 <?=e($pilote['secteur'])?></span>
        <?php endif; ?>
        <?php if($pilote['ville']): ?>
        <span class="pilote-meta-item">📍 <?=e($pilote['ville'])?></span>
        <?php endif; ?>
        <?php if($pilote['email_contact']): ?>
        <span class="pilote-meta-item">✉️ <a href="mailto:<?=e($pilote['email_contact'])?>"><?=e($pilote['email_contact'])?></a></span>
        <?php endif; ?>
        <?php if($pilote['site_web']): ?>
        <span class="pilote-meta-item">🌐 <a href="<?=e($pilote['site_web'])?>" target="_blank" rel="noopener"><?=e(parse_url($pilote['site_web'],PHP_URL_HOST)?:$pilote['site_web'])?></a></span>
        <?php endif; ?>
        <?php if($pilote['nb_stagiaires']): ?>
        <span class="pilote-meta-item">👥 <?=(int)$pilote['nb_stagiaires']?> stagiaire(s) accueillis</span>
        <?php endif; ?>
      </div>
      <?php if($pilote['entreprise_desc']): ?>
      <p style="margin-top:.75rem;font-size:.88rem;color:var(--muted);max-width:600px;line-height:1.65"><?=e($pilote['entreprise_desc'])?></p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Offres publiées -->
  <h1 style="margin-bottom:1rem">
    Offres publiées
    <span style="font-size:1rem;color:var(--muted);font-weight:400">(<?=count($offres)?>)</span>
  </h1>

  <?php if(empty($offres)): ?>
  <div class="empty-state">
    <span class="empty-icon">📋</span>
    <p>Ce pilote n'a pas encore publié d'offres.</p>
  </div>
  <?php else: ?>
  <div class="offres">
    <?php foreach($offres as $o): ?>
    <article class="offre-card" onclick="window.location.href='offre.php?id=<?=(int)$o['id']?>'" style="cursor:pointer">
      <h2><?=e($o['titre'])?></h2>
      <p class="offre-entreprise"><?=e($o['entreprise']??'')?></p>
      <div class="offre-meta">
        <span class="badge <?=$o['type']==='stage'?'badge-stage':'badge-alt'?>"><?=$o['type']==='stage'?'🎓 Stage':'🔄 Alternance'?></span>
        <span class="badge badge-lieu">📍 <?=e($o['lieu'])?></span>
        <?php if($o['duree']): ?><span class="badge badge-duree">⏱ <?=e($o['duree'])?></span><?php endif; ?>
        <?php if($o['remuneration']): ?><span class="badge badge-remun">💶 <?=e($o['remuneration'])?></span><?php endif; ?>
      </div>
      <?php if(!empty($o['competences'])): ?>
      <div class="offre-competences">
        <?php foreach(array_slice($o['competences'],0,4) as $c): ?>
        <span class="tag-comp"><?=e($c)?></span>
        <?php endforeach; ?>
        <?php if(count($o['competences'])>4): ?><span class="tag-comp tag-more">+<?=count($o['competences'])-4?></span><?php endif; ?>
      </div>
      <?php endif; ?>
      <p class="offre-desc"><?=e(substr($o['description'],0,120))?>…</p>
      <a class="btn" href="offre.php?id=<?=(int)$o['id']?>">Voir l'offre →</a>
    </article>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</main>

<footer>
  <span class="footer-brand">Web4All</span>
  <span>© 2026 – Projet pédagogique</span>
  <a href="mention_legales.php">Mentions légales</a>
</footer>
<script src="js/script.js"></script>
</body>
</html>
