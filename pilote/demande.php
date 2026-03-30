<?php
require_once '../php/config.php';
requirePilote();
$u = currentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Soumettre une offre – Web4All</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <script>
    </script>
<script src="../js/script.js"></script>
  <script>window.SITE_URL="<?= SITE_URL ?>";</script>
  <script>(function(){var t=localStorage.getItem("w4a_theme")||"dark";document.documentElement.setAttribute("data-theme",t);})();</script>
</head>
<body>
<header class="big-header">
  <h1>Soumettre une <span class="accent">offre</span></h1>
  <p class="header-sub">Votre demande sera examinée par un administrateur</p>
</header>
<nav class="navbar" id="navbar">
  <a href="../index.php">← Site</a>
  <a href="../entreprises.php">Entreprises</a>
  <a href="index.php">Espace Pilote</a>
  <a href="demande.php" id="on" class="nav-highlight">+ Soumettre une offre</a>
  <?php if(isAdmin()):?><a href="../admin/index.php">Admin</a><?php endif;?>
  <div class="nav-right">
    <span class="nav-user">
      <span class="role-badge role-pilote">Pilote</span>
      <?=e($u['prenom'].' '.$u['nom'])?>
      <?php if(!empty($u['entreprise_nom'])): ?>
        · <span style="color:var(--amber);font-size:.78rem"><?=e($u['entreprise_nom'])?></span>
      <?php endif;?>
    </span>
  <button class="theme-toggle" onclick="toggleTheme()" id="themeToggle" style="margin-right:.5rem">🌸 Mode clair</button>
    <button class="nav-btn" onclick="fetch('../php/auth.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'logout'})}).then(()=>location.href='../index.php')">Déconnexion</button>
  </div>
</nav>

<main class="container main-content">
  <div style="display:grid;grid-template-columns:1fr 340px;gap:1.5rem;align-items:start">
    <!-- Formulaire -->
    <div class="form-card" style="max-width:100%">
      <p class="form-title">Nouvelle offre de stage / alternance</p>
      <p class="form-sub">Pour l'entreprise : <strong style="color:var(--amber)"><?=e($u['entreprise_nom']??'Non définie')?></strong></p>

      <div class="form-alert error"   id="formError"></div>
      <div class="form-alert success" id="formSuccess"></div>

      <div class="form-row">
        <div class="form-group"><label>Titre *</label><input type="text" id="fTitre" placeholder="ex : Stage Développeur Web"></div>
        <div class="form-group"><label>Type *</label>
          <select id="fType"><option value="stage">Stage</option><option value="alternance">Alternance</option></select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Lieu *</label>
          <select id="fLieu">
            <option value="Rouen">Rouen</option>
            <option value="Le Havre">Le Havre</option>
            <option value="Caen">Caen</option>
            <option value="Paris">Paris</option>
            <option value="Lille">Lille</option>
          </select>
        </div>
        <div class="form-group"><label>Durée</label><input type="text" id="fDuree" placeholder="ex : 6 mois"></div>
      </div>
      <div class="form-group"><label>Rémunération</label><input type="text" id="fRemun" placeholder="ex : 600 €/mois"></div>
      <div class="form-group"><label>Description complète *</label>
        <textarea id="fDesc" rows="6" placeholder="Décrivez le poste, les missions, le contexte…"></textarea>
      </div>
      <div class="form-group">
        <label>Compétences requises</label>
        <div id="compsList" style="display:flex;flex-wrap:wrap;gap:.35rem;margin-top:.4rem"></div>
      </div>
      <button class="btn btn-primary btn-full" id="submitBtn" style="margin-top:.5rem">Soumettre la demande →</button>
    </div>

    <!-- Sidebar mes demandes -->
    <div>
      <div class="form-card" style="max-width:100%">
        <p class="form-title" style="font-size:1rem">Mes demandes</p>
        <p class="form-sub">Historique de vos soumissions</p>
        <div id="mesDemandesContainer">
          <div class="empty-state" style="padding:2rem"><span class="spinner"></span></div>
        </div>
      </div>

      <div class="form-card" style="max-width:100%;margin-top:1rem">
        <p class="form-title" style="font-size:.95rem">ℹ️ Comment ça marche ?</p>
        <ol style="padding-left:1.2rem;font-size:.82rem;color:var(--muted);line-height:2">
          <li>Remplissez le formulaire</li>
          <li>Soumettez votre demande</li>
          <li>L'admin la valide sous 48h</li>
          <li>L'offre est publiée sur le site</li>
        </ol>
      </div>
    </div>
  </div>
