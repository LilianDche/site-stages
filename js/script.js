
// ── THÈME SOMBRE/CLAIR ────────────────────────────────────────
function initTheme(){
    const saved = localStorage.getItem('w4a_theme') || 'dark';
    applyTheme(saved);
}
function applyTheme(theme){
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('w4a_theme', theme);
    const btn = document.getElementById('themeToggle');
    if(btn){
        btn.textContent = theme === 'light' ? '🌙 Mode sombre' : '🌸 Mode clair';
        btn.title = theme === 'light' ? 'Passer en mode sombre' : 'Passer en mode clair';
    }
}
function toggleTheme(){
    const current = document.documentElement.getAttribute('data-theme') || 'dark';
    applyTheme(current === 'dark' ? 'light' : 'dark');
}
// Appliquer immédiatement pour éviter le flash
(function(){ 
    const t = localStorage.getItem('w4a_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', t);
})();


// ── FAVORIS (localStorage) ────────────────────────────────────
function getFavoris(){ try{return JSON.parse(localStorage.getItem('w4a_favoris')||'[]');}catch{return[];} }
function toggleFavori(id){
    const favs=getFavoris();
    const idx=favs.indexOf(id);
    if(idx===-1) favs.push(id); else favs.splice(idx,1);
    localStorage.setItem('w4a_favoris',JSON.stringify(favs));
    return idx===-1; // true = ajouté
}
function isFavori(id){ return getFavoris().includes(id); }
function handleFavori(id,btn){ const added=toggleFavori(id); btn.textContent=added?'⭐':'☆'; btn.title=added?'Retirer des favoris':'Ajouter aux favoris'; }

// ============================================================
//  WEB4ALL v7 – script.js
// ============================================================

// Calcul automatique du chemin de base — fonctionne quel que soit le domaine ou le dossier
(function(){
    // Remonter jusqu'à trouver le répertoire racine du projet
    // en retirant les segments /pilote/ ou /admin/ de l'URL courante
    const path = window.location.pathname;
    const seg  = path.replace(/\/(pilote|admin)\/[^/]*$/, '')  // retirer /pilote/xxx ou /admin/xxx
                      .replace(/\/[^/]*\.php$/, '')              // retirer /xxx.php final
                      .replace(/\/$/, '');                        // retirer slash final
    window._BASE = window.location.origin + seg;
})();

const API = {
    auth:        _BASE+'/php/auth.php',
    offres:      _BASE+'/php/offres.php',
    candidatures:_BASE+'/php/candidatures.php',
    entreprises: _BASE+'/php/entreprises.php',
    competences: _BASE+'/php/competences.php',
    promotions:  _BASE+'/php/promotions.php',
    pilotes:     _BASE+'/php/pilotes.php',
};

async function apiPost(url,data){
    try{
        const r=await fetch(url,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)});
        if(!r.ok && r.status===0) throw new Error('network');
        return r.json();
    }catch(err){
        console.warn('apiPost error',url,err);
        return {ok:false,msg:'Erreur réseau. Vérifiez que le serveur est démarré.'};
    }
}
async function apiGet(url,params={}){
    try{
        const qs=new URLSearchParams(params).toString();
        const r=await fetch(qs?`${url}?${qs}`:url);
        if(!r.ok && r.status===0) throw new Error('network');
        return r.json();
    }catch(err){
        console.warn('apiGet error',url,err);
        return null;
    }
}
function e(str){ const d=document.createElement('div'); d.textContent=str??''; return d.innerHTML; }

// ── AUTH ──────────────────────────────────────────────────────
let currentUser=null;

async function loadUser(){
    try{
        const d=await apiGet(API.auth,{action:'me'});
        currentUser=d&&d.ok?d.user:null;
    }catch{ currentUser=null; }
    updateNavAuth();
    applyTheme(localStorage.getItem('w4a_theme')||'dark');
    }

async function doLogout(){
    await apiPost(API.auth,{action:'logout'});
    currentUser=null; updateNavAuth();
    if(window.location.pathname.includes('profil')) window.location.href='index.php';
    else location.reload();
}

