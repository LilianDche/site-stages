<?php require_once 'php/config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mon profil – Web4All</title>
  <meta name="description" content="Gérez votre profil candidat, suivez vos candidatures et retrouvez vos offres favorites.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script>(function(){var t=localStorage.getItem("w4a_theme")||"dark";document.documentElement.setAttribute("data-theme",t);})();</script>
  <script>window.SITE_URL="<?= SITE_URL ?>";</script>
</head>
<body>

<header class="big-header">
  <h1>Mon <span class="accent">profil</span></h1>
  <p class="header-sub">Gérez vos informations personnelles</p>
</header>

<nav class="navbar" id="navbar">
  <button class="burger-btn" id="burgerBtn" aria-label="Menu" aria-expanded="false">
    <span></span><span></span><span></span>
  </button>
  <div class="nav-links" id="navLinks">
    <a href="index.php">Accueil</a>
    <a href="entreprises.php">Entreprises</a>
    <a href="profil.php" id="on">Profil</a>
    <a href="mention_legales.php">Mentions légales</a>
  </div>
  <div class="nav-right" id="navRight">
    <button id="themeToggle" class="nav-btn" onclick="toggleTheme()" title="Changer le thème">🌸 Mode clair</button>
  </div>
</nav>

<?php include 'php/modal_auth.php'; ?>

