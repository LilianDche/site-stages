<?php
require_once '../php/config.php';
requirePilote();
$u = currentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Espace Pilote – Web4All</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <script>window.SITE_URL="<?= SITE_URL ?>";</script>
<script src="../js/script.js"></script>
  <script>(function(){var t=localStorage.getItem("w4a_theme")||"dark";document.documentElement.setAttribute("data-theme",t);})();</script>
</head>
<body>

<header class="big-header">
  <h1>Espace <span class="accent">Pilote</span></h1>
  <p class="header-sub">
    <?= e($u['entreprise_nom'] ?? 'Votre espace de gestion') ?>
  </p>
</header>

<nav class="navbar" id="navbar">
  <a href="../index.php">← Site</a>
  <a href="../entreprises.php">Entreprises</a>
  <a href="index.php" id="on">Pilote</a>
  <a href="demande.php" style="color:var(--amber)">+ Soumettre offre</a>
  <?php if(isAdmin()):?><a href="../admin/index.php">Admin</a><?php endif;?>
  <div class="nav-right">
    <span class="nav-user">
      <span class="role-badge role-<?= e($u['role']) ?>"><?= ucfirst(e($u['role'])) ?></span>
      <?= e($u['prenom'].' '.$u['nom']) ?>
    </span>
  <button class="theme-toggle" onclick="toggleTheme()" id="themeToggle" style="margin-right:.5rem">🌸 Mode clair</button>
    <button class="nav-btn" onclick="fetch('../php/auth.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'logout'})}).then(()=>location.href='../index.php')">Déconnexion</button>
  </div>
</nav>

<main class="container main-content">

  <!-- STATS -->
  <div class="admin-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:1.5rem">
    <div class="stat-card"><span class="stat-icon">📋</span><div><div class="stat-big" id="sOffres">–</div><div class="stat-lbl">Mes offres actives</div></div></div>
    <div class="stat-card"><span class="stat-icon">📨</span><div><div class="stat-big" id="sCandidatures">–</div><div class="stat-lbl">Candidatures reçues</div></div></div>
    <div class="stat-card"><span class="stat-icon">⏳</span><div><div class="stat-big" id="sEnAttente">–</div><div class="stat-lbl">En attente validation</div></div></div>
  </div>

  <!-- ONGLETS -->
  <div class="page-tabs">
    <button class="page-tab active" data-tab="offres">📋 Mes offres</button>
    <button class="page-tab" data-tab="entreprises">🏢 Entreprises</button>
    <button class="page-tab" data-tab="promotions">🎓 Promotions</button>
    <button class="page-tab" data-tab="candidatures">📨 Candidatures</button>
  </div>

  <!-- ══ MES OFFRES ══════════════════════════════════════════════ -->
  <div class="tab-panel active" id="panel-offres">

    <div class="admin-form" id="offreForm">
      <h3 id="offreFormTitle">➕ Nouvelle offre</h3>
      <input type="hidden" id="offreId">
      <p style="font-size:.82rem;color:var(--muted);margin-bottom:1rem">
        Entreprise : <strong style="color:var(--amber)"><?= e($u['entreprise_nom'] ?? '–') ?></strong>
      </p>
      <div class="form-alert error"   id="offreError"></div>
      <div class="form-alert success" id="offreSuccess"></div>

      <div class="form-row">
        <div class="form-group"><label>Titre *</label><input type="text" id="fTitre"></div>
        <div class="form-group"><label>Type *</label>
          <select id="fType"><option value="stage">Stage</option><option value="alternance">Alternance</option></select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Lieu *</label>
          <select id="fLieu">
            <option>Rouen</option><option>Le Havre</option>
            <option>Caen</option><option>Paris</option><option>Lille</option>
          </select>
        </div>
        <div class="form-group"><label>Durée</label><input type="text" id="fDuree" placeholder="ex: 6 mois"></div>
      </div>
      <div class="form-group"><label>Rémunération</label><input type="text" id="fRemun" placeholder="ex: 600 €/mois"></div>
      <div class="form-group"><label>Description *</label><textarea id="fDesc" rows="4"></textarea></div>
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
      <thead><tr><th>Titre</th><th>Lieu</th><th>Type</th><th>Durée</th><th>Validation</th><th>Actions</th></tr></thead>
      <tbody id="offresTbody"><tr><td colspan="6" style="text-align:center;padding:2rem"><span class="spinner"></span></td></tr></tbody>
    </table>
  </div>

  <!-- ══ ENTREPRISES ════════════════════════════════════════════ -->
  <div class="tab-panel" id="panel-entreprises" style="display:none">

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

  <!-- ══ PROMOTIONS ════════════════════════════════════════════ -->
  <div class="tab-panel" id="panel-promotions" style="display:none">
    <div id="promosContainer" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(420px,1fr));gap:1rem"></div>
  </div>

  <!-- ══ CANDIDATURES ══════════════════════════════════════════ -->
  <div class="tab-panel" id="panel-candidatures" style="display:none">
    <table class="data-table">
      <thead><tr><th>Candidat</th><th>Offre</th><th>Date</th><th>Fichiers</th><th>Statut</th></tr></thead>
      <tbody id="candsTbody"><tr><td colspan="5" style="text-align:center;padding:2rem"><span class="spinner"></span></td></tr></tbody>
    </table>
  </div>