function updateNavAuth(){
    const nr=document.getElementById('navRight');
    if(!nr) return;
    if(currentUser){
        const roleLabel={admin:'Admin',pilote:'Pilote',etudiant:'Étudiant'}[currentUser.role]||'';
        nr.innerHTML=`
          <span class="nav-user">
            <span class="role-badge role-${e(currentUser.role)}">${roleLabel}</span>
            ${e(currentUser.prenom)}
          </span>
          ${currentUser.role!=='etudiant'?`<a href="pilote/index.php" class="nav-btn">Espace pilote</a>`:''}
          ${currentUser.role!=='etudiant'?`<a href="pilote/demande.php" class="nav-btn" style="color:var(--amber);border-color:var(--amber)">+ Soumettre offre</a>`:''}
          ${currentUser.role==='admin'?`<a href="admin/index.php" class="nav-btn">Admin</a>`:''}

          <button class="theme-toggle" id="themeToggle" onclick="toggleTheme()">🌸 Mode clair</button>
          <button class="nav-btn" onclick="doLogout()">Déconnexion</button>
        `;
    } else {
        nr.innerHTML=`
          <button class="theme-toggle" id="themeToggle" onclick="toggleTheme()">🌸 Mode clair</button>
          <button class="nav-btn" id="btnLogin">Se connecter</button>
          <button class="nav-btn primary" id="btnRegister">S'inscrire</button>
        `;
        document.getElementById('btnLogin')  ?.addEventListener('click',()=>openModal('login'));
        document.getElementById('btnRegister')?.addEventListener('click',()=>openModal('register'));
    }
}

// ── MODAL ─────────────────────────────────────────────────────
function openModal(tab='login'){
    document.getElementById('authModal')?.classList.add('active');
    switchTab(tab); clearAlerts();
}
function closeModal(){ document.getElementById('authModal')?.classList.remove('active'); clearAlerts(); }
function switchTab(tab){
    document.querySelectorAll('.modal-tab')  .forEach(t=>t.classList.toggle('active',t.dataset.tab===tab));
    document.querySelectorAll('.modal-panel').forEach(p=>p.classList.toggle('active',p.id==='panel-'+tab));
    clearAlerts();
}
function showAlert(id,msg,type){ const el=document.getElementById(id); if(!el)return; el.textContent=msg; el.className=`form-alert ${type} show`; }
function clearAlerts(){ document.querySelectorAll('.form-alert').forEach(el=>{el.className='form-alert';el.textContent='';}); }

// ── SÉLECTEUR RÔLE ────────────────────────────────────────────
let selectedRole='etudiant';

function initRoleSelector(){
    document.querySelectorAll('.role-choice').forEach(btn=>{
        btn.addEventListener('click',()=>{
            document.querySelectorAll('.role-choice').forEach(b=>b.classList.remove('active'));
            btn.classList.add('active');
            selectedRole=btn.dataset.role;
            const promoField=document.getElementById('fieldPromo');
            const entField  =document.getElementById('fieldEntreprise');
            if(selectedRole==='pilote'){
                promoField&&(promoField.style.display='none');
                entField  &&(entField.style.display='block');
                document.getElementById('registerNomEntreprise')?.focus();
            } else {
                promoField&&(promoField.style.display='block');
                entField  &&(entField.style.display='none');
            }
        });
    });
}

// ── CHARGEMENT DONNÉES FORMULAIRE INSCRIPTION ─────────────────
async function loadPromotions(){
    const sel=document.getElementById('registerPromo'); if(!sel)return;
    const promos=await apiGet(API.promotions,{action:'liste'});
    if(!Array.isArray(promos)){ sel.innerHTML='<option value="">— Erreur chargement —</option>'; return; }
    sel.innerHTML='<option value="">— Sélectionner une promotion —</option>'
        +promos.map(p=>`<option value="${p.id}">${e(p.nom)} (${p.annee})</option>`).join('');
}

async function loadEntreprisesInscription(){
    const sel=document.getElementById('registerEntrepriseSelect'); if(!sel)return;
    const ents=await apiGet(API.entreprises,{action:'liste'});
    if(!Array.isArray(ents)){ sel.innerHTML='<option value="">— Erreur chargement —</option>'; return; }
    sel.innerHTML=
        '<option value="">— Choisir une entreprise —</option>'
        +'<option value="__autre__">✏️ Autre (nouvelle entreprise)</option>'
        +'<option disabled>──────────────────</option>'
        +ents.map(ent=>`<option value="${ent.id}">${e(ent.nom)}${ent.ville?' – '+e(ent.ville):''}</option>`).join('');
}

// Afficher/masquer le champ texte libre selon la sélection
window.onEntrepriseSelectChange = function(){
    const val=document.getElementById('registerEntrepriseSelect')?.value;
    const libre=document.getElementById('fieldNomEntrepriseLibre');
    if(libre) libre.style.display=(val==='__autre__')?'block':'none';
};

