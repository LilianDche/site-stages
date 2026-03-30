<?php
require_once 'php/config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Messagerie – Web4All</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script>(function(){var t=localStorage.getItem("w4a_theme")||"dark";document.documentElement.setAttribute("data-theme",t);})();</script>

  <script>window.SITE_URL="<?= SITE_URL ?>";</script>
</head>
<body class="page-messages">

<nav class="navbar" id="navbar">
  <button class="burger-btn" id="burgerBtn" aria-label="Menu" aria-expanded="false">
    <span></span><span></span><span></span>
  </button>
  <div class="nav-links" id="navLinks">
    <a href="index.php">Accueil</a>
    <a href="entreprises.php">Entreprises</a>
    <a href="profil.php">Profil</a>
    <a href="messages.php" id="on">💬 Messagerie</a>
    <a href="mention_legales.php">Mentions légales</a>
  </div>
  <div class="nav-right" id="navRight"></div>
</nav>

<main class="msg-page-main">

  <div id="notConnected" style="display:none;text-align:center;padding:5rem 2rem">
    <div style="font-size:3rem;opacity:.3;margin-bottom:1rem">🔒</div>
    <p style="color:var(--muted);margin-bottom:1.5rem">Connectez-vous pour accéder à la messagerie.</p>
    <button class="btn btn-primary" onclick="openModal('login')">Se connecter</button>
  </div>

  <div id="msgApp" style="display:none;flex-direction:column;flex:1;min-height:0">
    <div class="msg-layout">

      <!-- SIDEBAR : liste des conversations -->
      <div class="msg-sidebar" id="msgSidebar">
        <div class="msg-sidebar-header">
          <h2>Messages</h2>
          <button class="btn btn-primary" onclick="openNewConv()" style="font-size:.75rem;padding:.32rem .8rem">
            ✏️ Nouveau
          </button>
        </div>
        <div class="msg-search">
          <input type="text" id="convSearch" placeholder="Rechercher une conversation…" oninput="filtrerConvs()">
        </div>
        <div class="msg-convs" id="convsList">
          <div style="padding:2rem;text-align:center"><span class="spinner"></span></div>
        </div>
      </div>

      <!-- MAIN : messages -->
      <div class="msg-main" id="msgMain">
        <div class="msg-empty" id="msgEmpty">
          <span class="msg-empty-icon">💬</span>
          <p style="font-weight:600;color:var(--text)">Sélectionnez une conversation</p>
          <p style="font-size:.83rem">ou commencez-en une nouvelle</p>
          <button class="btn btn-primary" onclick="openNewConv()" style="margin-top:.5rem">
            ✏️ Nouvelle conversation
          </button>
        </div>

        <div id="msgConvPanel" style="display:none;height:100%;flex-direction:column;overflow:hidden">
          <div class="msg-header">
            <div class="msg-header-info">
              <div class="msg-header-avatar" id="chatAvatar"></div>
              <div>
                <div class="msg-header-name" id="chatName"></div>
                <div class="msg-header-role" id="chatRole"></div>
              </div>
            </div>
            <button class="btn btn-ghost" onclick="supprimerConv()" style="font-size:.75rem;padding:.3rem .7rem;color:#f87171;border-color:rgba(239,68,68,.3)">
              🗑 Supprimer
            </button>
          </div>
          <div class="msg-messages" id="msgMessages"></div>
          <div class="msg-compose">
            <textarea id="msgInput" placeholder="Votre message…" rows="1"
                      onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();envoyerMsg()}"
                      oninput="autoResize(this)"></textarea>
            <button class="msg-send-btn" onclick="envoyerMsg()">➤</button>
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

