<?php require_once 'php/config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Postuler – Web4All</title>
  <meta name="description" content="Formulaire de candidature – Web4All Stages & Alternances">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script>(function(){var t=localStorage.getItem("w4a_theme")||"dark";document.documentElement.setAttribute("data-theme",t);})();</script>
  <script>window.SITE_URL="<?= SITE_URL ?>";</script>
</head>
<body>

<header class="big-header">
  <h1>Postuler à une <span class="accent">offre</span></h1>
  <p class="header-sub">Remplissez le formulaire ci-dessous</p>
</header>

<!-- ── NAVBAR avec burger mobile ──────────────────────────── -->
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

<!-- Bouton retour en haut -->
<button id="backToTop" title="Retour en haut" onclick="window.scrollTo({top:0,behavior:'smooth'})">↑</button>

<main class="container main-content">
  <a href="index.php" class="back-link">← Retour aux offres</a>

  <div class="form-card">
    <p class="form-title">Formulaire de candidature</p>
    <p class="form-sub">Les champs * sont obligatoires</p>

    <div class="form-alert error"   id="formError"></div>
    <div class="form-alert success" id="formSuccess"></div>

    <!-- Offre sélectionnée -->
    <div class="form-group">
      <label>Offre sélectionnée</label>
      <input type="text" id="offreInput" readonly placeholder="Chargement…">
    </div>

    <!-- Nom + Prénom -->
    <div class="form-row">
      <div class="form-group">
        <label for="nom">Nom *</label>
        <!--
          HTML5 : required rend le champ obligatoire nativement.
          L'événement blur est utilisé en JS pour passer en majuscules après saisie.
        -->
        <input type="text"
               id="nom"
               name="nom"
               required
               autocomplete="family-name"
               placeholder="DUPONT">
        <span class="field-error" id="errNom">Le nom est obligatoire.</span>
      </div>
      <div class="form-group">
        <label for="prenom">Prénom *</label>
        <input type="text"
               id="prenom"
               name="prenom"
               required
               autocomplete="given-name"
               placeholder="Jean">
        <span class="field-error" id="errPrenom">Le prénom est obligatoire.</span>
      </div>
    </div>

    <!-- Email -->
    <div class="form-group">
      <label for="email">Courriel *</label>
      <!--
        HTML5 : type="email" valide automatiquement le format.
        Le pattern avec regex renforce la validation côté navigateur.
        JS vérifie en plus à la soumission via une regex explicite.
      -->
      <input type="email"
             id="email"
             name="email"
             required
             autocomplete="email"
             pattern="[^@\s]+@[^@\s]+\.[^@\s]+"
             placeholder="jean.dupont@exemple.fr">
      <span class="field-error" id="errEmail">Courriel invalide (ex: nom@domaine.fr).</span>
    </div>

    <!-- Message -->
    <div class="form-group">
      <label for="message">Message au recruteur *</label>
      <textarea id="message"
                name="message"
                rows="5"
                required
                placeholder="Décrivez votre motivation pour ce poste…"></textarea>
      <span class="field-error" id="errMessage">Le message de motivation est obligatoire.</span>
    </div>

    <!-- CV (formats élargis selon demande client) -->
    <div class="form-group">
      <label>CV *
        <small style="color:var(--muted);font-weight:400">(PDF, Word, ODT, RTF, JPG, PNG – max 2 Mo)</small>
      </label>
      <div class="upload-zone" id="zoneCV">
        <!--
          accept liste tous les formats autorisés côté HTML5.
          La vérification JS (extension + taille) est faite à l'événement change.
        -->
        <input type="file"
               id="fileCV"
               accept=".pdf,.doc,.docx,.odt,.rtf,.jpg,.jpeg,.png,
                       application/pdf,
                       application/msword,
                       application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                       application/vnd.oasis.opendocument.text,
                       application/rtf,text/rtf,
                       image/jpeg,image/png">
        <div class="upload-icon">📄</div>
        <div class="upload-label"><strong>Cliquez</strong> ou glissez votre CV ici</div>
        <div class="upload-name" id="nameCV"></div>
      </div>
      <p class="upload-hint">PDF · DOC · DOCX · ODT · RTF · JPG · PNG &nbsp;·&nbsp; max 2 Mo</p>
      <span class="field-error" id="errCV">CV obligatoire. Format accepté : PDF, DOC, DOCX, ODT, RTF, JPG, PNG (max 2 Mo).</span>
    </div>

    <!-- Lettre de motivation (PDF seul, optionnel) -->
    <div class="form-group">
      <label>Lettre de motivation
        <small style="color:var(--muted);font-weight:400">(PDF, optionnel)</small>
      </label>
      <div class="upload-zone" id="zoneLM">
        <input type="file" id="fileLM" accept=".pdf,application/pdf">
        <div class="upload-icon">✉️</div>
        <div class="upload-label"><strong>Cliquez</strong> ou glissez votre lettre ici</div>
        <div class="upload-name" id="nameLM"></div>
      </div>
      <p class="upload-hint">PDF uniquement &nbsp;·&nbsp; max 2 Mo</p>
    </div>

    <button class="btn btn-primary btn-full" id="submitBtn">Envoyer ma candidature →</button>
  </div>