// ── COMPÉTENCES FILTRE ────────────────────────────────────────
async function loadCompetencesFilter(){
    const sel=document.getElementById('filterCompetence'); if(!sel)return;
    const comps=await apiGet(API.competences);
    if(!Array.isArray(comps)){ return; }
    const groups={};
    comps.forEach(c=>{(groups[c.categorie||'Autre']??=[]).push(c);});
    sel.innerHTML='<option value="">Compétence</option>'
        +Object.entries(groups).map(([cat,cs])=>
            `<optgroup label="${e(cat)}">`+cs.map(c=>`<option value="${e(c.nom)}">${e(c.nom)}</option>`).join('')+'</optgroup>'
        ).join('');
}

// ── PAGINATION OFFRES ─────────────────────────────────────────
const OFFRES_PAR_PAGE = 12;
let offresPageCourante = 1;
let offresListeComplète = [];

function renderOffres(liste){
    offresListeComplète = liste;
    offresPageCourante = 1;
    _afficherPageOffres();
}

function _afficherPageOffres(){
    const container=document.getElementById('offresContainer');
    const stats    =document.getElementById('statsText');
    if(!container)return;
    const total = offresListeComplète.length;
    const totalPages = Math.ceil(total / OFFRES_PAR_PAGE);
    const debut = (offresPageCourante - 1) * OFFRES_PAR_PAGE;
    const page  = offresListeComplète.slice(debut, debut + OFFRES_PAR_PAGE);

    if(stats) stats.innerHTML=`<strong>${total}</strong> offre${total!==1?'s':''} disponible${total!==1?'s':''}`;

    if(!total){
        container.innerHTML=`<div class="empty-state"><span class="empty-icon">🔍</span><p>Aucune offre trouvée.</p></div>`;
        // Supprimer pagination éventuelle
        const old=document.getElementById('offresPagination'); if(old)old.remove();
        return;
    }

    container.innerHTML=page.map(o=>`
        <article class="offre-card" onclick="window.location.href='offre.php?id=${o.id}'" style="cursor:pointer">
            <h2>${e(o.titre)}</h2>
            <p class="offre-entreprise"><a href="entreprise.php?id=${o.entreprise_id||''}" onclick="event.stopPropagation()" style="color:var(--muted);text-decoration:none" onmouseover="this.style.color='var(--amber)'" onmouseout="this.style.color='var(--muted)'">${e(o.entreprise)}</a></p>
            <div class="offre-meta">
                <span class="badge ${o.type==='stage'?'badge-stage':'badge-alt'}">${o.type==='stage'?'🎓 Stage':'🔄 Alternance'}</span>
                <span class="badge badge-lieu">📍 ${e(o.lieu)}</span>
                ${o.duree        ?`<span class="badge badge-duree">⏱ ${e(o.duree)}</span>`:''}
                ${o.remuneration ?`<span class="badge badge-remun">💶 ${e(o.remuneration)}</span>`:''}
            </div>
            ${o.competences?.length?`
            <div class="offre-competences">
                ${o.competences.slice(0,4).map(c=>`<span class="tag-comp">${e(c)}</span>`).join('')}
                ${o.competences.length>4?`<span class="tag-comp tag-more">+${o.competences.length-4}</span>`:''}
            </div>`:''}
            <p class="offre-desc">${e(o.description)}</p>
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.75rem">
              <a class="btn" href="offre.php?id=${o.id}" onclick="event.stopPropagation()">Voir l'offre →</a>
              <button class="btn btn-ghost fav-btn" id="fav-${o.id}" style="padding:.52rem .75rem" title="Favori" onclick="event.stopPropagation();handleFavori(${o.id},this)">${isFavori(o.id)?'⭐':'☆'}</button>
            </div>
        </article>
    `).join('');

    // Rendre/mettre à jour la pagination
    let pag=document.getElementById('offresPagination');
    if(!pag){
        pag=document.createElement('div');
        pag.id='offresPagination';
        container.parentNode.insertBefore(pag, container.nextSibling);
    }
    pag.innerHTML = totalPages<=1 ? '' : _buildPagination(offresPageCourante, totalPages, 'goPageOffres');
}

