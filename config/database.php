<?php
// =====================================================
// FLEURA — Configuration de la base de données
// Supporte : Localhost (MySQL) + Supabase (PostgreSQL)
// =====================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('SITE_NAME', 'FLEURA');
define('CURRENCY', 'DA');
define('DELIVERY_FEE', 600);

// ============================================================
// SITE_URL : détection automatique (local ou en ligne)
// ============================================================
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Si tu es en WAMP dans un sous‑dossier, ajuste ici :
// Exemple : '/fleura'  ou  '' si le site est à la racine
$base_path = '/fleura';

define('SITE_URL', $protocol . '://' . $host . $base_path);

// ============================================================
// CONNEXION À LA BASE DE DONNÉES (MySQL ou PostgreSQL)
// ============================================================

// Détection automatique : si on est sur Render, on utilise Supabase (PostgreSQL)
$is_render = getenv('RENDER') !== false || getenv('DB_HOST') !== false;

if ($is_render) {
    // --- Connexion Supabase (PostgreSQL) ---
    // Variables d'environnement définies sur Render
    $db_host   = getenv('DB_HOST') ?: 'aws-1-eu-west-1.pooler.supabase.com';
    $db_port   = getenv('DB_PORT') ?: '5432';
    $db_name   = getenv('DB_NAME') ?: 'postgres';
    $db_user   = getenv('DB_USER') ?: 'postgres.hojanxmjdnkvcqhtrtds';
    $db_pass   = getenv('DB_PASS') ?: 'fleuraecommerce';

    try {
        $pdo = new PDO(
            "pgsql:host=$db_host;port=$db_port;dbname=$db_name",
            $db_user,
            $db_pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (PDOException $e) {
        die("Erreur de connexion à Supabase : " . $e->getMessage());
    }

} else {
    // --- Connexion Localhost (MySQL) ---
    $db_host = 'localhost';
    $db_name = 'fleura';
    $db_user = 'root';
    $db_pass = ''; // ← ton mot de passe MySQL si tu en as un

    try {
        $pdo = new PDO(
            "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
            $db_user,
            $db_pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (PDOException $e) {
        die("Erreur de connexion à la base locale : " . $e->getMessage());
    }
}