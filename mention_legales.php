<?php require_once 'php/config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
  <title>Mentions légales – Web4All</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <script>(function(){var t=localStorage.getItem("w4a_theme")||"dark";document.documentElement.setAttribute("data-theme",t);})();</script>
  <script>window.SITE_URL="<?= SITE_URL ?>";</script>
</head>
<body>

<header class="big-header">
  <h1>Mentions <span class="accent">légales</span></h1>
  <p class="header-sub">Informations légales et RGPD</p>
</header>

<nav class="navbar" id="navbar">
  <button class="burger-btn" id="burgerBtn" aria-label="Menu" aria-expanded="false">
    <span></span><span></span><span></span>
  </button>
  <div class="nav-links" id="navLinks">
    <a href="index.php">Accueil</a>
    <a href="entreprises.php">Entreprises</a>
    <a href="profil.php">Profil</a>
    <a href="mention_legales.php" id="on">Mentions légales</a>
  </div>
  <div class="nav-right" id="navRight">
    <button id="themeToggle" class="nav-btn" onclick="toggleTheme()" title="Changer le thème">🌸 Mode clair</button>
  </div>
</nav>

<?php include 'php/modal_auth.php'; ?>

<main class="container main-content">
  <div class="mentions-content">

    <h2>1. Informations générales</h2>
    <p>Le présent site est édité par <strong>Web4All</strong>, société fictive dans le cadre d'un projet pédagogique.</p>
    <p><strong>Siège social :</strong> 12 rue des Développeurs, 76000 Rouen · <strong>Email :</strong> contact@web4all.fr · <strong>Directeur :</strong> Jean-Marc Dupuis</p>

    <h2>2. Hébergement</h2>
    <p>Site hébergé localement via <strong>XAMPP</strong> (Apache, MySQL, PHP). Hébergement définitif à préciser en production.</p>

    <h2>3. Objet du site</h2>
    <p>Web4All permet de consulter des offres de stages et alternances, de postuler via un formulaire, de gérer un profil candidat, et d'administrer les offres (panel admin). Prototype pédagogique.</p>

    <h2>4. Propriété intellectuelle</h2>
    <p>Tous les éléments du site (textes, design, code) sont protégés. Toute reproduction sans autorisation est interdite.</p>

    <h2>5. Données personnelles (RGPD)</h2>
    <h3>5.1 Données collectées</h3>
    <p>Nom, prénom, email, message de candidature, données de navigation.</p>
    <h3>5.2 Finalité</h3>
    <p>Traitement des candidatures, amélioration de l'expérience utilisateur.</p>
    <h3>5.3 Base légale</h3>
    <p>Consentement de l'utilisateur et intérêt légitime de Web4All.</p>
    <h3>5.4 Conservation</h3>
    <p>Données conservées <strong>12 mois</strong> maximum.</p>
    <h3>5.5 Droits</h3>
    <p>Accès, rectification, opposition, effacement, portabilité. Contact : <strong>dpo@web4all.fr</strong></p>

    <h2>6. Cookies</h2>
    <p>Cookies techniques (sessions PHP) et cookies de préférences. Un bandeau permet de gérer votre consentement.</p>

    <h2>7. Sécurité</h2>
    <p>Mots de passe hashés via <strong>bcrypt</strong>. Sessions PHP sécurisées. Requêtes protégées via <strong>PDO / requêtes préparées</strong>.</p>

    <h2>8. Modification</h2>
    <p>Dernière mise à jour : <strong>13 mars 2026</strong>.</p>
  </div>
</main>

<footer>
  <span class="footer-brand">Web4All</span>
  <span>© 2026 – Projet pédagogique</span>
  <a href="mention_legales.php">Mentions légales</a>
</footer>

<script src="js/script.js"></script>
</body>
</html>
