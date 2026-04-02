<?php require_once 'php/config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Accueil – Web4All | Stages & Alternances</title>
  <meta name="description" content="Trouvez votre stage ou alternance en informatique parmi nos offres dans toute la France.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script>(function(){var t=localStorage.getItem("w4a_theme")||"dark";document.documentElement.setAttribute("data-theme",t);})();</script>
  <script>window.SITE_URL="<?= SITE_URL ?>";</script>
</head>
<body>

<div class="cookie-banner" id="cookieBanner">
  <p>Ce site utilise des cookies pour améliorer votre expérience. <a href="mention_legales.php">En savoir plus</a></p>
  <button id="acceptCookies">Accepter</button>
</div>

<header class="big-header">
  <h1><span class="accent">Web4All</span> – Stages & Alternances</h1>
  <p class="header-sub">Trouvez votre prochaine opportunité professionnelle</p>
</header>

<nav class="navbar" id="navbar">
  <button class="burger-btn" id="burgerBtn" aria-label="Menu" aria-expanded="false">
    <span></span><span></span><span></span>
  </button>
  <div class="nav-links" id="navLinks">
    <a href="index.php" id="on">Accueil</a>
    <a href="entreprises.php">Entreprises</a>
    <a href="profil.php">Profil</a>
    <a href="mention_legales.php">Mentions légales</a>
  </div>
  <div class="nav-right" id="navRight">
    <button id="themeToggle" class="nav-btn" onclick="toggleTheme()" title="Changer le thème">🌸 Mode clair</button>
  </div>
</nav>

<?php include 'php/modal_auth.php'; ?>

<main class="container main-content">
  <h1>Offres de <span class="accent">stages</span> & alternances</h1>

  <section class="search-section">
    <input type="text" id="searchInput" placeholder="Rechercher (développeur, réseau, Paris…)">

    <select id="filterType">
      <option value="">Type de contrat</option>
      <option value="stage">Stage</option>
      <option value="alternance">Alternance</option>
    </select>

    <select id="filterLieu">
      <option value="">Lieu</option>
      <option value="Rouen">Rouen</option>
      <option value="Le Havre">Le Havre</option>
      <option value="Caen">Caen</option>
      <option value="Paris">Paris</option>
      <option value="Lille">Lille</option>
    </select>

    <select id="filterDuree">
      <option value="">Durée</option>
      <option value="3 mois">3 mois</option>
      <option value="4 mois">4 mois</option>
      <option value="6 mois">6 mois</option>
      <option value="2 ans">2 ans (alternance)</option>
    </select>

    <select id="filterCompetence">
      <option value="">Compétence</option>
    </select>
  </section>

  <div class="stats-bar"><span id="statsText">Chargement…</span></div>

  <section id="offresContainer" class="offres">
    <div class="empty-state"><span class="spinner"></span></div>
  </section>

  <section class="map-section">
    <h2>Localisation des offres</h2>
    <div id="map"></div>
  </section>
</main>

<footer>
  <span class="footer-brand">Web4All</span>
  <span>© 2026 – Projet pédagogique</span>
  <a href="mention_legales.php">Mentions légales</a>
</footer>

<script src="js/script.js"></script>
<!-- Leaflet.js (OpenStreetMap) – gratuit, sans clé API -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</body>
</html>
