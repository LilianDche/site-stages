<?php
require_once '../php/config.php';
requireAdmin();
$u = currentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administration – Web4All</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <script>(function(){ var t=localStorage.getItem('w4a_theme')||'dark'; document.documentElement.setAttribute('data-theme',t); })();</script>
  <script src="../js/script.js"></script>
</head>
<body>

<header class="big-header">
  <h1>Panel <span class="accent">Admin</span></h1>
  <p class="header-sub">Gestion complète de la plateforme</p>
</header>

<nav class="navbar" id="navbar">
  <a href="../index.php">← Site</a>
  <a href="index.php" id="on">Admin</a>
  <div class="nav-right">
    <span class="nav-user">
      <span class="role-badge role-admin">Admin</span>
      <?= e($u['prenom'].' '.$u['nom']) ?>
    </span>
    <button class="theme-toggle" id="themeToggle" onclick="toggleTheme()">🌸 Mode clair</button>
    <button class="nav-btn" onclick="logout()">Déconnexion</button>
  </div>
</nav>

<main class="container main-content">

  <!-- STATS -->
  <div class="admin-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:1.5rem">
    <div class="stat-card"><span class="stat-icon">📋</span><div><div class="stat-big" id="sOffres">–</div><div class="stat-lbl">Offres actives</div></div></div>
    <div class="stat-card"><span class="stat-icon">🏢</span><div><div class="stat-big" id="sEntreprises">–</div><div class="stat-lbl">Entreprises</div></div></div>
    <div class="stat-card"><span class="stat-icon">👤</span><div><div class="stat-big" id="sUsers">–</div><div class="stat-lbl">Étudiants</div></div></div>
    <div class="stat-card"><span class="stat-icon">📨</span><div><div class="stat-big" id="sCands">–</div><div class="stat-lbl">Candidatures</div></div></div>
  </div>

  <!-- Alerte validations -->
  <div id="alertValidations" style="display:none;background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.3);border-radius:12px;padding:.8rem 1.2rem;margin-bottom:1.25rem;font-size:.85rem;color:#fbbf24;cursor:pointer" onclick="switchTab('validations')">
    ⚡ <span id="alertValidationsText"></span> — <u>Voir</u>
  </div>

  <!-- ONGLETS -->
  <div class="admin-tabs">
    <button class="admin-tab active"  data-tab="offres">📋 Offres</button>
    <button class="admin-tab"         data-tab="entreprises">🏢 Entreprises</button>
    <button class="admin-tab"         data-tab="utilisateurs">👤 Utilisateurs</button>
    <button class="admin-tab"         data-tab="comptes">🔑 Comptes</button>
    <button class="admin-tab"         data-tab="candidatures">📨 Candidatures</button>
    <button class="admin-tab"         data-tab="validations" id="tabValidations">⚡ Validations</button>
  </div>

  <!-- ══ OFFRES ══════════════════════════════════════════════ -->
  <div id="panel-offres" class="tab-panel active">
    <div class="admin-form" id="offreForm">
      <h3 id="offreFormTitle">➕ Nouvelle offre</h3>
      <input type="hidden" id="offreId">
      <div class="form-alert error"   id="offreError"></div>
      <div class="form-alert success" id="offreSuccess"></div>
      <div class="form-row">
        <div class="form-group"><label>Titre *</label><input type="text" id="fTitre" placeholder="Stage Développeur Web…"></div>
        <div class="form-group"><label>Entreprise *</label><select id="fEntrepriseId"><option value="">— Choisir —</option></select></div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Lieu *</label>
          <select id="fLieu"><option>Rouen</option><option>Le Havre</option><option>Caen</option><option>Paris</option><option>Lille</option></select>
        </div>
        <div class="form-group">
          <label>Type *</label>
          <select id="fType"><option value="stage">Stage</option><option value="alternance">Alternance</option></select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Durée</label><input type="text" id="fDuree" placeholder="ex: 6 mois"></div>
        <div class="form-group"><label>Rémunération</label><input type="text" id="fRemun" placeholder="ex: 600 €/mois"></div>
      </div>
      <div class="form-group"><label>Description *</label><textarea id="fDesc" rows="3" placeholder="Description du poste…"></textarea></div>
      <div class="form-group">
        <label>Compétences</label>
        <div id="fCompsList" style="display:flex;flex-wrap:wrap;gap:.35rem;margin-top:.3rem"></div>
      </div>
      <div style="display:flex;gap:.75rem;margin-top:.75rem">
        <button class="btn btn-primary" id="saveOffreBtn">Enregistrer</button>
        <button class="btn btn-ghost"   id="resetOffreBtn">Annuler</button>
      </div>
    </div>
    <table class="data-table">
      <thead><tr><th>Titre</th><th>Entreprise</th><th>Lieu</th><th>Type</th><th>Validation</th><th>Actif</th><th>Actions</th></tr></thead>
      <tbody id="offresTbody"><tr><td colspan="7" style="text-align:center;padding:2rem"><span class="spinner"></span></td></tr></tbody>
    </table>
  </div>

  <!-- ══ ENTREPRISES ════════════════════════════════════════ -->
  <div id="panel-entreprises" class="tab-panel" style="display:none">
    <div class="admin-form">
      <h3 id="entFormTitle">➕ Nouvelle entreprise</h3>
      <input type="hidden" id="entId">
      <div class="form-alert error"   id="entError"></div>
      <div class="form-alert success" id="entSuccess"></div>
      <div class="form-row">
        <div class="form-group"><label>Nom *</label><input type="text" id="eNom"></div>
        <div class="form-group"><label>Secteur</label><input type="text" id="eSecteur"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Ville</label><input type="text" id="eVille"></div>
        <div class="form-group"><label>Code postal</label><input type="text" id="eCP"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Email contact</label><input type="email" id="eEmail"></div>
        <div class="form-group"><label>Téléphone</label><input type="text" id="eTel"></div>
      </div>
      <div class="form-group"><label>Site web</label><input type="url" id="eSite" placeholder="https://"></div>
      <div class="form-group"><label>Description</label><textarea id="eDesc" rows="2"></textarea></div>
      <div style="display:flex;gap:.75rem;margin-top:.75rem">
        <button class="btn btn-primary" id="saveEntBtn">Enregistrer</button>
        <button class="btn btn-ghost"   id="resetEntBtn">Annuler</button>
      </div>
    </div>
    <table class="data-table">
      <thead><tr><th>Nom</th><th>Secteur</th><th>Ville</th><th>Offres</th><th>Contact</th><th>Actions</th></tr></thead>
      <tbody id="entsTbody"><tr><td colspan="6" style="text-align:center;padding:2rem"><span class="spinner"></span></td></tr></tbody>
    </table>
  </div>

  <!-- ══ UTILISATEURS ═══════════════════════════════════════ -->
  <div id="panel-utilisateurs" class="tab-panel" style="display:none">
    <table class="data-table">
      <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Promo / Entreprise</th><th>Inscription</th><th>Candidatures</th><th>Actions</th></tr></thead>
      <tbody id="usersTbody"><tr><td colspan="7" style="text-align:center;padding:2rem"><span class="spinner"></span></td></tr></tbody>
    </table>
  </div>

  <!-- ══ COMPTES ════════════════════════════════════════════ -->
  <div id="panel-comptes" class="tab-panel" style="display:none">
    <div style="display:flex;gap:.5rem;margin-bottom:1.25rem;flex-wrap:wrap">
      <button class="btn btn-ghost filter-btn active" id="fTous"     onclick="filterComptes('tous')">Tous</button>
      <button class="btn btn-ghost filter-btn"        id="fActif"    onclick="filterComptes('actif')">Actifs</button>
      <button class="btn btn-ghost filter-btn"        id="fAttente"  onclick="filterComptes('en_attente')" style="color:#fbbf24;border-color:rgba(251,191,36,.3)">En attente</button>
      <button class="btn btn-ghost filter-btn"        id="fSuspendu" onclick="filterComptes('suspendu')"   style="color:#f87171;border-color:rgba(239,68,68,.3)">Suspendus</button>
    </div>
    <table class="data-table">
      <thead><tr><th>Utilisateur</th><th>Rôle</th><th>Entreprise / Promo</th><th>Inscription</th><th>Statut</th><th>Actions</th></tr></thead>
      <tbody id="comptesTbody"><tr><td colspan="6" style="text-align:center;padding:2rem"><span class="spinner"></span></td></tr></tbody>
    </table>
  </div>

  <!-- ══ CANDIDATURES ═══════════════════════════════════════ -->
  <div id="panel-candidatures" class="tab-panel" style="display:none">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem">
      <div style="display:flex;gap:.5rem;flex-wrap:wrap">
        <input type="text" id="candSearch" placeholder="Rechercher candidat, offre…"
               oninput="filterCands()"
               style="padding:.5rem .9rem;border-radius:8px;border:1px solid var(--border);background:var(--surface);color:var(--text);font-size:.82rem;outline:none;width:220px">
        <select id="candStatutFilter" onchange="filterCands()"
                style="padding:.5rem .9rem;border-radius:8px;border:1px solid var(--border);background:var(--surface);color:var(--text);font-size:.82rem;outline:none">
          <option value="">Tous les statuts</option>
          <option value="envoyee">Envoyée</option>
          <option value="vue">Vue</option>
          <option value="acceptee">Acceptée</option>
          <option value="refusee">Refusée</option>
        </select>
      </div>
      <a href="api.php?action=export_candidatures_csv" class="btn btn-ghost" style="font-size:.82rem">📥 Exporter CSV</a>
    </div>
    <table class="data-table" id="candsTable">
      <thead><tr><th style="width:28px"></th><th>Candidat</th><th>Offre</th><th>Entreprise</th><th>Date</th><th>Statut</th></tr></thead>
      <tbody id="candsTbody"><tr><td colspan="6" style="text-align:center;padding:2rem"><span class="spinner"></span></td></tr></tbody>
    </table>
  </div>

  <!-- ══ VALIDATIONS ════════════════════════════════════════ -->
  <div id="panel-validations" class="tab-panel" style="display:none">
    <h3 class="panel-val-title" style="font-family:'Outfit',sans-serif;color:#fff;margin-bottom:1rem">🧑‍💼 Comptes pilotes en attente</h3>
    <table class="data-table" style="margin-bottom:2rem">
      <thead><tr><th>Nom & Email</th><th>Entreprise demandée</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody id="comptesAttenteTbody"><tr><td colspan="4" style="text-align:center;padding:1.5rem"><span class="spinner"></span></td></tr></tbody>
    </table>
    <h3 class="panel-val-title" style="font-family:'Outfit',sans-serif;color:#fff;margin-bottom:1rem">📋 Offres en attente de publication</h3>
    <table class="data-table">
      <thead><tr><th>Titre</th><th>Pilote</th><th>Entreprise</th><th>Type</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody id="offresAttenteTbody"><tr><td colspan="6" style="text-align:center;padding:1.5rem"><span class="spinner"></span></td></tr></tbody>
    </table>
  </div>