</main>

<footer>
  <span class="footer-brand">Web4All</span>
  <span>© 2026 – Projet pédagogique</span>
  <a href="mention_legales.php">Mentions légales</a>
</footer>

<script src="js/script.js"></script>
<script>
// ============================================================
//  formulaire.php — Validation JS + UX
//  Répond aux demandes de Jean-Marc (mail du client) :
//  1. Nom en majuscules après saisie (événement blur)
//  2. Validation email (type="email" + regex JS)
//  3. CV : formats autorisés + poids max 2 Mo
//  4. Champs obligatoires vérifiés avant envoi
//  5. Bouton retour en haut (scroll)
//  6. Menu burger mobile
// ============================================================

const MAX_SIZE  = 2 * 1024 * 1024; // 2 Mo
const CV_EXTS   = ['pdf','doc','docx','odt','rtf','jpg','jpeg','png'];
const EMAIL_RE  = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// ── 1. NOM EN MAJUSCULES après saisie (événement blur) ───────
// On écoute "blur" : l'événement se déclenche quand l'utilisateur
// quitte le champ, ce qui est le bon moment pour transformer la valeur.
document.getElementById('nom').addEventListener('blur', function() {
  this.value = this.value.toUpperCase();
});

// ── 2. VALIDATION EMAIL en temps réel (événement input) ──────
document.getElementById('email').addEventListener('input', function() {
  const valid = EMAIL_RE.test(this.value.trim());
  setFieldState('email', 'errEmail', valid || this.value.trim() === '');
});

// ── 3. UPLOAD CV — formats + poids ───────────────────────────
// Formats autorisés : .pdf .doc .docx .odt .rtf .jpg .png
// Vérification de l'extension ET de la taille à l'événement "change"
function getExtension(filename) {
  return filename.split('.').pop().toLowerCase();
}

function setupZone(zoneId, inputId, nameId, allowedExts) {
  const zone  = document.getElementById(zoneId);
  const input = document.getElementById(inputId);
  const name  = document.getElementById(nameId);
  if (!zone || !input) return;

  function validateFile(file) {
    if (!file) return;

    // Vérification de la taille (max 2 Mo)
    if (file.size > MAX_SIZE) {
      showZoneError(zone, input, `"${file.name}" dépasse 2 Mo.`);
      return;
    }

    // Vérification de l'extension
    const ext = getExtension(file.name);
    if (!allowedExts.includes(ext)) {
      showZoneError(zone, input,
        `Format non autorisé. Acceptés : ${allowedExts.join(', ').toUpperCase()}.`);
      return;
    }

    // Fichier valide
    zone.classList.remove('file-error');
    zone.classList.add('has-file');
    name.textContent = '✓ ' + file.name + ' (' + (file.size / 1024).toFixed(0) + ' Ko)';
    if (nameId === 'nameCV') setFieldState('fileCV', 'errCV', true);
  }

  function showZoneError(zone, input, msg) {
    zone.classList.remove('has-file');
    zone.classList.add('file-error');
    input.value = '';
    showFormMsg(msg, 'error');
    if (nameId === 'nameCV') {
      const err = document.getElementById('errCV');
      if (err) { err.textContent = msg; err.classList.add('show'); }
    }
  }

  // Événement change (clic ou clavier)
  input.addEventListener('change', () => validateFile(input.files[0]));

  // Drag & drop
  zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('dragover'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
  zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (file) { input.files = e.dataTransfer.files; validateFile(file); }
  });
}