<!-- Modal nouvelle conversation -->
<div class="msg-new-modal" id="newConvModal">
  <div class="msg-new-box">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
      <h3 style="margin:0">✏️ Nouvelle conversation</h3>
      <button class="btn btn-ghost" onclick="closeNewConv()" style="font-size:.8rem;padding:.25rem .6rem">✕</button>
    </div>
    <div class="form-group" style="margin-bottom:.5rem">
      <label>Rechercher un utilisateur</label>
      <input type="text" id="userSearch" placeholder="Nom, prénom…"
             style="width:100%;padding:.6rem .9rem;border-radius:8px;border:1px solid var(--border);background:var(--surface2);color:var(--text);font-family:inherit;font-size:.85rem;outline:none"
             oninput="chercherUsers()">
    </div>
    <div class="msg-search-results" id="userResults" style="display:none"></div>
  </div>
</div>

<?php include 'php/modal_auth.php'; ?>
<script src="js/script.js"></script>
<script>
const MSG_API = _BASE + '/php/messages.php';
let currentConvId = null;
let currentDestId = null;
let allConvs      = [];
let pollTimer     = null;

async function msgApi(p={}){
  const r=await fetch(MSG_API,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(p)});
  return r.json();
}
async function msgApiGet(p={}){
  const qs=new URLSearchParams(p).toString();
  return (await fetch(qs?`${MSG_API}?${qs}`:MSG_API)).json();
}

function formatTime(dt){
  const d=new Date(dt);
  const now=new Date();
  const diffH=(now-d)/3600000;
  if(diffH<24) return d.toLocaleTimeString('fr-FR',{hour:'2-digit',minute:'2-digit'});
  if(diffH<48) return 'Hier';
  return d.toLocaleDateString('fr-FR',{day:'2-digit',month:'2-digit'});
}
function initiales(prenom,nom){
  return ((prenom?.[0]||'')+(nom?.[0]||'')).toUpperCase()||'?';
}
function roleLabel(role){
  return {admin:'Admin',pilote:'Pilote',etudiant:'Étudiant'}[role]||role;
}

// ── CONVERSATIONS ─────────────────────────────────────────────
async function loadConvs(){
  allConvs=await msgApiGet({action:'conversations'});
  renderConvs(allConvs);
}

function filtrerConvs(){
  const q=(document.getElementById('convSearch')?.value||'').toLowerCase();
  if(!q){renderConvs(allConvs);return;}
  renderConvs(allConvs.filter(c=>{
    const other=getOther(c);
    return (other.prenom+' '+other.nom).toLowerCase().includes(q);
  }));
}

function getOther(c){
  const myId=window._myId;
  if(c.u1_id==myId) return {id:c.u2_id,prenom:c.u2_prenom,nom:c.u2_nom,role:c.u2_role};
  return {id:c.u1_id,prenom:c.u1_prenom,nom:c.u1_nom,role:c.u1_role};
}

function renderConvs(liste){
  const cont=document.getElementById('convsList');
  if(!liste.length){
    cont.innerHTML='<div style="padding:2rem;text-align:center;color:var(--muted);font-size:.85rem">Aucune conversation.<br>Commencez à écrire !</div>';
    return;
  }
  cont.innerHTML=liste.map(c=>{
    const other=getOther(c);
    const ini=initiales(other.prenom,other.nom);
    const preview=c.dernier_msg?(c.dernier_msg.length>40?c.dernier_msg.substring(0,40)+'…':c.dernier_msg):'Aucun message';
    return `<div class="msg-conv-item${c.id==currentConvId?' active':''}" data-conv-id="${c.id}" data-dest-id="${other.id}" data-prenom="${e(other.prenom)}" data-nom="${e(other.nom)}" data-role="${e(other.role)}" onclick="ouvrirConvFromEl(this)">
      <div class="msg-conv-avatar">${ini}</div>
      <div class="msg-conv-info">
        <div class="msg-conv-name">${e(other.prenom+' '+other.nom)}</div>
        <div class="msg-conv-preview">${e(preview)}</div>
      </div>
      <div class="msg-conv-meta">
        <span class="msg-conv-time">${formatTime(c.updated_at)}</span>
        ${c.nb_non_lus>0?`<span class="msg-badge">${c.nb_non_lus}</span>`:''}
      </div>
    </div>`;
  }).join('');
}