</main>

<div id="toast"></div>

<footer>
  <span class="footer-brand">Web4All Admin</span>
  <span>© 2026 – Accès réservé</span>
</footer>

<script>
const ADMIN_API = '../admin/api.php';
const ADMIN_ID  = <?= (int)$u['id'] ?>;

function e(s){ const d=document.createElement('div'); d.textContent=s??''; return d.innerHTML; }

// ── API ───────────────────────────────────────────────────────
async function api(data){
  const r=await fetch(ADMIN_API,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)});
  return r.json();
}

// ── TOAST ─────────────────────────────────────────────────────
function showToast(msg,type='success'){
  const t=document.getElementById('toast');
  if(!t) return;
  t.textContent=msg;
  t.style.cssText=`position:fixed;bottom:1.5rem;right:1.5rem;padding:.75rem 1.25rem;border-radius:10px;font-size:.85rem;font-weight:600;color:#fff;z-index:9999;transition:opacity .3s;pointer-events:none;max-width:320px;box-shadow:0 4px 20px rgba(0,0,0,.3);background:${type==='success'?'rgba(52,211,153,.9)':'rgba(239,68,68,.9)'};opacity:1`;
  clearTimeout(t._t);
  t._t=setTimeout(()=>{t.style.opacity='0';},3000);
}