setupZone('zoneCV', 'fileCV', 'nameCV', CV_EXTS);
setupZone('zoneLM', 'fileLM', 'nameLM', ['pdf']);

// ── Helpers affichage erreurs ─────────────────────────────────
function showFormMsg(msg, type) {
  const errEl = document.getElementById('formError');
  const okEl  = document.getElementById('formSuccess');
  errEl.className = okEl.className = 'form-alert';
  if (type === 'error') { errEl.textContent = msg; errEl.className += ' error show'; }
  else                  { okEl.textContent  = msg; okEl.className  += ' success show'; }
}

// Affiche/masque l'erreur inline d'un champ
// valid=true → champ OK, valid=false → champ en erreur
function setFieldState(inputId, errId, valid) {
  const input = document.getElementById(inputId);
  const err   = document.getElementById(errId);
  if (!input) return;
  if (valid) {
    input.classList.remove('invalid');
    if (err) err.classList.remove('show');
  } else {
    input.classList.add('invalid');
    if (err) err.classList.add('show');
  }
}

// ── 4. VÉRIFICATION à la soumission ──────────────────────────
// On vérifie manuellement chaque champ obligatoire avant d'envoyer.
// Si un champ est vide, on affiche le message d'erreur correspondant
// et on empêche l'envoi (return false).
document.addEventListener('DOMContentLoaded', async () => {

  // Pré-remplir l'offre depuis l'URL
  const params  = new URLSearchParams(window.location.search);
  const offreId = params.get('offre');
  if (offreId) {
    try {
      const o = await apiGet('php/offres.php', { id: offreId });
      const inp = document.getElementById('offreInput');
      if (inp && o.titre) { inp.value = o.titre; inp.dataset.offreId = o.id; }
    } catch {}
  }

  // Pré-remplir depuis le profil connecté
  const userData = await apiGet('php/auth.php', { action: 'me' });
  if (userData.ok && userData.user) {
    const u = userData.user;
    document.getElementById('nom').value    = (u.nom    || '').toUpperCase();
    document.getElementById('prenom').value = u.prenom  || '';
    document.getElementById('email').value  = u.email   || '';
  }

  // ── Soumission ───────────────────────────────────────────
  document.getElementById('submitBtn').addEventListener('click', async () => {
    const nom     = document.getElementById('nom').value.trim();
    const prenom  = document.getElementById('prenom').value.trim();
    const email   = document.getElementById('email').value.trim();
    const message = document.getElementById('message').value.trim();
    const cvFile  = document.getElementById('fileCV').files[0];
    const lmFile  = document.getElementById('fileLM').files[0];
    const offreId = document.getElementById('offreInput')?.dataset.offreId;

    // Validation champ par champ — affiche les erreurs inline
    let hasError = false;

    // Nom obligatoire
    if (!nom) { setFieldState('nom', 'errNom', false); hasError = true; }
    else        setFieldState('nom', 'errNom', true);

    // Prénom obligatoire
    if (!prenom) { setFieldState('prenom', 'errPrenom', false); hasError = true; }
    else          setFieldState('prenom', 'errPrenom', true);

    // Email obligatoire + format valide
    if (!email || !EMAIL_RE.test(email)) { setFieldState('email', 'errEmail', false); hasError = true; }
    else                                  setFieldState('email', 'errEmail', true);

    // Message obligatoire
    if (!message) { setFieldState('message', 'errMessage', false); hasError = true; }
    else           setFieldState('message', 'errMessage', true);

    // CV obligatoire
    if (!cvFile) { setFieldState('fileCV', 'errCV', false); hasError = true; }
    else          setFieldState('fileCV', 'errCV', true);

    // Si au moins une erreur, on arrête et on informe l'utilisateur
    if (hasError) {
      showFormMsg('Veuillez corriger les champs en rouge avant d\'envoyer.', 'error');
      // Scroller jusqu'à la première erreur visible
      const firstErr = document.querySelector('.field-error.show, input.invalid, textarea.invalid');
      if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
      return;
    }

    // Vérifier la session
    const me = await apiGet('php/auth.php', { action: 'me' });
    if (!me.ok) {
      showFormMsg('Vous devez être connecté pour postuler.', 'error');
      setTimeout(() => openModal('login'), 600);
      return;
    }
    if (me.user.role !== 'etudiant') {
      showFormMsg('Seuls les étudiants peuvent postuler.', 'error');
      return;
    }

    const btn = document.getElementById('submitBtn');
    btn.disabled = true; btn.textContent = 'Envoi en cours…';

    try {
      // Étape 1 : enregistrer la candidature
      const res = await apiPost('php/candidatures.php', { action: 'postuler', offre_id: offreId, message });
      if (!res.ok) {
        showFormMsg(res.msg || 'Erreur lors de l\'envoi.', 'error');
        btn.disabled = false; btn.textContent = 'Envoyer ma candidature →';
        return;
      }

      // Étape 2 : upload des fichiers
      const fd = new FormData();
      fd.append('offre_id', offreId);
      fd.append('cv', cvFile);
      if (lmFile) fd.append('lm', lmFile);
      const upRes  = await fetch('php/upload.php', { method: 'POST', body: fd });
      const upData = await upRes.json();

      if (!upData.ok) {
        showFormMsg('Candidature enregistrée, mais erreur lors de l\'upload : ' + upData.msg, 'error');
      } else {
        showFormMsg('✓ Candidature envoyée avec succès !', 'success');
        setTimeout(() => window.location.href = 'profil.php', 2000);
      }
    } catch (err) {
      showFormMsg('Erreur réseau. Vérifiez que XAMPP est démarré.', 'error');
    }
    btn.disabled = false; btn.textContent = 'Envoyer ma candidature →';
  });
});