// ── OUVRIR CONVERSATION ───────────────────────────────────────
function ouvrirConvFromEl(el){
  ouvrirConv(+el.dataset.convId,+el.dataset.destId,el.dataset.prenom,el.dataset.nom,el.dataset.role,el);
}
async function ouvrirConv(convId, destId, prenom, nom, role, elClicked){
  currentConvId=convId;
  currentDestId=destId;

  // Mettre à jour l'en-tête
  document.getElementById('chatAvatar').textContent=initiales(prenom,nom);
  document.getElementById('chatName').textContent=prenom+' '+nom;
  document.getElementById('chatRole').textContent=roleLabel(role);

  // Afficher le panel
  document.getElementById('msgEmpty').style.display='none';
  const panel=document.getElementById('msgConvPanel');
  panel.style.display='flex';

  // Marquer comme active dans la sidebar
  document.querySelectorAll('.msg-conv-item').forEach(el=>el.classList.remove('active'));
  if(elClicked) elClicked.classList.add('active');

  await chargerMessages();

  // Polling auto toutes les 5s
  clearInterval(pollTimer);
  pollTimer=setInterval(async()=>{
    if(currentConvId) await chargerMessages(true);
  },5000);

  // Rafraîchir liste convs pour mettre à jour le badge
  await loadConvs();
  document.querySelector(`.msg-conv-item[data-conv-id="${convId}"]`)?.classList.add('active');
  updateMsgBadge();
}

// ── MESSAGES ─────────────────────────────────────────────────
async function chargerMessages(silent=false){
  const msgs=await msgApiGet({action:'messages',conversation_id:currentConvId});
  if(!Array.isArray(msgs)) return;
  renderMessages(msgs);
}

function renderMessages(msgs){
  const cont=document.getElementById('msgMessages');
  const myId=window._myId;
  const wasAtBottom=cont.scrollHeight-cont.scrollTop-cont.clientHeight<80;

  cont.innerHTML=msgs.map(m=>{
    const mine=m.expediteur_id==myId;
    return `<div class="msg-bubble-wrap ${mine?'mine':'theirs'}">
      <div class="msg-bubble">${e(m.contenu)}</div>
      <div class="msg-time">${new Date(m.created_at).toLocaleTimeString('fr-FR',{hour:'2-digit',minute:'2-digit'})}</div>
    </div>`;
  }).join('');

  if(wasAtBottom || msgs.length<5) cont.scrollTop=cont.scrollHeight;
}

// ── ENVOYER ───────────────────────────────────────────────────
async function envoyerMsg(){
  const input=document.getElementById('msgInput');
  const contenu=input.value.trim();
  if(!contenu) return;
  if(!currentDestId) return;

  input.value='';
  autoResize(input);

  const res=await msgApi({action:'envoyer',destinataire_id:currentDestId,contenu});
  if(res.ok){
    if(!currentConvId){
      currentConvId=res.conversation_id;
    }
    await chargerMessages();
    await loadConvs();
    updateMsgBadge();
  }
}

function autoResize(ta){
  ta.style.height='auto';
  ta.style.height=Math.min(ta.scrollHeight,120)+'px';
}

// ── SUPPRIMER CONVERSATION ────────────────────────────────────
async function supprimerConv(){
  if(!confirm('Supprimer cette conversation et tous ses messages ?')) return;
  await msgApi({action:'supprimer_conversation',conversation_id:currentConvId});
  currentConvId=null; currentDestId=null;
  document.getElementById('msgEmpty').style.display='flex';
  document.getElementById('msgConvPanel').style.display='none';
  clearInterval(pollTimer);
  await loadConvs();
}

// ── NOUVELLE CONVERSATION ─────────────────────────────────────
function openNewConv(){
  document.getElementById('newConvModal').classList.add('active');
  document.getElementById('userSearch').value='';
  document.getElementById('userResults').style.display='none';
  setTimeout(()=>document.getElementById('userSearch').focus(),100);
}
function closeNewConv(){
  document.getElementById('newConvModal').classList.remove('active');
}
document.getElementById('newConvModal').addEventListener('click',e=>{
  if(e.target===e.currentTarget) closeNewConv();
});