</main>
<footer><span class="footer-brand">Web4All</span><span>© 2026</span></footer>

<script>
async function call(url,data){ const r=await fetch(url,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)}); return r.json(); }
async function get(url,p={}){ const qs=new URLSearchParams(p).toString(); return (await fetch(qs?`${url}?${qs}`:url)).json(); }
function e(s){ const d=document.createElement('div'); d.textContent=s??''; return d.innerHTML; }

// Compétences
let allComps=[]; const selComps=new Set();
async function loadComps(){
  allComps=await get('../php/competences.php');
  renderComps();
}
function renderComps(){
  const div=document.getElementById('compsList');
  div.innerHTML=allComps.map(c=>`
    <label style="display:inline-flex;align-items:center;gap:.25rem;cursor:pointer;padding:.2rem .6rem;
      border-radius:6px;font-size:.72rem;
      background:${selComps.has(c.id)?'var(--amber-dim)':'var(--surface2)'};
      border:1px solid ${selComps.has(c.id)?'var(--border-hi)':'rgba(255,255,255,.06)'};
      color:${selComps.has(c.id)?'var(--amber)':'var(--muted)'}">
      <input type="checkbox" ${selComps.has(c.id)?'checked':''} style="display:none"
             onchange="toggle(${c.id})">${e(c.nom)}
    </label>`).join('');
}
function toggle(id){ selComps.has(id)?selComps.delete(id):selComps.add(id); renderComps(); }

// Mes demandes
async function loadMesDemandes(){
  const list=await call('../php/demande_offre.php',{action:'mes_demandes'});
  const cont=document.getElementById('mesDemandesContainer');
  const statLabels={validee:'Validée',en_attente:'En attente',refusee:'Refusée'};
  if(!list.length){ cont.innerHTML='<p style="font-size:.82rem;color:var(--muted);text-align:center;padding:1rem">Aucune demande pour l\'instant.</p>'; return; }
  cont.innerHTML=list.map(o=>`
    <div class="demande-card">
      <div class="demande-card-info">
        <h3>${e(o.titre)}</h3>
        <p>${e(o.type)} · ${e(o.lieu)} · ${new Date(o.created_at).toLocaleDateString('fr-FR')}</p>
      </div>
      <span class="statut-${o.statut_validation}">${statLabels[o.statut_validation]||o.statut_validation}</span>
    </div>`).join('');
}

// Soumission
document.getElementById('submitBtn').addEventListener('click', async ()=>{
  const errEl=document.getElementById('formError'); const okEl=document.getElementById('formSuccess');
  errEl.className='form-alert error'; okEl.className='form-alert success';
  const titre=document.getElementById('fTitre').value.trim();
  const desc =document.getElementById('fDesc').value.trim();
  const type =document.getElementById('fType').value;
  const lieu =document.getElementById('fLieu').value;
  if(!titre||!desc) { errEl.textContent='Titre et description sont obligatoires.'; errEl.className+=' show'; return; }
  const btn=document.getElementById('submitBtn'); btn.disabled=true; btn.textContent='Envoi…';
  const res=await call('../php/demande_offre.php',{
    action:'soumettre',titre,description:desc,type,lieu,
    duree:document.getElementById('fDuree').value.trim(),
    remuneration:document.getElementById('fRemun').value.trim(),
    competences:[...selComps]
  });
  btn.disabled=false; btn.textContent='Soumettre la demande →';
  if(!res.ok){ errEl.textContent=res.msg; errEl.className+=' show'; }
  else {
    okEl.textContent=res.msg; okEl.className+=' show';
    ['fTitre','fDesc','fDuree','fRemun'].forEach(id=>document.getElementById(id).value='');
    selComps.clear(); renderComps();
    await loadMesDemandes();
  }
});

loadComps();
loadMesDemandes();
</script>
</body>
</html>
