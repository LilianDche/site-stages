<?php
require_once 'php/config.php';
$entId = (int)($_GET['id'] ?? 0);
if (!$entId) { header('Location: entreprises.php'); exit; }

$db   = getDB();
$stmt = $db->prepare('SELECT * FROM entreprises WHERE id=?');
$stmt->execute([$entId]);
$ent  = $stmt->fetch();
if (!$ent) { header('Location: entreprises.php'); exit; }

// Offres actives de cette entreprise
$stmt2 = $db->prepare(
    'SELECT o.*,u.prenom AS pilote_prenom,u.nom AS pilote_nom,u.id AS pilote_id
     FROM offres o
     LEFT JOIN utilisateurs u ON u.id=o.created_by AND u.role="pilote"
     WHERE o.entreprise_id=? AND o.actif=1 AND o.statut_validation="validee"
     ORDER BY o.created_at DESC'
);
$stmt2->execute([$entId]);
$offres = $stmt2->fetchAll();

// Compétences par offre
$csStmt = $db->prepare('SELECT c.nom FROM competences c JOIN offre_competences oc ON oc.competence_id=c.id WHERE oc.offre_id=?');
foreach ($offres as &$o) { $csStmt->execute([$o['id']]); $o['competences'] = array_column($csStmt->fetchAll(),'nom'); }

// Pilotes liés à cette entreprise
$stmt3 = $db->prepare('SELECT id,prenom,nom FROM utilisateurs WHERE entreprise_id=? AND role="pilote" AND statut="actif"');
$stmt3->execute([$entId]);
$pilotes = $stmt3->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($ent['nom']) ?> – Web4All</title>
  <meta name="description" content="<?= e($ent['nom']) ?> recrute <?= count($offres) ?> stagiaire(s)/alternant(s). <?= e(substr($ent['description']??'',0,120)) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script>(function(){var t=localStorage.getItem("w4a_theme")||"dark";document.documentElement.setAttribute("data-theme",t);})();</script>
  <script>window.SITE_URL="<?= SITE_URL ?>";</script>
</head>
<body>

<header class="big-header">
  <h1><span class="accent"><?= e($ent['nom']) ?></span></h1>
  <p class="header-sub"><?= e($ent['secteur'] ?? '') ?><?= $ent['ville'] ? ' · '.e($ent['ville']) : '' ?></p>
</header>

<nav class="navbar" id="navbar">
  <button class="burger-btn" id="burgerBtn" aria-label="Menu" aria-expanded="false">
    <span></span><span></span><span></span>
  </button>
  <div class="nav-links" id="navLinks">
    <a href="index.php">Accueil</a>
    <a href="entreprises.php" id="on">Entreprises</a>
    <a href="profil.php">Profil</a>
    <a href="mention_legales.php">Mentions légales</a>
  </div>
  <div class="nav-right" id="navRight">
    <button id="themeToggle" class="nav-btn" onclick="toggleTheme()" title="Changer le thème">🌸 Mode clair</button>
  </div>
</nav>

<?php include 'php/modal_auth.php'; ?>

