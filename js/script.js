
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

async function apiPost(url,data){ const r=await fetch(url,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)}); return r.json(); }
async function apiGet(url,params={}){ const qs=new URLSearchParams(params).toString(); return (await fetch(qs?`${url}?${qs}`:url)).json(); }
function e(str){ const d=document.createElement('div'); d.textContent=str??''; return d.innerHTML; }

// ── AUTH ──────────────────────────────────────────────────────
let currentUser=null;

async function loadUser(){
    const d=await apiGet(API.auth,{action:'me'});
    currentUser=d.ok?d.user:null;
    updateNavAuth();
    applyTheme(localStorage.getItem('w4a_theme')||'dark');
    if(currentUser){ refreshMsgBadge(); injectMsgFab(); }
}

function injectMsgFab(){
    if(document.getElementById('msgFab')) return;
    const fab=document.createElement('a');
    fab.id='msgFab';
    fab.href=_BASE+'/messages.php';
    fab.title='Messagerie';
    fab.innerHTML=`<span style="font-size:1.3rem">💬</span><span class="msg-fab-badge" style="display:none;position:absolute;top:-4px;right:-4px;background:#e91e8c;color:#fff;font-size:.6rem;font-weight:700;padding:.1rem .38rem;border-radius:100px;min-width:18px;text-align:center;line-height:1.4"></span>`;
    fab.style.cssText='position:fixed;bottom:1.75rem;right:1.75rem;width:52px;height:52px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(0,0,0,.35);z-index:1000;text-decoration:none;transition:transform .2s,box-shadow .2s;';
    fab.addEventListener('mouseenter',()=>{fab.style.transform='scale(1.1)';fab.style.boxShadow='0 6px 28px rgba(0,0,0,.4)';});
    fab.addEventListener('mouseleave',()=>{fab.style.transform='';fab.style.boxShadow='0 4px 20px rgba(0,0,0,.35)';});
    document.body.appendChild(fab);
}
async function refreshMsgBadge(){
    try{
        const r=await fetch(_BASE+'/php/messages.php?action=nb_non_lus');
        const d=await r.json();
        const nb=d.nb||0;
        // Badge FAB
        const fabBadge=document.querySelector('.msg-fab-badge');
        if(fabBadge){ fabBadge.textContent=nb; fabBadge.style.display=nb>0?'block':'none'; }
        // Anciens badges nav (fallback)
        document.querySelectorAll('.msg-nav-badge').forEach(el=>{
            el.textContent=nb; el.style.display=nb>0?'inline-flex':'none';
        });
    }catch{}
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
    sel.innerHTML='<option value="">— Sélectionner une promotion —</option>'
        +promos.map(p=>`<option value="${p.id}">${e(p.nom)} (${p.annee})</option>`).join('');
}

async function loadEntreprisesInscription(){
    const sel=document.getElementById('registerEntrepriseSelect'); if(!sel)return;
    const ents=await apiGet(API.entreprises,{action:'liste'});
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
    const groups={};
    comps.forEach(c=>{(groups[c.categorie||'Autre']??=[]).push(c);});
    sel.innerHTML='<option value="">Compétence</option>'
        +Object.entries(groups).map(([cat,cs])=>
            `<optgroup label="${e(cat)}">`+cs.map(c=>`<option value="${e(c.nom)}">${e(c.nom)}</option>`).join('')+'</optgroup>'
        ).join('');
}

// ── AFFICHAGE OFFRES ──────────────────────────────────────────
function renderOffres(liste){
    const container=document.getElementById('offresContainer');
    const stats    =document.getElementById('statsText');
    if(!container)return;
    if(stats) stats.innerHTML=`<strong>${liste.length}</strong> offre${liste.length!==1?'s':''} disponible${liste.length!==1?'s':''}`;
    if(!liste.length){
        container.innerHTML=`<div class="empty-state"><span class="empty-icon">🔍</span><p>Aucune offre trouvée.</p></div>`;
        return;
    }
    container.innerHTML=liste.map(o=>`
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
        if(!res.ok){showAlert('loginError',res.msg,'error');return;}
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
        if(!res.ok){showAlert('registerError',res.msg,'error');return;}
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


// ── GOOGLE MAPS ───────────────────────────────────────────────
let map,markers=[];
function initMap(){
    if(!document.getElementById('map'))return;
    map=new google.maps.Map(document.getElementById('map'),{
        zoom:6,center:{lat:48.8,lng:2.3},
        styles:[
            {elementType:'geometry',stylers:[{color:'#111'}]},
            {elementType:'labels.text.fill',stylers:[{color:'#aaa'}]},
            {featureType:'road',elementType:'geometry',stylers:[{color:'#222'}]},
            {featureType:'water',elementType:'geometry',stylers:[{color:'#050505'}]},
            {featureType:'poi',stylers:[{visibility:'off'}]},
        ]
    });
    window.updateMapMarkers=function(offres){
        markers.forEach(m=>m.setMap(null));markers=[];
        (offres||[]).forEach(o=>{
            if(!o.lat||!o.lng)return;
            const m=new google.maps.Marker({
                position:{lat:parseFloat(o.lat),lng:parseFloat(o.lng)},
                map,title:o.titre,
                icon:{path:google.maps.SymbolPath.CIRCLE,scale:7,fillColor:'#f59e0b',fillOpacity:1,strokeColor:'#000',strokeWeight:2}
            });
            const info=new google.maps.InfoWindow({content:`<div style="font-family:sans-serif;font-size:12px"><strong>${e(o.titre)}</strong><br>${e(o.entreprise||'')} · ${e(o.lieu)}</div>`});
            m.addListener('click',()=>info.open(map,m));
            markers.push(m);
        });
    };
}