// ── 5. BOUTON RETOUR EN HAUT ──────────────────────────────────
// Apparaît quand la navbar n'est plus visible (scrollY > hauteur navbar)
// On écoute l'événement "scroll" sur window.
const backTopBtn = document.getElementById('backToTop');
window.addEventListener('scroll', () => {
  const navbar = document.getElementById('navbar');
  const navBottom = navbar ? navbar.getBoundingClientRect().bottom : 80;
  // Si le bas de la navbar est au-dessus du viewport (navbar plus visible)
  if (navBottom < 0) {
    backTopBtn.classList.add('visible');
  } else {
    backTopBtn.classList.remove('visible');
  }
});

// ── 6. MENU BURGER MOBILE ─────────────────────────────────────
// Le bouton burger affiche/masque les liens de navigation en mobile.
// On écoute l'événement "click" sur le bouton burger.
// aria-expanded est mis à jour pour l'accessibilité.
const burgerBtn  = document.getElementById('burgerBtn');
const navLinks   = document.getElementById('navLinks');

burgerBtn.addEventListener('click', () => {
  const isOpen = navLinks.classList.toggle('open');
  burgerBtn.classList.toggle('open', isOpen);
  burgerBtn.setAttribute('aria-expanded', isOpen);
});

// Fermer le menu si on clique sur un lien
navLinks.querySelectorAll('a').forEach(link => {
  link.addEventListener('click', () => {
    navLinks.classList.remove('open');
    burgerBtn.classList.remove('open');
    burgerBtn.setAttribute('aria-expanded', 'false');
  });
});

// Fermer le menu si on clique ailleurs
document.addEventListener('click', e => {
  if (!burgerBtn.contains(e.target) && !navLinks.contains(e.target)) {
    navLinks.classList.remove('open');
    burgerBtn.classList.remove('open');
    burgerBtn.setAttribute('aria-expanded', 'false');
  }
});
</script>
</body>
</html>