<main class="container main-content">

  <!-- ── NON CONNECTÉ ──────────────────────────────────────── -->
  <div id="viewNotConnected" style="display:none;text-align:center;padding:5rem 2rem">
    <div style="font-size:3rem;opacity:.3;margin-bottom:1rem">🔒</div>
    <h2 style="font-family:'Outfit',sans-serif;color:#fff;margin-bottom:.5rem">Accès réservé</h2>
    <p style="color:var(--muted);margin-bottom:1.5rem">Connectez-vous pour accéder à votre profil.</p>
    <button class="btn btn-primary" onclick="openModal('login')">Se connecter</button>
    <p style="margin-top:1rem;font-size:.82rem;color:var(--muted)">
      <a href="#" onclick="openModal('login');setTimeout(()=>{document.getElementById('forgotPasswordLink')?.click()},300);return false;"
         style="color:var(--muted)">Mot de passe oublié ?</a>
    </p>
  </div>

  <!-- ── CONNECTÉ ──────────────────────────────────────────── -->
  <div id="viewConnected" style="display:none">
    <div class="profil-grid">

      <!-- SIDEBAR -->
      <aside class="profil-sidebar">
        <div class="avatar-circle" id="avatarCircle">?</div>
        <p class="profil-name"  id="profilFullName">–</p>
        <p class="profil-email" id="profilEmail">–</p>
        <p id="profilRole" style="font-size:.75rem;margin-top:.35rem"></p>

        <div class="profil-stats">
          <div class="stat-item">
            <div class="stat-num" id="nbCandidatures">–</div>
            <div class="stat-label">Candidatures</div>
          </div>
          <div class="stat-item">
            <div class="stat-num" id="nbFavoris">–</div>
            <div class="stat-label">Favoris</div>
          </div>
        </div>

        <button class="btn btn-ghost btn-full" style="margin-top:.5rem" onclick="doLogout()">
          Se déconnecter
        </button>
      </aside>

      <!-- CONTENU PRINCIPAL -->
      <div style="display:flex;flex-direction:column;gap:1.5rem">

        <!-- Informations personnelles -->
        <div class="form-card" style="max-width:100%">
          <p class="form-title">Informations personnelles</p>
          <p class="form-sub">Modifiez vos informations et sauvegardez.</p>

          <div class="form-alert error"   id="profilError"></div>
          <div class="form-alert success" id="profilSuccess"></div>

          <div class="form-row">
            <div class="form-group"><label>Prénom</label><input type="text" id="profilPrenom" autocomplete="given-name"></div>
            <div class="form-group"><label>Nom</label><input type="text" id="profilNom" autocomplete="family-name"></div>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" id="profilEmailInput" autocomplete="email">
          </div>
          <button class="btn btn-primary" id="saveProfilBtn">Enregistrer les modifications</button>
          <button class="btn btn-ghost" id="togglePwdBtn" style="margin-top:.25rem;font-size:.8rem" onclick="togglePwdBox()">🔒 Changer le mot de passe</button>
        </div>

        <!-- CV DE PROFIL -->
        <div class="form-card" style="max-width:100%">
          <p class="form-title">📄 Mon CV</p>
          <p class="form-sub">Ce CV sera joint automatiquement à vos candidatures. Max 20 Mo, PDF uniquement.</p>
          <div class="form-alert error"   id="cvError"></div>
          <div class="form-alert success" id="cvSuccess"></div>
          <div id="cvActuel" style="margin-bottom:.75rem;display:none">
            <div style="display:flex;align-items:center;gap:.75rem;padding:.65rem 1rem;background:var(--surface2);border:1px solid var(--border);border-radius:var(--r)">
              <span style="font-size:1.2rem">📄</span>
              <a id="cvLien" href="#" target="_blank" style="color:var(--amber);font-size:.88rem;flex:1">CV actuel</a>
              <button class="btn btn-ghost" id="supprimerCvBtn" style="font-size:.75rem;padding:.25rem .6rem;color:#f87171;border-color:rgba(239,68,68,.3)">✕ Supprimer</button>
            </div>
          </div>
          <label style="display:flex;align-items:center;gap:.75rem;cursor:pointer">
            <input type="file" id="cvFileInput" accept=".pdf,application/pdf" style="display:none">
            <button class="btn btn-ghost" id="cvUploadBtn" onclick="document.getElementById('cvFileInput').click()">📎 Choisir un PDF</button>
            <span id="cvFileName" style="font-size:.82rem;color:var(--muted)">Aucun fichier sélectionné</span>
          </label>
          <button class="btn btn-primary" id="enregistrerCvBtn" style="margin-top:.75rem;display:none">Enregistrer le CV</button>
        </div>

        <!-- CHANGEMENT MOT DE PASSE (masqué par défaut) -->
        <div class="form-card" id="pwdBox" style="max-width:100%;display:none">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.2rem">
            <p class="form-title">🔒 Changer le mot de passe</p>
            <button class="btn btn-ghost" style="font-size:.75rem;padding:.25rem .6rem" onclick="togglePwdBox()">✕</button>
          </div>
          <div id="pwdAlert" class="form-alert"></div>
          <div class="form-group">
            <label class="form-label">Mot de passe actuel</label>
            <input type="password" id="oldPassword" class="form-input" placeholder="••••••••">
          </div>
          <div class="form-group">
            <label class="form-label">Nouveau mot de passe</label>
            <input type="password" id="newPassword" class="form-input" placeholder="••••••••">
          </div>
          <div class="form-group">
            <label class="form-label">Confirmer le nouveau mot de passe</label>
            <input type="password" id="confirmPassword" class="form-input" placeholder="••••••••">
          </div>
          <button class="btn btn-primary" id="changePwdBtn">Modifier le mot de passe</button>
        </div>

        <!-- ONGLETS candidatures / favoris -->
        <div class="page-tabs">
          <button class="page-tab active" data-tab="candidatures">📨 Mes candidatures</button>
          <button class="page-tab"        data-tab="favoris">⭐ Mes favoris</button>
        </div>

        <!-- Panel candidatures -->
        <div id="panel-candidatures" class="form-card" style="max-width:100%">
          <p class="form-title">Mes candidatures</p>
          <p class="form-sub">Suivez l'état de vos dossiers.</p>
          <div id="candidaturesContainer">
            <div class="empty-state"><span class="spinner"></span></div>
          </div>
        </div>

        <!-- Panel favoris -->
        <div id="panel-favoris" class="form-card" style="max-width:100%;display:none">
          <p class="form-title">Mes favoris</p>
          <p class="form-sub">Offres sauvegardées pour plus tard.</p>
          <div id="favorisContainer">
            <div class="empty-state"><span class="spinner"></span></div>
          </div>
        </div>

      </div>
    </div>
  </div>
</main>

<footer>
  <span class="footer-brand">Web4All</span>
  <span>© 2026 – Projet pédagogique</span>
  <a href="mention_legales.php">Mentions légales</a>
</footer>

<script src="js/script.js"></script>
<script>
function e(str){ const d=document.createElement('div'); d.textContent=str??''; return d.innerHTML; }

function togglePwdBox(){
  const box = document.getElementById('pwdBox');
  const btn = document.getElementById('togglePwdBtn');
  const open = box.style.display === 'none';
  box.style.display = open ? 'block' : 'none';
  if(open){ box.scrollIntoView({behavior:'smooth',block:'nearest'}); }
  // appelé aussi depuis le lien "mot de passe oublié" dans le modal
}