<main class="container main-content">
  <a href="entreprises.php" class="back-link">← Retour aux entreprises</a>

  <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start;margin-bottom:2rem">
    <!-- Infos principale -->
    <div class="form-card" style="max-width:100%">
      <p class="form-title"><?= e($ent['nom']) ?></p>
      <?php if($ent['description']): ?>
      <p style="color:var(--muted);font-size:.9rem;line-height:1.7;margin-bottom:1rem"><?= e($ent['description']) ?></p>
      <?php endif; ?>
      <div style="display:flex;gap:.5rem;flex-wrap:wrap">
        <?php if($ent['secteur']): ?><span class="badge badge-lieu">💼 <?= e($ent['secteur']) ?></span><?php endif; ?>
        <?php if($ent['ville']): ?><span class="badge badge-lieu">📍 <?= e($ent['ville']) ?> <?= e($ent['code_postal']??'') ?></span><?php endif; ?>
        <?php if($ent['nb_stagiaires']): ?><span class="badge badge-duree">👥 <?= (int)$ent['nb_stagiaires'] ?> stagiaire(s) accueillis</span><?php endif; ?>
      </div>
    </div>

    <!-- Sidebar contact -->
    <div class="sidebar-card" style="background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);padding:1.5rem">
      <h3 style="font-family:'Outfit',sans-serif;font-size:.85rem;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:1rem">Contact</h3>
      <?php if($ent['email_contact']): ?>
      <div style="display:flex;gap:.5rem;margin-bottom:.7rem;font-size:.85rem">
        <span>✉️</span><a href="mailto:<?= e($ent['email_contact']) ?>" style="color:var(--amber)"><?= e($ent['email_contact']) ?></a>
      </div>
      <?php endif; ?>
      <?php if($ent['telephone']): ?>
      <div style="display:flex;gap:.5rem;margin-bottom:.7rem;font-size:.85rem">
        <span>📞</span><span style="color:var(--muted)"><?= e($ent['telephone']) ?></span>
      </div>
      <?php endif; ?>
      <?php if($ent['site_web']): ?>
      <div style="display:flex;gap:.5rem;margin-bottom:.7rem;font-size:.85rem">
        <span>🌐</span><a href="<?= e($ent['site_web']) ?>" target="_blank" rel="noopener" style="color:var(--amber)"><?= e(parse_url($ent['site_web'],PHP_URL_HOST)?:$ent['site_web']) ?></a>
      </div>
      <?php endif; ?>
      <?php if($pilotes): ?>
      <div style="border-top:1px solid var(--border);margin-top:1rem;padding-top:1rem">
        <p style="font-size:.75rem;color:var(--muted);margin-bottom:.5rem;text-transform:uppercase;letter-spacing:.5px">Pilotes</p>
        <?php foreach($pilotes as $p): ?>
        <a href="pilote_profil.php?id=<?= (int)$p['id'] ?>" style="display:block;color:var(--amber);font-size:.85rem;margin-bottom:.3rem">
          👤 <?= e($p['prenom'].' '.$p['nom']) ?>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Offres -->
  <h1 style="margin-bottom:1rem">
    Offres disponibles
    <span style="font-size:1rem;color:var(--muted);font-weight:400">(<?= count($offres) ?>)</span>
  </h1>

  <?php if(empty($offres)): ?>
  <div class="empty-state"><span class="empty-icon">📋</span><p>Aucune offre active pour le moment.</p></div>
  <?php else: ?>
  <div class="offres">
    <?php foreach($offres as $o): ?>
    <article class="offre-card" onclick="window.location.href='offre.php?id=<?= (int)$o['id'] ?>'" style="cursor:pointer">
      <h2><?= e($o['titre']) ?></h2>
      <div class="offre-meta">
        <span class="badge <?= $o['type']==='stage'?'badge-stage':'badge-alt' ?>"><?= $o['type']==='stage'?'🎓 Stage':'🔄 Alternance' ?></span>
        <span class="badge badge-lieu">📍 <?= e($o['lieu']) ?></span>
        <?php if($o['duree']): ?><span class="badge badge-duree">⏱ <?= e($o['duree']) ?></span><?php endif; ?>
        <?php if($o['remuneration']): ?><span class="badge badge-remun">💶 <?= e($o['remuneration']) ?></span><?php endif; ?>
      </div>
      <?php if(!empty($o['competences'])): ?>
      <div class="offre-competences">
        <?php foreach(array_slice($o['competences'],0,4) as $c): ?><span class="tag-comp"><?= e($c) ?></span><?php endforeach; ?>
        <?php if(count($o['competences'])>4): ?><span class="tag-comp tag-more">+<?= count($o['competences'])-4 ?></span><?php endif; ?>
      </div>
      <?php endif; ?>
      <p class="offre-desc"><?= e(substr($o['description'],0,120)) ?>…</p>
      <a class="btn" href="offre.php?id=<?= (int)$o['id'] ?>" onclick="event.stopPropagation()">Voir l'offre →</a>
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