</main>

<div id="toast" style="position:fixed;bottom:1.5rem;right:1.5rem;padding:.75rem 1.25rem;border-radius:10px;font-size:.85rem;font-weight:600;opacity:0;transition:opacity .3s;pointer-events:none;z-index:9999"></div>

<footer><span class="footer-brand">Web4All</span><span>© 2026 – Espace Pilote</span></footer>

<script>
const ADMIN_API  = '../admin/api.php';
const CURRENT_USER_ID = <?= (int)$u['id'] ?>;
const CURRENT_ENT_ID  = <?= (int)($u['entreprise_id'] ?? 0) ?>;
const IS_ADMIN        = <?= isAdmin() ? 'true' : 'false' ?>;
const ENT_API   = '../php/entreprises.php';
const PROMO_API = '../php/promotions.php';

function e(s){ const d=document.createElement('div'); d.textContent=s??''; return d.innerHTML; }

async function adminCall(data){
  const r=await fetch(ADMIN_API,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)});
  return r.json();
}
async function entCall(data){
  const r=await fetch('../php/entreprises.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)});
  return r.json();
}
async function get(url,p={}){
  const qs=new URLSearchParams(p).toString();
  return (await fetch(qs?`${url}?${qs}`:url)).json();
}

// ── TABS ──────────────────────────────────────────────────────
const ALL=['offres','entreprises','promotions','candidatures'];
let loaded=new Set(['offres']);

function switchTab(name){
  document.querySelectorAll('.page-tab').forEach(t=>t.classList.toggle('active',t.dataset.tab===name));
  ALL.forEach(p=>{ const el=document.getElementById('panel-'+p); if(el) el.style.display=p===name?'block':'none'; });
  if(!loaded.has(name)){
    loaded.add(name);
    ({entreprises:loadEnts,promotions:loadPromos,candidatures:loadCands}[name]||(() => {}))();
  }
}
document.querySelectorAll('.page-tab').forEach(t=>t.addEventListener('click',()=>switchTab(t.dataset.tab)));

// ── STATS ─────────────────────────────────────────────────────
async function loadStats(){
  const s=await adminCall({action:'stats_pilote'});
  document.getElementById('sOffres').textContent      = s.offres       ??0;
  document.getElementById('sCandidatures').textContent= s.candidatures ??0;
  document.getElementById('sEnAttente').textContent   = s.en_attente   ??0;
}

// ── COMPÉTENCES ───────────────────────────────────────────────
let allComps=[]; const selComps=new Set();
async function loadComps(){
  allComps=await get('../php/competences.php');
  renderComps();
}
function renderComps(){
  const div=document.getElementById('fCompsList'); if(!div)return;
  div.innerHTML=allComps.map(c=>`
    <label style="display:inline-flex;align-items:center;gap:.25rem;cursor:pointer;padding:.2rem .6rem;border-radius:6px;font-size:.72rem;
      background:${selComps.has(c.id)?'var(--amber-dim)':'var(--surface2)'};
      border:1px solid ${selComps.has(c.id)?'var(--border-hi)':'rgba(255,255,255,.06)'};
      color:${selComps.has(c.id)?'var(--amber)':'var(--muted)'}">
      <input type="checkbox" ${selComps.has(c.id)?'checked':''} style="display:none" onchange="toggleComp(${c.id})">${e(c.nom)}
    </label>`).join('');
}
function toggleComp(id){ selComps.has(id)?selComps.delete(id):selComps.add(id); renderComps(); }

// ══ OFFRES ════════════════════════════════════════════════════
async function loadOffres(){
  const offres=await adminCall({action:'liste_offres_pilote'});
  const labels={validee:'✓ Publiée',en_attente:'⏳ En attente',refusee:'✕ Refusée'};
  const cls   ={validee:'badge-remun',en_attente:'badge-attente',refusee:'statut-refusee'};
  document.getElementById('offresTbody').innerHTML=offres.map(o=>`
    <tr>
      <td><strong>${e(o.titre)}</strong></td>
      <td>${e(o.lieu)}</td>
      <td><span class="badge ${o.type==='stage'?'badge-stage':'badge-alt'}">${o.type}</span></td>
      <td style="font-size:.78rem">${e(o.duree||'–')}</td>
      <td><span class="statut-badge ${cls[o.statut_validation]||''}" style="display:inline-flex;padding:.18rem .65rem;border-radius:100px;font-size:.7rem;font-weight:600">${labels[o.statut_validation]||o.statut_validation}</span></td>
      <td style="display:flex;gap:.35rem">
        ${o.created_by == CURRENT_USER_ID || IS_ADMIN ? `
          <button class="btn" style="font-size:.72rem;padding:.28rem .65rem"
                  onclick='editOffre(${JSON.stringify({id:o.id,titre:o.titre,lieu:o.lieu,type:o.type,description:o.description,duree:o.duree||"",remuneration:o.remuneration||"",competences:o.competences||[]}).replace(/'/g,"&#39;")})'>✏️ Modifier</button>
          <button class="btn btn-danger" style="font-size:.72rem;padding:.28rem .65rem"
                  onclick="deleteOffre(${o.id})">🗑 Supprimer</button>
        ` : '<span style="font-size:.75rem;color:var(--muted)">— lecture seule</span>'}
      </td>
    </tr>`).join('')||'<tr><td colspan="6" style="text-align:center;color:var(--muted)">Aucune offre.</td></tr>';
}