// ── CHARGEMENT OFFRES ─────────────────────────────────────────
async function chargerOffres(params={}){
    const c=document.getElementById('offresContainer'); if(!c)return;
    c.innerHTML=`<div class="empty-state"><span class="spinner"></span></div>`;
    try{
        const data=await apiGet(API.offres,params);
        const offres=Array.isArray(data)?data:[];
        renderOffres(offres);
        if(window.updateMapMarkers) window.updateMapMarkers(offres);
    } catch{
        c.innerHTML=`<div class="empty-state"><span class="empty-icon">⚠️</span><p>Vérifiez que XAMPP est démarré.</p></div>`;
    }
}

// ── HELPERS PAGINATION ────────────────────────────────────────
function _buildPagination(current, total, fnName){
    let html=`<div class="pagination">`;
    html+=`<button class="pag-btn" ${current===1?'disabled':''} onclick="${fnName}(${current-1})">‹</button>`;
    for(let i=1;i<=total;i++){
        if(total>7 && i>2 && i<total-1 && Math.abs(i-current)>1){
            if(i===3||i===total-2) html+=`<span class="pag-ellipsis">…</span>`;
            continue;
        }
        html+=`<button class="pag-btn${i===current?' pag-active':''}" onclick="${fnName}(${i})">${i}</button>`;
    }
    html+=`<button class="pag-btn" ${current===total?'disabled':''} onclick="${fnName}(${current+1})">›</button>`;
    html+=`</div>`;
    return html;
}

function goPageOffres(page){
    offresPageCourante=page;
    _afficherPageOffres();
    document.getElementById('offresContainer')?.scrollIntoView({behavior:'smooth',block:'start'});
}

function getFiltres(){
    return {
        q:          document.getElementById('searchInput')      ?.value.trim()||'',
        type:       document.getElementById('filterType')       ?.value||'',
        lieu:       document.getElementById('filterLieu')       ?.value||'',
        duree:      document.getElementById('filterDuree')      ?.value||'',
        competence: document.getElementById('filterCompetence') ?.value||'',
    };
}
let searchTimer;
function onSearch(){ clearTimeout(searchTimer); searchTimer=setTimeout(()=>chargerOffres(getFiltres()),300); }

// ── FORMULAIRE CANDIDATURE ────────────────────────────────────
async function prefillFormulaire(){
    const id=new URLSearchParams(window.location.search).get('offre');
    if(!id)return;
    try{
        const o=await apiGet(API.offres,{id});
        const inp=document.getElementById('offreInput');
        if(inp&&o.titre){inp.value=o.titre; inp.dataset.offreId=o.id;}
    }catch{}
}

// ── SCROLL NAVBAR + BOUTON RETOUR EN HAUT ────────────────────
document.addEventListener('scroll',()=>{
    const navbar = document.getElementById('navbar');
    if(navbar) navbar.classList.toggle('scrolled', window.scrollY > 60);
    // Bouton retour en haut : visible quand la navbar n'est plus dans le viewport
    const btn = document.getElementById('backToTop');
    if(btn){
        const navBottom = navbar ? navbar.getBoundingClientRect().bottom : 80;
        btn.classList.toggle('visible', navBottom < 0);
    }
});