document.addEventListener('DOMContentLoaded', async () => {
  await loadUser();

  if (!currentUser) {
    document.getElementById('viewNotConnected').style.display='block';
    return;
  }
  document.getElementById('viewConnected').style.display='block';

  // ── SIDEBAR ───────────────────────────────────────────────
  const initiales=((currentUser.prenom?.[0]||'')+(currentUser.nom?.[0]||'')).toUpperCase()||'?';
  document.getElementById('avatarCircle').textContent  = initiales;
  document.getElementById('profilFullName').textContent = currentUser.prenom+' '+currentUser.nom;
  document.getElementById('profilEmail').textContent    = currentUser.email;

  const roleLabel={admin:'Admin',pilote:'Pilote',etudiant:'Étudiant'}[currentUser.role]||'';
  const roleClass={admin:'role-admin',pilote:'role-pilote',etudiant:'role-etudiant'}[currentUser.role]||'';
  document.getElementById('profilRole').innerHTML=`<span class="role-badge ${roleClass}">${roleLabel}</span>`;

  // ── FORMULAIRE PROFIL ─────────────────────────────────────
  document.getElementById('profilPrenom').value     = currentUser.prenom||'';
  document.getElementById('profilNom').value        = currentUser.nom||'';
  document.getElementById('profilEmailInput').value = currentUser.email||'';

  document.getElementById('saveProfilBtn').addEventListener('click', async () => {
    const prenom = document.getElementById('profilPrenom').value.trim();
    const nom    = document.getElementById('profilNom').value.trim();
    const email  = document.getElementById('profilEmailInput').value.trim();
    const errEl  = document.getElementById('profilError');
    const okEl   = document.getElementById('profilSuccess');
    errEl.className=okEl.className='form-alert';

    if(!prenom||!nom||!email){ errEl.textContent='Remplissez tous les champs.'; errEl.className+=' error show'; return; }
    const res=await apiPost('php/auth.php',{action:'update_profil',prenom,nom,email});
    if(!res.ok){ errEl.textContent=res.msg||'Erreur.'; errEl.className+=' error show'; }
    else {
      currentUser=res.user;
      document.getElementById('profilFullName').textContent=prenom+' '+nom;
      document.getElementById('profilEmail').textContent=email;
      document.getElementById('avatarCircle').textContent=((prenom[0]||'')+(nom[0]||'')).toUpperCase();
      updateNavAuth();
      okEl.textContent='✓ Profil mis à jour.'; okEl.className+=' success show';
    }
  });

  // ── CHANGEMENT MOT DE PASSE ──────────────────────────────
  document.getElementById('changePwdBtn').addEventListener('click', async () => {
    const oldPwd  = document.getElementById('oldPassword').value;
    const newPwd  = document.getElementById('newPassword').value;
    const confirm = document.getElementById('confirmPassword').value;
    const alertEl = document.getElementById('pwdAlert');
    alertEl.className = 'form-alert';

    if (!oldPwd || !newPwd || !confirm) {
      alertEl.textContent = 'Remplissez tous les champs.'; alertEl.className += ' error show'; return;
    }
    if (newPwd !== confirm) {
      alertEl.textContent = 'Les mots de passe ne correspondent pas.'; alertEl.className += ' error show'; return;
    }
    if (newPwd.length < 6) {
      alertEl.textContent = 'Le nouveau mot de passe doit faire au moins 6 caractères.'; alertEl.className += ' error show'; return;
    }
    const btn = document.getElementById('changePwdBtn');
    btn.disabled = true; btn.textContent = '…';
    const res = await apiPost('php/auth.php', { action: 'change_password', old_password: oldPwd, new_password: newPwd });
    btn.disabled = false; btn.textContent = 'Modifier le mot de passe';
    if (!res.ok) { alertEl.textContent = res.msg || 'Erreur.'; alertEl.className += ' error show'; }
    else {
      alertEl.textContent = '✓ ' + res.msg; alertEl.className += ' success show';
      document.getElementById('oldPassword').value = '';
      document.getElementById('newPassword').value = '';
      document.getElementById('confirmPassword').value = '';
    }
  });

  // ── ONGLETS ───────────────────────────────────────────────
  document.querySelectorAll('.page-tab').forEach(tab=>{
    tab.addEventListener('click',()=>{
      document.querySelectorAll('.page-tab').forEach(t=>t.classList.remove('active'));
      tab.classList.add('active');
      const name=tab.dataset.tab;
      document.getElementById('panel-candidatures').style.display=name==='candidatures'?'block':'none';
      document.getElementById('panel-favoris').style.display=name==='favoris'?'block':'none';
      if(name==='favoris') loadFavoris();
    });
  });

  // ── CANDIDATURES ──────────────────────────────────────────
  const sLabel={envoyee:'Envoyée',vue:'Vue',acceptee:'Acceptée',refusee:'Refusée'};
  const sClass={envoyee:'statut-envoyee',vue:'statut-vue',acceptee:'statut-acceptee',refusee:'statut-refusee'};

  async function loadCandidatures(){
    try{
      const cands=await apiGet('php/candidatures.php',{action:'mes_candidatures'});
      const cont=document.getElementById('candidaturesContainer');
      document.getElementById('nbCandidatures').textContent=cands.length;

      if(!cands.length){
        cont.innerHTML='<div class="empty-state"><span class="empty-icon">📄</span><p>Aucune candidature pour l\'instant.<br><a href="index.php" style="color:var(--amber)">Parcourir les offres →</a></p></div>';
        return;
      }
      cont.innerHTML=cands.map(c=>`
        <div class="candidature-item">
          <div class="candidature-info">
            <h3><a href="offre.php?id=${c.offre_id||''}" style="color:#fff;text-decoration:none">${e(c.titre)}</a></h3>
            <p>${e(c.entreprise||'')} · ${e(c.lieu)} · ${e(c.type)} · ${new Date(c.created_at).toLocaleDateString('fr-FR')}</p>
            <div style="display:flex;gap:.4rem;margin-top:.3rem;flex-wrap:wrap">
              ${c.cv_path?`<a href="uploads/${e(c.cv_path)}" target="_blank" class="btn" style="font-size:.7rem;padding:.2rem .5rem">📄 CV</a>`:''}
              ${c.lm_path?`<a href="uploads/${e(c.lm_path)}" target="_blank" class="btn" style="font-size:.7rem;padding:.2rem .5rem">✉️ LM</a>`:''}
            </div>
          </div>
          <span class="${sClass[c.statut]||'statut-envoyee'}">${sLabel[c.statut]||c.statut}</span>
        </div>
      `).join('');
    }catch{
      document.getElementById('candidaturesContainer').innerHTML=
        '<div class="empty-state"><span class="empty-icon">⚠️</span><p>Impossible de charger les candidatures.</p></div>';
    }
  }

  // ── FAVORIS ───────────────────────────────────────────────
  let favorisLoaded=false;
  async function loadFavoris(){
    if(favorisLoaded) return;
    favorisLoaded=true;
    const cont=document.getElementById('favorisContainer');
    const ids=getFavoris();
    document.getElementById('nbFavoris').textContent=ids.length;

    if(!ids.length){
      cont.innerHTML='<div class="empty-state"><span class="empty-icon">⭐</span><p>Aucun favori pour l\'instant.<br>Cliquez sur ☆ sur une offre pour la sauvegarder.</p></div>';
      return;
    }
    cont.innerHTML='<div class="empty-state"><span class="spinner"></span></div>';
    try{
      // Charger les détails de chaque offre favorite
      const offres=await Promise.all(ids.map(id=>apiGet('php/offres.php',{id}).catch(()=>null)));
      const valides=offres.filter(o=>o&&o.titre);
      if(!valides.length){
        localStorage.removeItem('w4a_favoris');
        cont.innerHTML='<div class="empty-state"><span class="empty-icon">⭐</span><p>Vos favoris ont expiré.</p></div>';
        return;
      }
      cont.innerHTML=valides.map(o=>`
        <div class="candidature-item">
          <div class="candidature-info" style="flex:1">
            <h3><a href="offre.php?id=${o.id}" style="color:#fff;text-decoration:none">${e(o.titre)}</a></h3>
            <p>${e(o.entreprise||'')} · ${e(o.lieu)} · ${e(o.type)}${o.duree?' · '+e(o.duree):''}</p>
          </div>
          <div style="display:flex;gap:.4rem;flex-wrap:wrap;align-items:center">
            <a href="offre.php?id=${o.id}" class="btn" style="font-size:.78rem;padding:.35rem .75rem">Voir →</a>
            <button class="btn btn-ghost" style="font-size:.78rem;padding:.35rem .75rem;color:#f87171;border-color:rgba(239,68,68,.3)"
                    onclick="retirerFavori(${o.id},this)">✕ Retirer</button>
          </div>
        </div>`).join('');
    }catch{
      cont.innerHTML='<div class="empty-state"><span class="empty-icon">⚠️</span><p>Impossible de charger les favoris.</p></div>';
    }
  }

  window.retirerFavori=function(id,btn){
    toggleFavori(id);
    btn.closest('.candidature-item').style.opacity='0';
    setTimeout(()=>{ favorisLoaded=false; loadFavoris(); },300);
    const nb=getFavoris().length;
    document.getElementById('nbFavoris').textContent=nb;
  };

  // ── CV DE PROFIL ─────────────────────────────────────────
  function rafraichirCv(cvPath){
    const bloc   = document.getElementById('cvActuel');
    const lien   = document.getElementById('cvLien');
    const bouton = document.getElementById('enregistrerCvBtn');
    if(cvPath){
      lien.href        = 'uploads/'+cvPath;
      lien.textContent = cvPath;
      bloc.style.display = 'block';
    } else {
      bloc.style.display = 'none';
    }
    // Réinitialiser le sélecteur
    document.getElementById('cvFileInput').value = '';
    document.getElementById('cvFileName').textContent = 'Aucun fichier sélectionné';
    bouton.style.display = 'none';
  }
  rafraichirCv(currentUser.cv_path || null);

  document.getElementById('cvFileInput').addEventListener('change', function(){
    const f = this.files[0];
    const errEl = document.getElementById('cvError');
    const okEl  = document.getElementById('cvSuccess');
    errEl.className = okEl.className = 'form-alert';
    if(!f) return;
    if(f.type !== 'application/pdf' && !f.name.endsWith('.pdf')){
      errEl.textContent = 'Le fichier doit être un PDF.'; errEl.className += ' error show'; return;
    }
    if(f.size > 20 * 1024 * 1024){
      errEl.textContent = 'Le fichier dépasse 20 Mo.'; errEl.className += ' error show'; return;
    }
    document.getElementById('cvFileName').textContent = f.name;
    document.getElementById('enregistrerCvBtn').style.display = 'inline-block';
  });

  document.getElementById('enregistrerCvBtn').addEventListener('click', async () => {
    const f      = document.getElementById('cvFileInput').files[0];
    const errEl  = document.getElementById('cvError');
    const okEl   = document.getElementById('cvSuccess');
    const btn    = document.getElementById('enregistrerCvBtn');
    errEl.className = okEl.className = 'form-alert';
    if(!f){ errEl.textContent='Sélectionnez un fichier.'; errEl.className+=' error show'; return; }
    btn.disabled = true; btn.textContent = 'Envoi…';
    const fd = new FormData();
    fd.append('cv', f);
    try{
      const res = await fetch('php/upload_cv_profil.php', {method:'POST', body:fd}).then(r=>r.json());
      if(!res.ok){ errEl.textContent = res.msg||'Erreur.'; errEl.className += ' error show'; }
      else {
        currentUser.cv_path = res.cv_path;
        rafraichirCv(res.cv_path);
        okEl.textContent = '✓ '+res.msg; okEl.className += ' success show';
      }
    }catch{ errEl.textContent='Erreur réseau.'; errEl.className+=' error show'; }
    btn.disabled = false; btn.textContent = 'Enregistrer le CV';
  });

  document.getElementById('supprimerCvBtn').addEventListener('click', async () => {
    if(!confirm('Supprimer votre CV de profil ?')) return;
    const errEl = document.getElementById('cvError');
    const okEl  = document.getElementById('cvSuccess');
    errEl.className = okEl.className = 'form-alert';
    const fd = new FormData(); fd.append('action','supprimer');
    const res = await fetch('php/upload_cv_profil.php',{method:'POST',body:fd}).then(r=>r.json());
    if(!res.ok){ errEl.textContent=res.msg||'Erreur.'; errEl.className+=' error show'; }
    else{ currentUser.cv_path=null; rafraichirCv(null); okEl.textContent='✓ '+res.msg; okEl.className+=' success show'; }
  });

  // ── INIT ──────────────────────────────────────────────────
  await loadCandidatures();
  document.getElementById('nbFavoris').textContent=getFavoris().length;
});
</script>
</body>
</html>