function editOffre(o){
  document.getElementById('offreId').value = o.id;
  document.getElementById('fTitre').value  = o.titre;
  document.getElementById('fLieu').value   = o.lieu;
  document.getElementById('fType').value   = o.type;
  document.getElementById('fDesc').value   = o.description;
  document.getElementById('fDuree').value  = o.duree;
  document.getElementById('fRemun').value  = o.remuneration;
  selComps.clear();
  (o.competences||[]).forEach(c=>selComps.add(c.id));
  renderComps();
  document.getElementById('offreFormTitle').textContent='✏️ Modifier l\'offre';
  document.getElementById('offreForm').scrollIntoView({behavior:'smooth',block:'start'});
}
function resetOffreForm(){
  document.getElementById('offreId').value='';
  ['fTitre','fDesc','fDuree','fRemun'].forEach(id=>document.getElementById(id).value='');
  selComps.clear(); renderComps();
  document.getElementById('offreFormTitle').textContent='➕ Nouvelle offre';
  document.getElementById('offreError').className='form-alert error';
  document.getElementById('offreSuccess').className='form-alert success';
}
document.getElementById('resetOffreBtn').addEventListener('click',resetOffreForm);

document.getElementById('saveOffreBtn').addEventListener('click',async()=>{
  const errEl=document.getElementById('offreError');
  const okEl =document.getElementById('offreSuccess');
  errEl.className=okEl.className='form-alert';
  const id=document.getElementById('offreId').value;

  // Le pilote modifie via admin API (garde son entreprise_id)
  // Pour créer, il passe par demande_offre (soumission à validation)
  if(id){
    const res=await adminCall({
      action:'modifier_offre', id,
      titre:       document.getElementById('fTitre').value.trim(),
      entreprise_id: <?= (int)($u['entreprise_id'] ?? 0) ?>,
      lieu:        document.getElementById('fLieu').value,
      type:        document.getElementById('fType').value,
      description: document.getElementById('fDesc').value.trim(),
      duree:       document.getElementById('fDuree').value.trim(),
      remuneration:document.getElementById('fRemun').value.trim(),
      competences: [...selComps], actif:1,
    });
    if(!res.ok){ errEl.textContent=res.msg; errEl.className+=' error show'; }
    else { showToast(res.msg,'success'); resetOffreForm(); await loadOffres(); await loadStats(); }
  } else {
    // Nouvelle offre → via demande (soumission)
    const res=await fetch('../php/demande_offre.php',{
      method:'POST',headers:{'Content-Type':'application/json'},
      body:JSON.stringify({
        action:'soumettre',
        titre:       document.getElementById('fTitre').value.trim(),
        description: document.getElementById('fDesc').value.trim(),
        type:        document.getElementById('fType').value,
        lieu:        document.getElementById('fLieu').value,
        duree:       document.getElementById('fDuree').value.trim(),
        remuneration:document.getElementById('fRemun').value.trim(),
        competences: [...selComps],
      })
    }).then(r=>r.json());
    if(!res.ok){ errEl.textContent=res.msg; errEl.className+=' error show'; }
    else { showToast(res.msg+' ⏳ En attente de validation.','success'); resetOffreForm(); await loadOffres(); await loadStats(); }
  }
});