// ── COOKIE ────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async ()=>{
    // ── BURGER MENU ───────────────────────────────────────────────
    const burgerBtn = document.getElementById('burgerBtn');
    const navLinks  = document.getElementById('navLinks');
    if(burgerBtn && navLinks){
        burgerBtn.addEventListener('click', ()=>{
            const open = navLinks.classList.toggle('open');
            burgerBtn.classList.toggle('open', open);
            burgerBtn.setAttribute('aria-expanded', open);
        });
        // Fermer le menu au clic sur un lien
        navLinks.querySelectorAll('a').forEach(a=>{
            a.addEventListener('click', ()=>{
                navLinks.classList.remove('open');
                burgerBtn.classList.remove('open');
                burgerBtn.setAttribute('aria-expanded','false');
            });
        });
        // Fermer si clic dehors
        document.addEventListener('click', e=>{
            if(!burgerBtn.contains(e.target) && !navLinks.contains(e.target)){
                navLinks.classList.remove('open');
                burgerBtn.classList.remove('open');
                burgerBtn.setAttribute('aria-expanded','false');
            }
        });
    }
    const banner=document.getElementById('cookieBanner');
    if(banner){
        banner.style.display=localStorage.getItem('w4a_cookies')==='1'?'none':'flex';
        document.getElementById('acceptCookies')?.addEventListener('click',()=>{localStorage.setItem('w4a_cookies','1');banner.style.display='none';});
    }

    await loadUser();

    // Modal
    document.getElementById('modalClose')      ?.addEventListener('click',closeModal);
    document.getElementById('authModal')       ?.addEventListener('click',ev=>{if(ev.target===ev.currentTarget)closeModal();});
    document.getElementById('switchToRegister')?.addEventListener('click',ev=>{ev.preventDefault();switchTab('register');});
    document.getElementById('switchToLogin')   ?.addEventListener('click',ev=>{ev.preventDefault();switchTab('login');});
    document.querySelectorAll('.modal-tab').forEach(t=>t.addEventListener('click',()=>switchTab(t.dataset.tab)));

    // Mot de passe oublié
    document.getElementById('forgotPasswordLink')?.addEventListener('click', ev=>{
        ev.preventDefault();
        // Cacher les onglets quand on est sur le panel forgot
        document.querySelectorAll('.modal-tab').forEach(t=>t.style.display='none');
        switchTab('forgot');
    });
    document.getElementById('backToLogin')?.addEventListener('click', ev=>{
        ev.preventDefault();
        document.querySelectorAll('.modal-tab').forEach(t=>t.style.display='');
        switchTab('login');
    });
    document.getElementById('forgotBtn')?.addEventListener('click', async ()=>{
        const email   = document.getElementById('forgotEmail')?.value.trim()||'';
        const alertEl = document.getElementById('forgotAlert');
        alertEl.className = 'form-alert';
        if(!email){ alertEl.textContent='Entrez votre email.'; alertEl.className+=' error show'; return; }
        const btn = document.getElementById('forgotBtn');
        btn.disabled=true; btn.textContent='Envoi…';
        const res = await apiPost('php/auth.php',{action:'forgot_password',email});
        btn.disabled=false; btn.textContent='Envoyer le lien →';
        alertEl.textContent = res.msg || (res.ok ? 'Email envoyé !' : 'Erreur.');
        alertEl.className += res.ok ? ' success show' : ' error show';
        if(res.ok) document.getElementById('forgotEmail').value='';
    });

    // Login
    document.getElementById('loginBtn')?.addEventListener('click',async()=>{
        clearAlerts();
        const email=document.getElementById('loginEmail')?.value||'';
        const pass =document.getElementById('loginPassword')?.value||'';
        if(!email||!pass){showAlert('loginError','Remplissez tous les champs.','error');return;}
        const btn=document.getElementById('loginBtn'); btn.disabled=true; btn.textContent='…';
        const res=await apiPost(API.auth,{action:'login',email,password:pass});
        btn.disabled=false; btn.textContent='Se connecter →';
        if(!res||!res.ok){showAlert('loginError',(res&&res.msg)||'Erreur de connexion.','error');return;}
        currentUser=res.user;
        showAlert('loginSuccess',`Bienvenue ${res.user.prenom} !`,'success');
        setTimeout(()=>{closeModal();updateNavAuth();location.reload();},900);
    });

    // Sélecteur de rôle + données inscription
    initRoleSelector();
    await loadPromotions();
    await loadEntreprisesInscription();

    // Register
    document.getElementById('registerBtn')?.addEventListener('click',async()=>{
        clearAlerts();
        const prenom=document.getElementById('registerPrenom')?.value||'';
        const nom   =document.getElementById('registerNom')?.value||'';
        const email =document.getElementById('registerEmail')?.value||'';
        const pass  =document.getElementById('registerPassword')?.value||'';
        const promo       = document.getElementById('registerPromo')?.value||'';
        const entSelectVal= document.getElementById('registerEntrepriseSelect')?.value||'';
        const nomEntLibre = document.getElementById('registerNomEntreprise')?.value.trim()||'';
        const btn=document.getElementById('registerBtn'); btn.disabled=true; btn.textContent='…';

        // Validation pilote
        if(selectedRole==='pilote'){
            if(!entSelectVal){
                showAlert('registerError','Sélectionnez une entreprise.','error');
                btn.disabled=false; btn.textContent='Créer mon compte →'; return;
            }
            if(entSelectVal==='__autre__'&&!nomEntLibre){
                showAlert('registerError','Saisissez le nom de votre entreprise.','error');
                btn.disabled=false; btn.textContent='Créer mon compte →'; return;
            }
        }

        // Si entreprise existante sélectionnée → on envoie entreprise_id
        // Si "Autre" → on envoie nom_entreprise (sera créée à la validation)
        const isExistante = selectedRole==='pilote' && entSelectVal && entSelectVal!=='__autre__';
        const isAutre     = selectedRole==='pilote' && entSelectVal==='__autre__';

        const res=await apiPost(API.auth,{
            action:'register',prenom,nom,email,password:pass,
            role:selectedRole,
            promotion_id:  selectedRole==='etudiant'?(promo||null):null,
            entreprise_id: isExistante ? parseInt(entSelectVal) : null,
            nom_entreprise:isAutre     ? nomEntLibre : '',
        });
        btn.disabled=false; btn.textContent='Créer mon compte →';
        if(!res||!res.ok){showAlert('registerError',(res&&res.msg)||'Erreur lors de la création.','error');return;}
        showAlert('registerSuccess',res.msg+(res.pending?' (compte en attente de validation)':''),'success');
        setTimeout(()=>switchTab('login'),2000);
    });

    ['loginEmail','loginPassword'].forEach(id=>{
        document.getElementById(id)?.addEventListener('keydown',ev=>{if(ev.key==='Enter')document.getElementById('loginBtn')?.click();});
    });

    // Filtres accueil
    document.getElementById('searchInput')     ?.addEventListener('input',onSearch);
    document.getElementById('filterType')      ?.addEventListener('change',()=>chargerOffres(getFiltres()));
    document.getElementById('filterLieu')      ?.addEventListener('change',()=>chargerOffres(getFiltres()));
    document.getElementById('filterDuree')     ?.addEventListener('change',()=>chargerOffres(getFiltres()));
    document.getElementById('filterCompetence')?.addEventListener('change',()=>chargerOffres(getFiltres()));

    // Page accueil
    if(document.getElementById('offresContainer')){
        await loadCompetencesFilter();
        chargerOffres();
    }

    // Page formulaire - prefill uniquement (le submit est géré dans chaque page)
    if(document.getElementById('offreInput')){
        await prefillFormulaire();
    }
});