// ── LOGOUT ────────────────────────────────────────────────────
function logout(){
  fetch('../php/auth.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'logout'})})
    .then(()=>location.href='../index.php');
}

// ── TABS ──────────────────────────────────────────────────────
const ALL_TABS=['offres','entreprises','utilisateurs','comptes','candidatures','validations'];
const loadedTabs=new Set(['offres']);

function switchTab(name){
  document.querySelectorAll('.admin-tab').forEach(t=>t.classList.toggle('active',t.dataset.tab===name));
  ALL_TABS.forEach(p=>{ const el=document.getElementById('panel-'+p); if(el) el.style.display=p===name?'block':'none'; });
  if(!loadedTabs.has(name)){
    loadedTabs.add(name);
    ({entreprises:loadEntreprises,utilisateurs:loadUsers,comptes:loadComptes,candidatures:loadCands,validations:loadValidations}[name]||(() => {}))();
  }
}
document.querySelectorAll('.admin-tab').forEach(tab=>tab.addEventListener('click',()=>switchTab(tab.dataset.tab)));

// ── STATS ─────────────────────────────────────────────────────
async function loadStats(){
  const s=await api({action:'stats'});
  document.getElementById('sOffres').textContent      = s.offres      ??0;
  document.getElementById('sEntreprises').textContent = s.entreprises ??0;
  document.getElementById('sUsers').textContent       = s.utilisateurs??0;
  document.getElementById('sCands').textContent       = s.candidatures??0;
  const total=(+(s.en_attente_comptes??0))+(+(s.en_attente_offres??0));
  const alEl=document.getElementById('alertValidations');
  if(total>0){
    const parts=[];
    if(s.en_attente_comptes>0) parts.push(s.en_attente_comptes+' compte(s) pilote');
    if(s.en_attente_offres >0) parts.push(s.en_attente_offres+' offre(s)');
    document.getElementById('alertValidationsText').textContent=parts.join(' et ')+' en attente';
    alEl.style.display='block';
    document.getElementById('tabValidations').style.color='#fbbf24';
  } else { alEl.style.display='none'; }
}

// ── COMPÉTENCES ───────────────────────────────────────────────
let allComps=[]; const selComps=new Set();
async function loadCompsSelect(){
  allComps=await api({action:'liste_competences'});
  renderComps();
}
function renderComps(){
  const div=document.getElementById('fCompsList'); if(!div) return;
  div.innerHTML=allComps.map(c=>`
    <label style="display:inline-flex;align-items:center;gap:.25rem;cursor:pointer;padding:.2rem .6rem;border-radius:6px;font-size:.72rem;
      background:${selComps.has(c.id)?'var(--amber-dim)':'var(--surface2)'};
      border:1px solid ${selComps.has(c.id)?'var(--border-hi)':'rgba(255,255,255,.06)'};
      color:${selComps.has(c.id)?'var(--amber)':'var(--muted)'}">
      <input type="checkbox" ${selComps.has(c.id)?'checked':''} style="display:none" onchange="toggleComp(${c.id})">${e(c.nom)}
    </label>`).join('');
}
function toggleComp(id){ selComps.has(id)?selComps.delete(id):selComps.add(id); renderComps(); }

// ── SELECT ENTREPRISES (formulaire offre) ─────────────────────
async function loadEntreprisesSelect(){
  const ents=await api({action:'liste_entreprises'});
  const sel=document.getElementById('fEntrepriseId');
  sel.innerHTML='<option value="">— Choisir —</option>'
    +ents.map(ent=>`<option value="${ent.id}">${e(ent.nom)} – ${e(ent.ville||'')}</option>`).join('');
}

// ══ OFFRES ════════════════════════════════════════════════════
async function loadOffres(){
  const offres=await api({action:'liste_offres'});
  const labels={validee:'Validée',en_attente:'En attente',refusee:'Refusée'};
  const cls   ={validee:'badge-remun',en_attente:'badge-attente',refusee:'statut-refusee'};
  document.getElementById('offresTbody').innerHTML=offres.map(o=>`
    <tr>
      <td><strong>${e(o.titre)}</strong></td>
      <td>${e(o.entreprise_nom||'')}</td>
      <td>${e(o.lieu)}</td>
      <td><span class="badge ${o.type==='stage'?'badge-stage':'badge-alt'}">${o.type}</span></td>
      <td><span class="badge ${cls[o.statut_validation]||''}">${labels[o.statut_validation]||o.statut_validation}</span></td>
      <td><span class="badge ${o.actif?'badge-remun':'statut-refusee'}">${o.actif?'Oui':'Non'}</span></td>
      <td style="display:flex;gap:.35rem">
        <button class="btn" style="font-size:.72rem;padding:.28rem .65rem"
                onclick='editOffre(${JSON.stringify({id:o.id,titre:o.titre,entreprise_id:o.eid,lieu:o.lieu,type:o.type,description:o.description,duree:o.duree||"",remuneration:o.remuneration||"",competences:o.competences||[]}).replace(/'/g,"&#39;")})'>✏️</button>
        <button class="btn btn-danger" style="font-size:.72rem;padding:.28rem .65rem" onclick="deleteOffre(${o.id})">🗑</button>
      </td>
    </tr>`).join('')||'<tr><td colspan="7" style="text-align:center;color:var(--muted)">Aucune offre.</td></tr>';
}

function editOffre(o){
  document.getElementById('offreId').value       = o.id;
  document.getElementById('fTitre').value        = o.titre;
  document.getElementById('fEntrepriseId').value = o.entreprise_id;
  document.getElementById('fLieu').value         = o.lieu;
  document.getElementById('fType').value         = o.type;
  document.getElementById('fDesc').value         = o.description;
  document.getElementById('fDuree').value        = o.duree||'';
  document.getElementById('fRemun').value        = o.remuneration||'';
  selComps.clear();
  (o.competences||[]).forEach(c=>selComps.add(c.id));
  renderComps();
  document.getElementById('offreFormTitle').textContent='✏️ Modifier l\'offre';
  document.getElementById('offreForm').scrollIntoView({behavior:'smooth',block:'start'});
}

function resetOffreForm(){
  document.getElementById('offreId').value='';
  ['fTitre','fDesc','fDuree','fRemun'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('fEntrepriseId').value='';
  document.getElementById('fLieu').value='Rouen';
  document.getElementById('fType').value='stage';
  selComps.clear(); renderComps();
  document.getElementById('offreFormTitle').textContent='➕ Nouvelle offre';
  document.getElementById('offreError').className='form-alert error';
  document.getElementById('offreSuccess').className='form-alert success';
}

document.getElementById('resetOffreBtn').addEventListener('click',resetOffreForm);
document.getElementById('saveOffreBtn').addEventListener('click',async()=>{
  const errEl=document.getElementById('offreError'), okEl=document.getElementById('offreSuccess');
  errEl.className=okEl.className='form-alert';
  const id=document.getElementById('offreId').value;
  const res=await api({
    action:id?'modifier_offre':'creer_offre', id,
    titre:        document.getElementById('fTitre').value.trim(),
    entreprise_id:document.getElementById('fEntrepriseId').value,
    lieu:         document.getElementById('fLieu').value,
    type:         document.getElementById('fType').value,
    description:  document.getElementById('fDesc').value.trim(),
    duree:        document.getElementById('fDuree').value.trim(),
    remuneration: document.getElementById('fRemun').value.trim(),
    competences:  [...selComps], actif:1,
  });
  if(!res.ok){ errEl.textContent=res.msg; errEl.className+=' error show'; }
  else { showToast(res.msg,'success'); resetOffreForm(); await loadOffres(); await loadStats(); }
});

async function deleteOffre(id){
  if(!confirm('Supprimer cette offre définitivement ?')) return;
  const res=await api({action:'supprimer_offre',id});
  if(res.ok){ showToast('Offre supprimée.','success'); await loadOffres(); await loadStats(); }
  else showToast(res.msg||'Erreur.','error');
}

// ══ ENTREPRISES ═══════════════════════════════════════════════
async function loadEntreprises(){
  const ents=await api({action:'liste_entreprises'});
  document.getElementById('entsTbody').innerHTML=ents.map(ent=>`
    <tr>
      <td><strong>${e(ent.nom)}</strong></td>
      <td>${e(ent.secteur||'–')}</td>
      <td>${e(ent.ville||'–')}</td>
      <td style="text-align:center">${ent.nb_offres}</td>
      <td><a href="mailto:${e(ent.email_contact||'')}" style="color:var(--amber);font-size:.78rem">${e(ent.email_contact||'–')}</a></td>
      <td style="display:flex;gap:.35rem">
        <button class="btn" style="font-size:.72rem;padding:.28rem .65rem"
                onclick='editEnt(${JSON.stringify({id:ent.id,nom:ent.nom,secteur:ent.secteur||"",ville:ent.ville||"",code_postal:ent.code_postal||"",email_contact:ent.email_contact||"",telephone:ent.telephone||"",site_web:ent.site_web||"",description:ent.description||""}).replace(/'/g,"&#39;")})'>✏️</button>
        <button class="btn btn-danger" style="font-size:.72rem;padding:.28rem .65rem" onclick="deleteEnt(${ent.id})">🗑</button>
      </td>
    </tr>`).join('')||'<tr><td colspan="6" style="text-align:center;color:var(--muted)">Aucune entreprise.</td></tr>';
}

function editEnt(ent){
  document.getElementById('entId').value    = ent.id;
  document.getElementById('eNom').value     = ent.nom;
  document.getElementById('eSecteur').value = ent.secteur;
  document.getElementById('eVille').value   = ent.ville;
  document.getElementById('eCP').value      = ent.code_postal;
  document.getElementById('eEmail').value   = ent.email_contact;
  document.getElementById('eTel').value     = ent.telephone;
  document.getElementById('eSite').value    = ent.site_web;
  document.getElementById('eDesc').value    = ent.description;
  document.getElementById('entFormTitle').textContent='✏️ Modifier l\'entreprise';
  document.getElementById('eNom').scrollIntoView({behavior:'smooth',block:'center'});
}
function resetEntForm(){
  document.getElementById('entId').value='';
  ['eNom','eSecteur','eVille','eCP','eEmail','eTel','eSite','eDesc'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('entFormTitle').textContent='➕ Nouvelle entreprise';
  document.getElementById('entError').className='form-alert error';
  document.getElementById('entSuccess').className='form-alert success';
}
document.getElementById('resetEntBtn').addEventListener('click',resetEntForm);
document.getElementById('saveEntBtn').addEventListener('click',async()=>{
  const errEl=document.getElementById('entError'), okEl=document.getElementById('entSuccess');
  errEl.className=okEl.className='form-alert';
  const id=document.getElementById('entId').value;
  const nom=document.getElementById('eNom').value.trim();
  if(!nom){ errEl.textContent='Le nom est obligatoire.'; errEl.className+=' error show'; return; }
  const res=await api({
    action:id?'modifier_entreprise':'creer_entreprise', id, nom,
    secteur:      document.getElementById('eSecteur').value.trim(),
    ville:        document.getElementById('eVille').value.trim(),
    code_postal:  document.getElementById('eCP').value.trim(),
    email_contact:document.getElementById('eEmail').value.trim(),
    telephone:    document.getElementById('eTel').value.trim(),
    site_web:     document.getElementById('eSite').value.trim(),
    description:  document.getElementById('eDesc').value.trim(),
    pays:'France',
  });
  if(!res.ok){ errEl.textContent=res.msg; errEl.className+=' error show'; }
  else { showToast(res.msg,'success'); resetEntForm(); await loadEntreprises(); await loadEntreprisesSelect(); await loadStats(); }
});
async function deleteEnt(id){
  if(!confirm('Supprimer cette entreprise et toutes ses offres ?')) return;
  const res=await api({action:'supprimer_entreprise',id});
  if(res.ok){ showToast('Entreprise supprimée.','success'); await loadEntreprises(); await loadEntreprisesSelect(); await loadStats(); }
  else showToast(res.msg||'Erreur.','error');
}

// ══ UTILISATEURS ══════════════════════════════════════════════
async function loadUsers(){
  const users=await api({action:'liste_utilisateurs'});
  document.getElementById('usersTbody').innerHTML=users.map(u=>`
    <tr>
      <td><strong>${e(u.prenom+' '+u.nom)}</strong></td>
      <td style="font-size:.8rem">${e(u.email)}</td>
      <td>
        <select onchange="changerRole(${u.id},this.value)"
                style="background:var(--surface2);border:1px solid var(--border);color:var(--text);padding:.25rem .5rem;border-radius:6px;font-size:.75rem"
                ${u.role==='admin'?'disabled':''}>
          ${['etudiant','pilote','admin'].map(r=>`<option value="${r}" ${u.role===r?'selected':''}>${r}</option>`).join('')}
        </select>
      </td>
      <td style="font-size:.78rem;color:var(--muted)">${e(u.entreprise_nom||u.promo_nom||'–')}</td>
      <td style="font-size:.78rem">${new Date(u.created_at).toLocaleDateString('fr-FR')}</td>
      <td style="text-align:center">${u.nb_cands}</td>
      <td>${u.role!=='admin'?`<button class="btn btn-danger" style="font-size:.72rem;padding:.28rem .65rem" onclick="deleteUser(${u.id})">🗑</button>`:'–'}</td>
    </tr>`).join('')||'<tr><td colspan="7" style="text-align:center;color:var(--muted)">Aucun utilisateur.</td></tr>';
}
async function changerRole(id,role){
  const res=await api({action:'changer_role',id,role});
  if(!res.ok) showToast(res.msg||'Erreur.','error');
  else showToast('Rôle mis à jour.','success');
}
async function deleteUser(id){
  if(!confirm('Supprimer cet utilisateur ?')) return;
  const res=await api({action:'supprimer_utilisateur',id});
  if(res.ok){ showToast('Utilisateur supprimé.','success'); await loadUsers(); await loadStats(); }
}

// ══ COMPTES ═══════════════════════════════════════════════════
let allComptes=[], filtreActuel='tous';
async function loadComptes(){
  allComptes=await api({action:'liste_utilisateurs'});
  renderComptes();
}
function filterComptes(f){
  filtreActuel=f;
  document.querySelectorAll('.filter-btn').forEach(b=>b.classList.remove('active'));
  document.getElementById({tous:'fTous',actif:'fActif',en_attente:'fAttente',suspendu:'fSuspendu'}[f])?.classList.add('active');
  renderComptes();
}
function renderComptes(){
  const liste=filtreActuel==='tous'?allComptes:allComptes.filter(u=>u.statut===filtreActuel);
  const sClass={actif:'badge-actif',en_attente:'badge-attente',suspendu:'badge-suspendu'};
  const sLabel={actif:'Actif',en_attente:'En attente',suspendu:'Suspendu'};
  const rClass={admin:'badge-stage',pilote:'role-pilote',etudiant:'role-etudiant'};
  document.getElementById('comptesTbody').innerHTML=liste.map(u=>`
    <tr>
      <td><strong>${e(u.prenom+' '+u.nom)}</strong><br><small style="color:var(--muted)">${e(u.email)}</small></td>
      <td><span class="role-badge ${rClass[u.role]||''}">${u.role}</span></td>
      <td style="font-size:.78rem;color:var(--muted)">${e(u.entreprise_nom||u.promo_nom||'–')}</td>
      <td style="font-size:.78rem">${new Date(u.created_at).toLocaleDateString('fr-FR')}</td>
      <td><span class="statut-badge ${sClass[u.statut]||''}">${sLabel[u.statut]||u.statut}</span></td>
      <td>
        ${u.role!=='admin'?`<div style="display:flex;gap:.35rem;flex-wrap:wrap">
          ${u.statut!=='actif'?`<button class="btn" style="font-size:.72rem;padding:.28rem .65rem;color:#34d399;border-color:rgba(52,211,153,.4)" onclick="changerStatutCompte(${u.id},'actif')">✓ Activer</button>`:''}
          ${u.statut==='actif'?`<button class="btn btn-danger" style="font-size:.72rem;padding:.28rem .65rem" onclick="changerStatutCompte(${u.id},'suspendu')">⊘ Suspendre</button>`:''}
          <button class="btn btn-ghost" style="font-size:.72rem;padding:.28rem .65rem" onclick="supprimerCompte(${u.id})">🗑</button>
        </div>`:'–'}
      </td>
    </tr>`).join('')||'<tr><td colspan="6" style="text-align:center;color:var(--muted)">Aucun compte.</td></tr>';
}
async function changerStatutCompte(id,statut){
  if(statut==='suspendu'&&!confirm('Suspendre ce compte ?')) return;
  const res=await api({action:'changer_statut_utilisateur',id,statut});
  if(res.ok){ showToast('Statut mis à jour.','success'); await loadComptes(); await loadStats(); }
}
async function supprimerCompte(id){
  if(!confirm('Supprimer définitivement ce compte ?')) return;
  await api({action:'supprimer_utilisateur',id});
  await loadComptes(); await loadStats();
}

// ══ CANDIDATURES ══════════════════════════════════════════════
let allCands=[];
const sLabel={envoyee:'Envoyée',vue:'Vue',acceptee:'Acceptée',refusee:'Refusée'};
const sClass={envoyee:'statut-envoyee',vue:'statut-vue',acceptee:'statut-acceptee',refusee:'statut-refusee'};

async function loadCands(){
  allCands=await api({action:'liste_candidatures'});
  renderCands(allCands);
}
function filterCands(){
  const q=(document.getElementById('candSearch')?.value||'').toLowerCase();
  const st=document.getElementById('candStatutFilter')?.value||'';
  renderCands(allCands.filter(c=>
    (!q||(c.prenom+' '+c.nom).toLowerCase().includes(q)||c.email.toLowerCase().includes(q)||(c.offre_titre||'').toLowerCase().includes(q)||(c.entreprise||'').toLowerCase().includes(q))
    &&(!st||c.statut===st)
  ));
}
function renderCands(liste){
  const tbody=document.getElementById('candsTbody');
  if(!liste.length){
    tbody.innerHTML='<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--muted)">Aucune candidature.</td></tr>';
    return;
  }
  tbody.innerHTML=liste.map(c=>`
    <tr class="main-row" onclick="toggleDetail('det-${c.id}',this)">
      <td style="text-align:center;color:var(--muted)" id="arrow-${c.id}">▶</td>
      <td><strong>${e(c.prenom+' '+c.nom)}</strong><br><small style="color:var(--muted)">${e(c.email)}</small></td>
      <td style="font-size:.82rem"><a href="../offre.php?id=${c.offre_id||''}" target="_blank" style="color:var(--accent);text-decoration:none" onclick="event.stopPropagation()">${e(c.offre_titre)}</a></td>
      <td style="font-size:.78rem">${e(c.entreprise||'–')}</td>
      <td style="font-size:.78rem">${new Date(c.created_at).toLocaleDateString('fr-FR')}</td>
      <td>
        <select onchange="event.stopPropagation();changeStatut(${c.id},this.value)"
                style="background:var(--surface2);border:1px solid var(--border);color:var(--text);padding:.25rem .5rem;border-radius:6px;font-size:.75rem">
          ${['envoyee','vue','acceptee','refusee'].map(s=>`<option value="${s}" ${c.statut===s?'selected':''}>${sLabel[s]}</option>`).join('')}
        </select>
      </td>
    </tr>
    <tr class="detail-row" id="det-${c.id}" style="display:none;background:var(--surface2)">
      <td></td>
      <td colspan="5" style="padding:1.25rem 1rem">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
          <div>
            <p style="font-size:.7rem;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:.6rem">Candidat</p>
            <p><strong>${e(c.prenom+' '+c.nom)}</strong></p>
            <p style="font-size:.82rem;margin:.2rem 0"><a href="mailto:${e(c.email)}" style="color:var(--accent)">${e(c.email)}</a></p>
            <div style="display:flex;gap:.4rem;margin-top:.75rem;flex-wrap:wrap">
              <a href="../profil.php" target="_blank" class="btn btn-ghost" style="font-size:.75rem;padding:.3rem .7rem">👤 Profil</a>
              <a href="../messages.php?with=${c.utilisateur_id}&name=${encodeURIComponent((c.prenom||'')+' '+(c.nom||''))}&role=etudiant"
                 class="btn btn-ghost" style="font-size:.75rem;padding:.3rem .7rem">💬 Message</a>
            </div>
            ${c.message?`<div style="margin-top:1rem">
              <p style="font-size:.7rem;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:.4rem">Motivation</p>
              <div style="background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:.8rem;font-size:.82rem;line-height:1.6;white-space:pre-wrap">${e(c.message)}</div>
            </div>`:''}
          </div>
          <div>
            <p style="font-size:.7rem;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:.6rem">Offre</p>
            <p><a href="../offre.php?id=${c.offre_id||''}" target="_blank" style="color:var(--accent);font-weight:600">${e(c.offre_titre)}</a></p>
            <p style="font-size:.82rem;color:var(--muted);margin:.2rem 0">${e(c.entreprise||'')} · ${e(c.lieu||'')}
              <span class="badge ${c.type==='stage'?'badge-stage':'badge-alt'}" style="font-size:.68rem;margin-left:.3rem">${c.type}</span>
            </p>
            <p style="font-size:.7rem;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin:1rem 0 .5rem">Fichiers</p>
            <div style="display:flex;gap:.4rem;flex-wrap:wrap">
              ${c.cv_path
                ?`<a href="../uploads/${e(c.cv_path)}" target="_blank" class="btn" style="font-size:.75rem;padding:.32rem .75rem">📄 Voir CV</a>
                   <a href="../uploads/${e(c.cv_path)}" download class="btn btn-ghost" style="font-size:.75rem;padding:.32rem .75rem">⬇ DL CV</a>`
                :'<span style="font-size:.8rem;color:var(--muted)">Pas de CV</span>'}
              ${c.lm_path
                ?`<a href="../uploads/${e(c.lm_path)}" target="_blank" class="btn" style="font-size:.75rem;padding:.32rem .75rem">✉️ Voir LM</a>
                   <a href="../uploads/${e(c.lm_path)}" download class="btn btn-ghost" style="font-size:.75rem;padding:.32rem .75rem">⬇ DL LM</a>`
                :''}
            </div>
            <p style="font-size:.7rem;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin:1rem 0 .5rem">Statut</p>
            <div style="display:flex;gap:.35rem;flex-wrap:wrap">
              ${['envoyee','vue','acceptee','refusee'].map(s=>`
                <button id="sbtn-${c.id}-${s}"
                        class="btn ${c.statut===s?'btn-primary':'btn-ghost'}"
                        style="font-size:.75rem;padding:.32rem .75rem"
                        onclick="event.stopPropagation();changeStatut(${c.id},'${s}');updateStatutRow(${c.id},'${s}')">
                  ${sLabel[s]}
                </button>`).join('')}
            </div>
          </div>
        </div>
      </td>
    </tr>`).join('');
}

function toggleDetail(id,row){
  const det=document.getElementById(id); if(!det) return;
  const open=det.style.display==='table-row';
  det.style.display=open?'none':'table-row';
  const arrow=document.getElementById('arrow-'+id.replace('det-',''));
  if(arrow) arrow.textContent=open?'▶':'▼';
}
function updateStatutRow(cid,st){
  const sel=document.querySelector(`tr.main-row select[onchange*="changeStatut(${cid},"]`);
  if(sel) sel.value=st;
  ['envoyee','vue','acceptee','refusee'].forEach(s=>{
    const btn=document.getElementById(`sbtn-${cid}-${s}`);
    if(btn) btn.className='btn '+(s===st?'btn-primary':'btn-ghost');
  });
}
async function changeStatut(id,statut){
  const res=await api({action:'statut_candidature',id,statut});
  if(res.ok){ showToast('Statut : '+sLabel[statut],'success'); await loadStats(); }
}

// ══ VALIDATIONS ═══════════════════════════════════════════════
async function loadValidations(){
  const comptes=await api({action:'comptes_attente'});
  document.getElementById('comptesAttenteTbody').innerHTML=comptes.map(u=>`
    <tr>
      <td><strong>${e(u.prenom+' '+u.nom)}</strong><br><small style="color:var(--muted)">${e(u.email)}</small></td>
      <td><strong style="color:var(--amber)">${e(u.nom_entreprise_demande||u.entreprise_nom||'–')}</strong></td>
      <td style="font-size:.78rem">${new Date(u.created_at).toLocaleDateString('fr-FR')}</td>
      <td style="display:flex;gap:.35rem">
        <button class="btn" style="font-size:.72rem;padding:.28rem .65rem;color:#34d399;border-color:rgba(52,211,153,.4)" onclick="validerCompte(${u.id},'actif')">✓ Valider</button>
        <button class="btn btn-danger" style="font-size:.72rem;padding:.28rem .65rem" onclick="validerCompte(${u.id},'suspendu')">✕ Refuser</button>
      </td>
    </tr>`).join('')||'<tr><td colspan="4" style="text-align:center;padding:1.5rem;color:var(--muted)">Aucun compte en attente.</td></tr>';

  const offres=await api({action:'offres_attente'});
  document.getElementById('offresAttenteTbody').innerHTML=offres.map(o=>`
    <tr>
      <td><strong>${e(o.titre)}</strong><br><small style="color:var(--muted)">${e((o.description||'').substring(0,70))}…</small></td>
      <td>${e((o.pilote_prenom||'')+' '+(o.pilote_nom||''))}</td>
      <td>${e(o.entreprise_nom||'–')}</td>
      <td><span class="badge ${o.type==='stage'?'badge-stage':'badge-alt'}">${o.type}</span></td>
      <td style="font-size:.78rem">${new Date(o.created_at).toLocaleDateString('fr-FR')}</td>
      <td style="display:flex;gap:.35rem">
        <button class="btn" style="font-size:.72rem;padding:.28rem .65rem;color:#34d399;border-color:rgba(52,211,153,.4)" onclick="validerOffre(${o.id},'validee')">✓ Publier</button>
        <button class="btn btn-danger" style="font-size:.72rem;padding:.28rem .65rem" onclick="validerOffre(${o.id},'refusee')">✕ Refuser</button>
      </td>
    </tr>`).join('')||'<tr><td colspan="6" style="text-align:center;padding:1.5rem;color:var(--muted)">Aucune offre en attente.</td></tr>';
}
async function validerCompte(id,statut){
  const res=await api({action:'valider_compte',id,statut});
  showToast(res.msg||'Fait.',res.ok?'success':'error');
  await loadValidations(); await loadStats(); await loadComptes();
}
async function validerOffre(id,statut){
  const res=await api({action:'valider_offre',id,statut});
  showToast(res.msg||(statut==='validee'?'Offre publiée.':'Offre refusée.'),res.ok?'success':'error');
  await loadValidations(); await loadStats(); await loadOffres();
}

// ── INIT ──────────────────────────────────────────────────────
(async()=>{
  await loadCompsSelect();
  await loadEntreprisesSelect();
  await loadStats();
  await loadOffres();
  if(typeof refreshMsgBadge==='function') refreshMsgBadge();
})();
</script>
</body>
</html>
