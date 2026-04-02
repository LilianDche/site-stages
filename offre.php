<?php
require_once 'php/config.php';

// Récupérer l'offre depuis la BDD
$offreId = (int)($_GET['id'] ?? 0);
if (!$offreId) { header('Location: index.php'); exit; }

$db   = getDB();
$stmt = $db->prepare(
    'SELECT o.*, e.nom AS entreprise_nom, e.secteur, e.ville AS entreprise_ville,
            e.email_contact, e.telephone, e.site_web, e.description AS entreprise_desc,
            e.nb_stagiaires
     FROM offres o
     JOIN entreprises e ON e.id = o.entreprise_id
     WHERE o.id = ? AND o.actif = 1'
);
$stmt->execute([$offreId]);
$offre = $stmt->fetch();
if (!$offre) { header('Location: index.php'); exit; }

// Compétences de l'offre
$cs = $db->prepare(
    'SELECT c.nom, c.categorie FROM competences c
     JOIN offre_competences oc ON oc.competence_id = c.id
     WHERE oc.offre_id = ? ORDER BY c.categorie, c.nom'
);
$cs->execute([$offreId]);
$competences = $cs->fetchAll();

// Déjà postulé ?
$dejaPostule = false;
$user = currentUser();
if ($user && isEtudiant()) {
    $stmt2 = $db->prepare('SELECT id FROM candidatures WHERE utilisateur_id=? AND offre_id=?');
    $stmt2->execute([$user['id'], $offreId]);
    $dejaPostule = (bool)$stmt2->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($offre['titre']) ?> – Web4All</title>
  <meta name="description" content="<?= e(substr($offre['description'],0,160)) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script>(function(){var t=localStorage.getItem("w4a_theme")||"dark";document.documentElement.setAttribute("data-theme",t);})();</script>
  <script>window.SITE_URL="<?= SITE_URL ?>";</script>
</head>
<body>

<header class="big-header">
  <h1><span class="accent">Web4All</span> – Stages & Alternances</h1>
  <p class="header-sub">Détail de l'offre</p>
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

  <a href="index.php" class="back-link">← Retour aux offres</a>

  <div class="offre-detail-layout">

    <!-- ── COLONNE PRINCIPALE ─────────────────────────────── -->
    <div class="detail-main">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap">
        <h1 style="margin-bottom:0"><?= e($offre['titre']) ?></h1>
        <button class="btn btn-ghost" id="favBtn" onclick="toggleFavoriDetail()" style="flex-shrink:0;margin-top:.25rem">☆ Favori</button>
      </div>
      <p class="detail-entreprise-name">🏢 <a href="entreprise.php?id=<?= (int)$offre['entreprise_id'] ?>" style="color:var(--amber)"><?= e($offre['entreprise_nom']) ?></a></p>

      <div class="detail-meta">
        <span class="badge <?= $offre['type']==='stage' ? 'badge-stage' : 'badge-alt' ?>">
          <?= $offre['type']==='stage' ? '🎓 Stage' : '🔄 Alternance' ?>
        </span>
        <span class="badge badge-lieu">📍 <?= e($offre['lieu']) ?></span>
        <?php if($offre['duree']):  ?><span class="badge badge-duree">⏱ <?= e($offre['duree'])  ?></span><?php endif; ?>
        <?php if($offre['remuneration']): ?><span class="badge badge-remun">💶 <?= e($offre['remuneration']) ?></span><?php endif; ?>
      </div>

      <!-- Description -->
      <div class="detail-section">
        <h2>Description du poste</h2>
        <p><?= e($offre['description']) ?></p>
      </div>

      <!-- Compétences -->
      <?php if($competences): ?>
      <?php
        $grouped = [];
        foreach ($competences as $c) {
          $grouped[$c['categorie'] ?: 'Autre'][] = $c['nom'];
        }
      ?>
      <div class="detail-section">
        <h2>Compétences requises</h2>
        <div class="comp-groups">
          <?php foreach($grouped as $cat => $noms): ?>
          <div>
            <p class="comp-group-label"><?= e($cat) ?></p>
            <div class="comp-tags">
              <?php foreach($noms as $nom): ?>
              <span class="tag-comp"><?= e($nom) ?></span>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Entreprise -->
      <?php if($offre['entreprise_desc']): ?>
      <div class="detail-section">
        <h2>À propos de l'entreprise</h2>
        <p><?= e($offre['entreprise_desc']) ?></p>
      </div>
      <?php endif; ?>
    </div>

    <!-- ── SIDEBAR ─────────────────────────────────────────── -->
    <aside class="detail-sidebar">

      <!-- Card postuler / déjà postulé -->
      <div class="postuler-card">
        <h3>Postuler à cette offre</h3>
        <?php if($dejaPostule): ?>
          <div class="deja-postule">✓ Vous avez déjà postulé à cette offre.</div>
          <p style="margin-top:.75rem;font-size:.8rem;color:var(--muted)">
            Retrouvez votre candidature dans <a href="profil.php" style="color:var(--amber)">votre profil</a>.
          </p>
        <?php elseif($user && !isEtudiant()): ?>
          <p>Seuls les étudiants peuvent postuler aux offres.</p>
        <?php else: ?>
          <p>Envoyez votre candidature avec votre CV et lettre de motivation.</p>

          <!-- Formulaire candidature -->
          <div class="form-alert error" id="formError"></div>
          <div class="form-alert success" id="formSuccess"></div>

          <?php if(!$user): ?>
            <button class="btn btn-primary btn-full" onclick="openModal('login')">Se connecter pour postuler</button>
          <?php else: ?>
            <div class="form-group" style="margin-bottom:.75rem">
              <label>Message de motivation *</label>
              <textarea id="message" rows="4" placeholder="Décrivez votre motivation…"></textarea>
            </div>

            <p class="upload-section-label">CV * <span style="color:var(--muted);text-transform:none;font-weight:400">(PDF)</span></p>
            <div class="upload-zone" id="zoneCV">
              <input type="file" id="fileCV" accept=".pdf,application/pdf">
              <div class="upload-icon">📄</div>
              <div class="upload-label"><strong>Cliquez</strong> ou glissez votre CV ici</div>
              <div class="upload-name" id="namecv"></div>
            </div>
            <p class="upload-size-hint">PDF uniquement · max 2 Mo</p>

            <p class="upload-section-label">Lettre de motivation <span style="color:var(--muted);text-transform:none;font-weight:400">(PDF, optionnel)</span></p>
            <div class="upload-zone" id="zoneLM">
              <input type="file" id="fileLM" accept=".pdf,application/pdf">
              <div class="upload-icon">✉️</div>
              <div class="upload-label"><strong>Cliquez</strong> ou glissez votre LM ici</div>
              <div class="upload-name" id="namelm"></div>
            </div>
            <p class="upload-size-hint">PDF uniquement · max 2 Mo</p>

            <button class="btn btn-primary btn-full" id="submitBtn">Envoyer ma candidature →</button>
          <?php endif; ?>
        <?php endif; ?>
      </div>

      <!-- Infos entreprise -->
      <div class="sidebar-card">
        <h3>L'entreprise</h3>
        <div class="info-row">
          <span class="icon">🏢</span>
          <div><div class="label">Nom</div><div class="val"><?= e($offre['entreprise_nom']) ?></div></div>
        </div>
        <?php if($offre['secteur']): ?>
        <div class="info-row">
          <span class="icon">💼</span>
          <div><div class="label">Secteur</div><div class="val"><?= e($offre['secteur']) ?></div></div>
        </div>
        <?php endif; ?>
        <?php if($offre['entreprise_ville']): ?>
        <div class="info-row">
          <span class="icon">📍</span>
          <div><div class="label">Ville</div><div class="val"><?= e($offre['entreprise_ville']) ?></div></div>
        </div>
        <?php endif; ?>
        <?php if($offre['nb_stagiaires']): ?>
        <div class="info-row">
          <span class="icon">👥</span>
          <div><div class="label">Stagiaires accueillis</div><div class="val"><?= (int)$offre['nb_stagiaires'] ?> au total</div></div>
        </div>
        <?php endif; ?>
        <?php if($offre['email_contact']): ?>
        <div class="info-row">
          <span class="icon">✉️</span>
          <div><div class="label">Contact</div><div class="val"><a href="mailto:<?= e($offre['email_contact']) ?>" style="color:var(--amber)"><?= e($offre['email_contact']) ?></a></div></div>
        </div>
        <?php endif; ?>
        <?php if($offre['site_web']): ?>
        <div class="info-row">
          <span class="icon">🌐</span>
          <div><div class="label">Site web</div><div class="val"><a href="<?= e($offre['site_web']) ?>" target="_blank" rel="noopener" style="color:var(--amber)"><?= e(parse_url($offre['site_web'], PHP_URL_HOST) ?: $offre['site_web']) ?></a></div></div>
        </div>
        <?php endif; ?>
      </div>

      <!-- Infos offre -->
      <div class="sidebar-card">
        <h3>Détails de l'offre</h3>
        <div class="info-row">
          <span class="icon"><?= $offre['type']==='stage' ? '🎓' : '🔄' ?></span>
          <div><div class="label">Type</div><div class="val"><?= ucfirst(e($offre['type'])) ?></div></div>
        </div>
        <?php if($offre['duree']): ?>
        <div class="info-row">
          <span class="icon">⏱</span>
          <div><div class="label">Durée</div><div class="val"><?= e($offre['duree']) ?></div></div>
        </div>
        <?php endif; ?>
        <?php if($offre['remuneration']): ?>
        <div class="info-row">
          <span class="icon">💶</span>
          <div><div class="label">Rémunération</div><div class="val"><?= e($offre['remuneration']) ?></div></div>
        </div>
        <?php endif; ?>
        <div class="info-row">
          <span class="icon">📅</span>
          <div><div class="label">Publiée le</div><div class="val"><?= date('d/m/Y', strtotime($offre['created_at'])) ?></div></div>
        </div>
      </div>

    </aside>
  </div>
</main>

<footer>
  <span class="footer-brand">Web4All</span>
  <span>© 2026 – Projet pédagogique</span>
  <a href="mention_legales.php">Mentions légales</a>
</footer>

<script src="js/script.js"></script>
<script>
const OFFRE_ID = <?= $offreId ?>;
const MAX_SIZE = 2 * 1024 * 1024; // 2 Mo

// ── UPLOAD ZONES ──────────────────────────────────────────────
function setupZone(zoneId, inputId, nameId) {
  const zone  = document.getElementById(zoneId);
  const input = document.getElementById(inputId);
  const name  = document.getElementById(nameId);
  if (!zone || !input) return;

  function setFile(file) {
    if (!file) return;
    if (file.size > MAX_SIZE) {
      showFormAlert('Le fichier "'+file.name+'" dépasse 2 Mo.', 'error');
      input.value = '';
      zone.classList.remove('has-file');
      return;
    }
    if (file.type !== 'application/pdf') {
      showFormAlert('Seuls les fichiers PDF sont acceptés.', 'error');
      input.value = '';
      zone.classList.remove('has-file');
      return;
    }
    name.textContent = '✓ ' + file.name + ' (' + (file.size/1024).toFixed(0) + ' Ko)';
    zone.classList.add('has-file');
  }

  input.addEventListener('change', () => setFile(input.files[0]));

  zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
  zone.addEventListener('drop', e => {
    e.preventDefault(); zone.classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (file) { input.files = e.dataTransfer.files; setFile(file); }
  });
}

setupZone('zoneCV', 'fileCV', 'namecv');
setupZone('zoneLM', 'fileLM', 'namelm');

// ── SOUMISSION ────────────────────────────────────────────────
function showFormAlert(msg, type) {
  const errEl = document.getElementById('formError');
  const okEl  = document.getElementById('formSuccess');
  if (type === 'error') {
    errEl.textContent = msg; errEl.className = 'form-alert error show';
    okEl.className = 'form-alert success';
  } else {
    okEl.textContent = msg; okEl.className = 'form-alert success show';
    errEl.className = 'form-alert error';
  }
}

document.getElementById('submitBtn')?.addEventListener('click', async () => {
  const message = document.getElementById('message')?.value.trim();
  const cvFile  = document.getElementById('fileCV')?.files[0];
  const lmFile  = document.getElementById('fileLM')?.files[0];
  const btn     = document.getElementById('submitBtn');

  if (!message) { showFormAlert('Le message de motivation est obligatoire.', 'error'); return; }
  if (!cvFile)  { showFormAlert('Le CV est obligatoire (PDF, max 2 Mo).', 'error'); return; }

  btn.disabled = true; btn.textContent = 'Envoi en cours…';

  try {
    // 1. Enregistrer la candidature
    const res = await apiPost('php/candidatures.php', {
      action: 'postuler', offre_id: OFFRE_ID, message
    });

    if (!res.ok) {
      showFormAlert(res.msg || 'Erreur lors de la candidature.', 'error');
      btn.disabled = false; btn.textContent = 'Envoyer ma candidature →';
      return;
    }

    // 2. Upload des fichiers
    const fd = new FormData();
    fd.append('offre_id', OFFRE_ID);
    fd.append('cv', cvFile);
    if (lmFile) fd.append('lm', lmFile);

    const upRes = await fetch('php/upload.php', { method:'POST', body: fd });
    const upData = await upRes.json();

    if (!upData.ok) {
      showFormAlert('Candidature enregistrée mais erreur upload : ' + upData.msg, 'error');
    } else {
      showFormAlert('✓ Candidature envoyée avec CV' + (lmFile ? ' et lettre de motivation' : '') + ' !', 'success');
      setTimeout(() => window.location.href = 'profil.php', 2500);
    }
  } catch(err) {
    showFormAlert('Erreur réseau. Vérifiez que XAMPP est démarré.', 'error');
  }

  btn.disabled = false; btn.textContent = 'Envoyer ma candidature →';
});

// ── FAVORI ────────────────────────────────────────────────────
function updateFavBtn(){
  const btn=document.getElementById('favBtn');
  if(!btn) return;
  const fav=isFavori(OFFRE_ID);
  btn.textContent=fav?'⭐ Sauvegardé':'☆ Favori';
  btn.style.color=fav?'var(--amber)':'';
  btn.style.borderColor=fav?'var(--amber)':'';
}
function toggleFavoriDetail(){
  toggleFavori(OFFRE_ID);
  updateFavBtn();
}
document.addEventListener('DOMContentLoaded', updateFavBtn);
</script>
</body>
</html>