// ── LEAFLET (OpenStreetMap) ────────────────────────────────────
let map, markers = [];

function initMap() {
    const mapEl = document.getElementById('map');
    if (!mapEl) return;
    if (typeof L === 'undefined') {
        // Leaflet pas encore chargé, réessayer dans 200ms
        setTimeout(initMap, 200);
        return;
    }

    map = L.map('map', { zoomControl: true }).setView([46.8, 2.3], 6);

    // Tuiles sombre (CartoDB Dark Matter) pour coller au thème
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> © <a href="https://carto.com/">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    // Icône personnalisée amber
    const amberIcon = L.divIcon({
        className: '',
        html: '<div style="width:14px;height:14px;border-radius:50%;background:#f59e0b;border:2px solid #000;box-shadow:0 0 6px rgba(245,158,11,.6)"></div>',
        iconSize: [14, 14],
        iconAnchor: [7, 7],
        popupAnchor: [0, -10]
    });

    window.updateMapMarkers = function(offres) {
        markers.forEach(m => map.removeLayer(m));
        markers = [];
        (offres || []).forEach(o => {
            if (!o.lat || !o.lng) return;
            const siteBase = window.SITE_URL || _BASE;
            const popupHtml = `
                <div style="font-family:'Outfit',sans-serif;min-width:180px;max-width:240px;padding:2px 0">
                    <div style="font-size:12px;font-weight:700;color:#111;line-height:1.3;margin-bottom:4px">${e(o.titre)}</div>
                    <div style="font-size:11px;color:#555;margin-bottom:2px">🏢 ${e(o.entreprise || '')}</div>
                    <div style="font-size:11px;color:#555;margin-bottom:8px">📍 ${e(o.lieu)} &nbsp;·&nbsp; ${e(o.type || '')}</div>
                    <a href="${siteBase}/offre.php?id=${o.id}"
                       style="display:inline-block;background:#f59e0b;color:#000;font-size:11px;font-weight:700;padding:5px 12px;border-radius:6px;text-decoration:none;letter-spacing:.3px">
                       Voir l'offre →
                    </a>
                </div>`;
            const m = L.marker([parseFloat(o.lat), parseFloat(o.lng)], { icon: amberIcon, title: o.titre })
                .bindPopup(popupHtml, { maxWidth: 260, className: 'map-popup' })
                .addTo(map);
            markers.push(m);
        });
    };
}

document.addEventListener('DOMContentLoaded', initMap);
