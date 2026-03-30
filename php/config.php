<?php
define('DB_HOST','localhost'); define('DB_NAME','web4all');
define('DB_USER','root');      define('DB_PASS','');
define('DB_CHARSET','utf8mb4');define('SITE_URL','http://localhost/ProjetWEB');

function getDB(): PDO {
    static $pdo=null;
    if (!$pdo) {
        try { $pdo=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET,DB_USER,DB_PASS,
            [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
        } catch(PDOException $e){ http_response_code(500); die(json_encode(['error'=>'BDD inaccessible : '.$e->getMessage()])); }
    }
    return $pdo;
}

if (session_status() === PHP_SESSION_NONE) session_start();
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
