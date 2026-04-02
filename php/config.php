<?php
// ── ENVIRONNEMENT ─────────────────────────────────────────────
// Désactiver l'affichage des erreurs en production
// Mettre à true uniquement en développement local
define('DEV_MODE', isset($_SERVER['SERVER_NAME']) && in_array($_SERVER['SERVER_NAME'], ['localhost','127.0.0.1','::1']));
if (DEV_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

// ── BASE DE DONNÉES ───────────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_NAME',    'web4all');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

// ── CONFIGURATION EMAIL (SMTP) ────────────────────────────────
// Remplacez ces valeurs par vos identifiants SMTP réels.
// Exemple Gmail : activez "Mots de passe d'application" dans votre compte Google.
// Exemple OVH / Infomaniak / autres hébergeurs : utilisez les identifiants SMTP fournis.
define('MAIL_HOST',      'smtp.gmail.com');      // Serveur SMTP
define('MAIL_PORT',      587);                   // 587 = STARTTLS, 465 = SSL
define('MAIL_USERNAME',  'votre@gmail.com');     // Identifiant SMTP (votre adresse)
define('MAIL_PASSWORD',  'votre_app_password');  // Mot de passe SMTP (App Password Gmail)
define('MAIL_FROM',      'votre@gmail.com');     // Adresse expéditeur
define('MAIL_FROM_NAME', 'Web4All');             // Nom affiché


if (!defined('SITE_URL')) {
    $proto    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Retirer le dernier segment /php /pilote /admin si on est dans un sous-dossier
    $script   = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $base     = rtrim(dirname(str_replace(['/pilote/','/admin/','/php/'], '/', $script)), '/');
    define('SITE_URL', $proto.'://'.$host.$base);
}

function getDB(): PDO {
    static $pdo=null;
    if (!$pdo) {
        try { $pdo=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET,DB_USER,DB_PASS,
            [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
        } catch(PDOException $e){ http_response_code(500); die(json_encode(['error'=>'BDD inaccessible : '.$e->getMessage()])); }
    }
    return $pdo;
}

// ── SESSION (expiration 2 minutes d'inactivité) ───────────────
const SESSION_LIFETIME = 120; // secondes

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    session_set_cookie_params(['lifetime' => SESSION_LIFETIME, 'samesite' => 'Lax']);
    session_start();
}

// Vérifier l'inactivité : si la dernière activité dépasse SESSION_LIFETIME, on détruit la session
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_LIFETIME) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

function currentUser():?array  { return $_SESSION['user']??null; }
function isAdmin():bool        { return ($_SESSION['user']['role']??'')==='admin'; }
function isPilote():bool        { return in_array($_SESSION['user']['role']??'',['admin','pilote']); }
function isEtudiant():bool     { return ($_SESSION['user']['role']??'')==='etudiant'; }
function isActif():bool        { return ($_SESSION['user']['statut']??'')==='actif'; }

function requireLogin():void   { if(!currentUser()){ header('Location: '.SITE_URL.'/index.php?auth=1'); exit; } }
function requirePilote():void  { if(!isPilote()||!isActif()){ header('Location: '.SITE_URL.'/index.php'); exit; } }
function requireAdmin():void   { if(!isAdmin()){ header('Location: '.SITE_URL.'/index.php'); exit; } }

function e(string $s):string { return htmlspecialchars($s,ENT_QUOTES,'UTF-8'); }
function jsonResponse(array $d,int $c=200):void {
    http_response_code($c); header('Content-Type: application/json; charset=utf-8');
    echo json_encode($d,JSON_UNESCAPED_UNICODE); exit;
}
