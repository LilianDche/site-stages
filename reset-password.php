<?php
require_once 'php/config.php';

$token = trim($_GET['token'] ?? '');
$valid = false;
$prenom = '';

if($token){
    $db   = getDB();
    $stmt = $db->prepare('SELECT id, prenom FROM utilisateurs WHERE reset_token=? AND reset_token_expires > NOW() AND statut="actif"');
    $stmt->execute([$token]);
    $row  = $stmt->fetch();
    $valid = (bool)$row;
    if($row) $prenom = $row['prenom'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Réinitialiser le mot de passe – Web4All</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script>(function(){var t=localStorage.getItem("w4a_theme")||"dark";document.documentElement.setAttribute("data-theme",t);})();</script>
  <script>window.SITE_URL="<?= SITE_URL ?>";</script>
</head>
<body>

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

<main class="container main-content" style="display:flex;align-items:center;justify-content:center;padding-top:4rem">

  <?php if(!$token || !$valid): ?>
  <!-- Lien invalide ou expiré -->
  <div class="form-card" style="text-align:center;max-width:440px">
    <div style="font-size:3rem;margin-bottom:1rem">⚠️</div>
    <p class="form-title">Lien invalide ou expiré</p>
    <p class="form-sub" style="margin-bottom:1.5rem">Ce lien de réinitialisation n'est plus valide.<br>Il expire après 1 heure.</p>
    <button class="btn btn-primary btn-full" onclick="window.location.href='index.php?auth=1'">
      Refaire une demande →
    </button>
  </div>

  <?php else: ?>
  <!-- Formulaire de reset -->
  <div class="form-card" style="max-width:440px;width:100%">
    <p class="form-title">🔑 Nouveau mot de passe</p>
    <p class="form-sub">Bonjour <?= e($prenom) ?>, choisissez un nouveau mot de passe.</p>

    <div class="form-alert" id="resetAlert"></div>

    <div class="form-group">
      <label>Nouveau mot de passe <small style="color:var(--muted);font-weight:400">(min. 6 car.)</small></label>
      <input type="password" id="newPwd" class="form-input" placeholder="••••••••" autocomplete="new-password">
    </div>
    <div class="form-group">
      <label>Confirmer le mot de passe</label>
      <input type="password" id="confirmPwd" class="form-input" placeholder="••••••••" autocomplete="new-password">
    </div>

    <button class="btn btn-primary btn-full" id="resetBtn">Réinitialiser →</button>
    <p class="form-switch" style="margin-top:1rem">
      <a href="index.php">← Retour à l'accueil</a>
    </p>
  </div>

  <script>
  const TOKEN = <?= json_encode($token) ?>;

  document.getElementById('resetBtn').addEventListener('click', async () => {
    const newPwd    = document.getElementById('newPwd').value;
    const confirmPwd= document.getElementById('confirmPwd').value;
    const alertEl   = document.getElementById('resetAlert');
    alertEl.className = 'form-alert';

    if(!newPwd || !confirmPwd){
      alertEl.textContent = 'Remplissez les deux champs.';
      alertEl.className += ' error show'; return;
    }
    if(newPwd !== confirmPwd){
      alertEl.textContent = 'Les mots de passe ne correspondent pas.';
      alertEl.className += ' error show'; return;
    }
    if(newPwd.length < 6){
      alertEl.textContent = 'Le mot de passe doit faire au moins 6 caractères.';
      alertEl.className += ' error show'; return;
    }

    const btn = document.getElementById('resetBtn');
    btn.disabled = true; btn.textContent = '…';

    const res = await fetch('php/auth.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({action:'reset_password', token: TOKEN, password: newPwd})
    }).then(r=>r.json());

    btn.disabled = false; btn.textContent = 'Réinitialiser →';

    if(!res.ok){
      alertEl.textContent = res.msg || 'Erreur.';
      alertEl.className += ' error show';
    } else {
      alertEl.textContent = '✓ ' + res.msg;
      alertEl.className += ' success show';
      // Rediriger vers la page de connexion après 2s
      setTimeout(()=>{ window.location.href = 'index.php?auth=1'; }, 2000);
    }
  });

  // Enter key
  ['newPwd','confirmPwd'].forEach(id=>{
    document.getElementById(id)?.addEventListener('keydown', ev=>{
      if(ev.key==='Enter') document.getElementById('resetBtn').click();
    });
  });
  </script>
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