let searchTimer;
function chercherUsers(){
  clearTimeout(searchTimer);
  searchTimer=setTimeout(async()=>{
    const q=document.getElementById('userSearch').value.trim();
    if(q.length<2){document.getElementById('userResults').style.display='none';return;}
    const users=await msgApiGet({action:'chercher_utilisateur',q});
    const cont=document.getElementById('userResults');
    if(!users.length){
      cont.innerHTML='<div style="padding:.75rem 1rem;font-size:.82rem;color:var(--muted)">Aucun utilisateur trouvé.</div>';
      cont.style.display='block'; return;
    }
    cont.innerHTML=users.map(u=>`
      <div class="msg-search-result" data-uid="${u.id}" data-prenom="${e(u.prenom)}" data-nom="${e(u.nom)}" data-role="${e(u.role)}" onclick="startConvFromEl(this)">
        <div class="msg-conv-avatar" style="width:32px;height:32px;font-size:.72rem">${initiales(u.prenom,u.nom)}</div>
        <div>
          <div class="msg-search-result-name">${e(u.prenom+' '+u.nom)}</div>
          <div class="msg-search-result-role">${roleLabel(u.role)}${u.entreprise_nom?' · '+e(u.entreprise_nom):''}</div>
        </div>
      </div>`).join('');
    cont.style.display='block';
  },250);
}

function startConvFromEl(el){startConvWith(+el.dataset.uid,el.dataset.prenom,el.dataset.nom,el.dataset.role);}
async function startConvWith(destId, prenom, nom, role){
  closeNewConv();
  // Chercher si conv existante
  const existing=allConvs.find(c=>getOther(c).id==destId);
  if(existing){
    ouvrirConv(existing.id,destId,prenom,nom,role);
    return;
  }
  // Sinon ouvrir un panel vide prêt à écrire
  currentConvId=null;
  currentDestId=destId;
  document.getElementById('chatAvatar').textContent=initiales(prenom,nom);
  document.getElementById('chatName').textContent=prenom+' '+nom;
  document.getElementById('chatRole').textContent=roleLabel(role);
  document.getElementById('msgEmpty').style.display='none';
  const panel=document.getElementById('msgConvPanel');
  panel.style.display='flex';
  document.getElementById('msgMessages').innerHTML='<div style="text-align:center;padding:2rem;color:var(--muted);font-size:.83rem">Envoyez votre premier message 👋</div>';
  document.getElementById('msgInput').focus();
}

// ── BADGE GLOBAL ──────────────────────────────────────────────
async function updateMsgBadge(){
  const r=await fetch(MSG_API+'?action=nb_non_lus');
  const d=await r.json();
  const nb=d.nb||0;
  // Mettre à jour le badge dans la navbar et dans le lien de la page
  document.querySelectorAll('.msg-nav-badge').forEach(el=>{
    el.textContent=nb;
    el.style.display=nb>0?'inline-flex':'none';
  });
}

// ── INIT ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async ()=>{
  await loadUser();
  if(!currentUser){
    document.getElementById('notConnected').style.display='block';
    return;
  }
  window._myId=currentUser.id;
  document.getElementById('msgApp').style.display='flex';
  await loadConvs();
  updateMsgBadge();

  // Ouvrir directement une conv si ?with=userId dans l'URL
  const params=new URLSearchParams(window.location.search);
  const withId=params.get('with');
  const withName=params.get('name')||'';
  const withRole=params.get('role')||'etudiant';
  if(withId){
    const existing=allConvs.find(c=>getOther(c).id==withId);
    if(existing){
      const o=getOther(existing);
      ouvrirConv(existing.id,o.id,o.prenom,o.nom,o.role);
    } else {
      const parts=decodeURIComponent(withName).split(' ');
      startConvWith(parseInt(withId),parts[0]||'',parts.slice(1).join(' ')||'',withRole);
    }
  }
});
</script>
</body>
</html>
