<?php
// API Compétences – liste publique
require_once __DIR__.'/config.php';
header('Content-Type: application/json; charset=utf-8');
$db = getDB();
$stmt = $db->query('SELECT * FROM competences ORDER BY categorie, nom');
jsonResponse($stmt->fetchAll());