async function deleteOffre(id){
  if(!confirm('Supprimer cette offre définitivement ?')) return;
  const res=await adminCall({action:'supprimer_offre',id});
  if(res.ok){ showToast('Offre supprimée.','success'); await loadOffres(); await loadStats(); }
  else showToast(res.msg||'Erreur.','error');
}

// ══ ENTREPRISES ═══════════════════════════════════════════════
async function loadEnts(){
  const ents=await entCall({action:'liste'});
  document.getElementById('entsTbody').innerHTML=ents.map(ent=>`
    <tr>
      <td><strong>${e(ent.nom)}</strong></td>
      <td>${e(ent.secteur||'–')}</td>
      <td>${e(ent.ville||'–')}</td>
      <td style="text-align:center">${ent.nb_offres}</td>
      <td style="font-size:.78rem"><a href="mailto:${e(ent.email_contact||'')}" style="color:var(--amber)">${e(ent.email_contact||'–')}</a></td>
      <td style="display:flex;gap:.35rem">
        ${ent.id == CURRENT_ENT_ID || IS_ADMIN ? `
          <button class="btn" style="font-size:.72rem;padding:.28rem .65rem"
                  onclick='editEnt(${JSON.stringify({id:ent.id,nom:ent.nom,secteur:ent.secteur||"",ville:ent.ville||"",code_postal:ent.code_postal||"",email_contact:ent.email_contact||"",telephone:ent.telephone||"",site_web:ent.site_web||"",description:ent.description||""}).replace(/'/g,"&#39;")})'>✏️ Modifier</button>
          <button class="btn btn-danger" style="font-size:.72rem;padding:.28rem .65rem"
                  onclick="deleteEnt(${ent.id})">🗑 Supprimer</button>
        ` : '<span style="font-size:.75rem;color:var(--muted)">— lecture seule</span>'}
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
}
function resetEntForm(){
  document.getElementById('entId').value='';
  ['eNom','eSecteur','eVille','eCP','eEmail','eTel','eSite','eDesc'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('entFormTitle').textContent='➕ Nouvelle entreprise';
}
document.getElementById('resetEntBtn').addEventListener('click',resetEntForm);
document.getElementById('saveEntBtn').addEventListener('click',async()=>{
  const errEl=document.getElementById('entError');
  const okEl =document.getElementById('entSuccess');
  errEl.className=okEl.className='form-alert';
  const id=document.getElementById('entId').value;
  const nom=document.getElementById('eNom').value.trim();
  if(!nom){ errEl.textContent='Le nom est obligatoire.'; errEl.className+=' error show'; return; }
  const res=await entCall({
    action:id?'modifier':'creer', id,
    nom,
    secteur:      document.getElementById('eSecteur').value.trim(),
    ville:        document.getElementById('eVille').value.trim(),
    code_postal:  document.getElementById('eCP').value.trim(),
    email_contact:document.getElementById('eEmail').value.trim(),
    telephone:    document.getElementById('eTel').value.trim(),
    site_web:     document.getElementById('eSite').value.trim(),
    description:  document.getElementById('eDesc').value.trim(),
    pays:'France',
  });
  if(!res.ok){ errEl.textContent=res.msg||'Erreur.'; errEl.className+=' error show'; }
  else { showToast(res.msg||'Enregistré.','success'); resetEntForm(); await loadEnts(); }
});

async function deleteEnt(id){
  if(!confirm('Supprimer cette entreprise ?')) return;
  const res=await entCall({action:'supprimer',id});
  if(res.ok){ showToast('Entreprise supprimée.','success'); await loadEnts(); }
  else showToast(res.msg||'Erreur.','error');
}

// ══ PROMOTIONS ════════════════════════════════════════════════
async function loadPromos(){
  const promos=await get(PROMO_API,{action:'liste'});
  const cont=document.getElementById('promosContainer');
  if(!promos.length){cont.innerHTML='<div class="empty-state"><span class="empty-icon">🎓</span><p>Aucune promotion.</p></div>';return;}
  cont.innerHTML=promos.map(p=>`
    <div class="form-card" style="max-width:100%">
      <p class="form-title">${e(p.nom)}</p>
      <p class="form-sub">Promo ${p.annee} · <strong style="color:var(--amber)">${p.nb_etudiants}</strong> étudiant(s)</p>
      ${p.description?`<p style="font-size:.8rem;color:var(--muted)">${e(p.description)}</p>`:''}
      <button class="btn btn-ghost" style="margin-top:.75rem;font-size:.78rem" onclick="voirEtudiants(${p.id},this)">Voir les étudiants</button>
      <div class="etudiants-list" style="margin-top:.75rem;display:none"></div>
    </div>`).join('');
}
async function voirEtudiants(promoId,btn){
  const list=btn.nextElementSibling;
  if(list.style.display!=='none'){ list.style.display='none'; btn.textContent='Voir les étudiants'; return; }
  btn.textContent='Chargement…';
  const etudiants=await get(PROMO_API,{action:'etudiants',id:promoId});
  list.style.display='block'; btn.textContent='Masquer';
  if(!etudiants.length){list.innerHTML='<p style="font-size:.8rem;color:var(--muted)">Aucun étudiant.</p>';return;}
  list.innerHTML=`<table class="data-table" style="font-size:.78rem">
    <thead><tr><th>Nom</th><th>Email</th><th>Candidatures</th><th>Acceptées</th></tr></thead>
    <tbody>${etudiants.map(u=>`
      <tr>
        <td>${e(u.prenom+' '+u.nom)}</td>
        <td><a href="mailto:${e(u.email)}" style="color:var(--amber)">${e(u.email)}</a></td>
        <td style="text-align:center">${u.nb_candidatures}</td>
        <td style="text-align:center;color:#34d399;font-weight:600">${u.nb_acceptees}</td>
      </tr>`).join('')}
    </tbody></table>`;
}

// ══ CANDIDATURES ══════════════════════════════════════════════
async function loadCands(){
  const cands=await adminCall({action:'liste_candidatures_pilote'});
  const sLabel={envoyee:'Envoyée',vue:'Vue',acceptee:'Acceptée',refusee:'Refusée'};
  const sClass={envoyee:'statut-envoyee',vue:'statut-vue',acceptee:'statut-acceptee',refusee:'statut-refusee'};
  document.getElementById('candsTbody').innerHTML=cands.map(c=>`
    <tr>
      <td><strong>${e(c.prenom+' '+c.nom)}</strong><br><small style="color:var(--muted)">${e(c.email)}</small></td>
      <td style="font-size:.82rem"><a href="../offre.php?id=${c.offre_id||''}" style="color:inherit;text-decoration:none">${e(c.offre_titre)}</a></td>
      <td style="font-size:.78rem">${new Date(c.created_at).toLocaleDateString('fr-FR')}</td>
      <td style="display:flex;gap:.3rem">
        ${c.cv_path?`<a href="../uploads/${e(c.cv_path)}" target="_blank" class="btn" style="font-size:.7rem;padding:.2rem .5rem">📄 CV</a>`:''}
        ${c.lm_path?`<a href="../uploads/${e(c.lm_path)}" target="_blank" class="btn" style="font-size:.7rem;padding:.2rem .5rem">✉️ LM</a>`:'–'}
      </td>
      <td>
        <select onchange="changeStatut(${c.id},this.value)" style="background:var(--surface2);border:1px solid var(--border);color:var(--text);padding:.25rem .5rem;border-radius:6px;font-size:.75rem">
          ${['envoyee','vue','acceptee','refusee'].map(s=>`<option value="${s}" ${c.statut===s?'selected':''}>${sLabel[s]}</option>`).join('')}
        </select>
      </td>
    </tr>`).join('')||'<tr><td colspan="5" style="text-align:center;color:var(--muted)">Aucune candidature.</td></tr>';
}
async function changeStatut(id,statut){
  const res=await adminCall({action:'statut_candidature',id,statut});
  if(res.ok) showToast('Statut mis à jour.','success');
  else showToast(res.msg||'Erreur.','error');
}

// ── TOAST ────────────────────────────────────────────────────
function showToast(msg, type='success'){
  const t=document.getElementById('toast');
  if(!t)return;
  t.textContent=msg;
  t.style.background=type==='success'?'rgba(52,211,153,.9)':'rgba(239,68,68,.9)';
  t.style.color='#fff';
  t.style.opacity='1';
  clearTimeout(t._timer);
  t._timer=setTimeout(()=>{t.style.opacity='0';},3000);
}

// ── INIT ──────────────────────────────────────────────────────
(async()=>{
  await loadComps();
  await loadStats();
  await loadOffres();
  if(typeof refreshMsgBadge==="function") refreshMsgBadge();
})();
</script>
</body>
</html>
