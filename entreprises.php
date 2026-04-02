<?php require_once 'php/config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Entreprises partenaires – Web4All</title>
  <meta name="description" content="Découvrez les entreprises partenaires qui proposent des stages et alternances sur Web4All.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script>(function(){var t=localStorage.getItem("w4a_theme")||"dark";document.documentElement.setAttribute("data-theme",t);})();</script>
  <script>window.SITE_URL="<?= SITE_URL ?>";</script>
</head>
<body>

<header class="big-header">
  <h1><span class="accent">Entreprises</span> partenaires</h1>
  <p class="header-sub">Découvrez qui recrute des stagiaires et alternants</p>
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

  <!-- Barre de recherche -->
  <div style="display:flex;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap">
    <input type="text" id="searchEnt" placeholder="Rechercher une entreprise, un secteur…"
           style="flex:1;min-width:200px;padding:.7rem 1rem;border-radius:var(--r);border:1px solid var(--border);background:var(--surface);color:var(--text);font-family:inherit;font-size:.9rem;outline:none"
           oninput="filtrerEnts()">
    <select id="filterVille" onchange="filtrerEnts()"
            style="padding:.7rem 1rem;border-radius:var(--r);border:1px solid var(--border);background:var(--surface);color:var(--text);font-family:inherit;font-size:.88rem;outline:none">
      <option value="">Toutes les villes</option>
      <option>Rouen</option><option>Le Havre</option>
      <option>Caen</option><option>Paris</option><option>Lille</option>
    </select>
  </div>

  <div class="stats-bar"><span id="statsEnt">Chargement…</span></div>

  <div id="entsContainer" class="entreprises-grid">
    <div class="empty-state" style="grid-column:1/-1"><span class="spinner"></span></div>
  </div>

</main>

<footer>
  <span class="footer-brand">Web4All</span>
  <span>© 2026 – Projet pédagogique</span>
  <a href="mention_legales.php">Mentions légales</a>
</footer>

<script src="js/script.js"></script>
<script>
const ENTS_PAR_PAGE = 12;
let allEnts = [];
let entsPageCourante = 1;
let entsFiltrees = [];

async function loadEnts(){
  const r = await fetch('php/entreprises.php?action=liste');
  allEnts = await r.json();
  filtrerEnts();
}

function filtrerEnts(){
  const q     = document.getElementById('searchEnt').value.toLowerCase().trim();
  const ville = document.getElementById('filterVille').value;
  entsFiltrees = allEnts.filter(e=>{
    const matchQ = !q||(e.nom||'').toLowerCase().includes(q)||(e.secteur||'').toLowerCase().includes(q)||(e.description||'').toLowerCase().includes(q);
    const matchV = !ville||e.ville===ville;
    return matchQ&&matchV;
  });
  entsPageCourante = 1;
  afficherPageEnts();
}

function afficherPageEnts(){
  const cont  = document.getElementById('entsContainer');
  const stats = document.getElementById('statsEnt');
  const total = entsFiltrees.length;
  const totalPages = Math.ceil(total / ENTS_PAR_PAGE);
  const debut = (entsPageCourante - 1) * ENTS_PAR_PAGE;
  const page  = entsFiltrees.slice(debut, debut + ENTS_PAR_PAGE);

  stats.innerHTML = `<strong>${total}</strong> entreprise${total>1?'s':''} partenaire${total>1?'s':''}`;

  if(!total){
    cont.innerHTML = '<div class="empty-state" style="grid-column:1/-1"><span class="empty-icon">🔍</span><p>Aucune entreprise trouvée.</p></div>';
    let old=document.getElementById('entsPagination'); if(old)old.remove();
    return;
  }

  cont.innerHTML = page.map(ent=>`
    <div class="entreprise-card" onclick="window.location.href='entreprise.php?id=${ent.id}'" style="cursor:pointer">
      <h3>${e(ent.nom)}</h3>
      ${ent.secteur?`<p class="ent-secteur">💼 ${e(ent.secteur)}</p>`:''}
      <div style="display:flex;gap:.4rem;flex-wrap:wrap;margin:.4rem 0">
        ${ent.ville?`<span class="badge badge-lieu">📍 ${e(ent.ville)}</span>`:''}
        ${ent.nb_offres>0?`<span class="badge badge-remun">📋 ${ent.nb_offres} offre${ent.nb_offres>1?'s':''}</span>`:''}
        ${ent.nb_stagiaires>0?`<span class="badge badge-duree">👥 ${ent.nb_stagiaires} stagiaire${ent.nb_stagiaires>1?'s':''}</span>`:''}
      </div>
      ${ent.description?`<p class="ent-info" style="font-size:.82rem;color:var(--muted);margin-top:.3rem;line-height:1.5">${e(ent.description.substring(0,100))}${ent.description.length>100?'…':''}</p>`:''}
      <div style="margin-top:.75rem;display:flex;gap:.5rem;flex-wrap:wrap">
        <a class="btn" href="entreprise.php?id=${ent.id}" onclick="event.stopPropagation()">Voir les offres →</a>
        ${ent.site_web?`<a class="btn btn-ghost" href="${e(ent.site_web)}" target="_blank" rel="noopener" onclick="event.stopPropagation()">🌐 Site web</a>`:''}
      </div>
    </div>`).join('');

  // Pagination
  let pag = document.getElementById('entsPagination');
  if(!pag){
    pag = document.createElement('div');
    pag.id = 'entsPagination';
    cont.parentNode.insertBefore(pag, cont.nextSibling);
  }
  pag.innerHTML = totalPages<=1 ? '' : buildEntsPagination(entsPageCourante, totalPages);
}

function buildEntsPagination(current, total){
  let html=`<div class="pagination">`;
  html+=`<button class="pag-btn" ${current===1?'disabled':''} onclick="goPageEnts(${current-1})">‹</button>`;
  for(let i=1;i<=total;i++){
    if(total>7 && i>2 && i<total-1 && Math.abs(i-current)>1){
      if(i===3||i===total-2) html+=`<span class="pag-ellipsis">…</span>`;
      continue;
    }
    html+=`<button class="pag-btn${i===current?' pag-active':''}" onclick="goPageEnts(${i})">${i}</button>`;
  }
  html+=`<button class="pag-btn" ${current===total?'disabled':''} onclick="goPageEnts(${current+1})">›</button>`;
  html+=`</div>`;
  return html;
}

function goPageEnts(page){
  entsPageCourante = page;
  afficherPageEnts();
  document.getElementById('entsContainer')?.scrollIntoView({behavior:'smooth',block:'start'});
}

function e(s){ const d=document.createElement('div'); d.textContent=s??''; return d.innerHTML; }

loadEnts();
</script>
</body>
</html>
