<?php
// =====================================================
// FLEURA — Configuration de la base de données
// Supporte : Localhost (MySQL) + Supabase (PostgreSQL)
// =====================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Détecter l'environnement
$is_local = (strpos($_SERVER['SERVER_NAME'] ?? '', 'localhost') !== false ||  
             strpos($_SERVER['SERVER_NAME'] ?? '', '127.0.0.1') !== false);

if ($is_local) {
    // ============================================
    // LOCAL : MySQL (Wamp)
    // ============================================
    $DB_HOST = 'localhost';
    $DB_NAME = 'fleura';
    $DB_USER = 'root';
    $DB_PASS = '';

    try {
        $pdo = new PDO(
            "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
            $DB_USER,
            $DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    } catch (PDOException $e) {
        die("Erreur de connexion MySQL : " . $e->getMessage());
    }

    define('BASE_URL', '/fleura'); // adapte selon ton dossier local

} else {
    // ============================================
    // PRODUCTION : Supabase (PostgreSQL) sur Render
    // ============================================
    $supabase_password = getenv('SUPABASE_PASSWORD') ?: 'fleuraecommerce';

    $database_url = "postgresql://postgres.hojanxmjdnkvcqhtrtds:$supabase_password@aws-1-eu-west-1.pooler.supabase.com:5432/postgres";

    $url = parse_url($database_url);
    $host = $url["host"] ?? '';
    $port = $url["port"] ?? '5432';
    $user = $url["user"] ?? '';
    $password = $url["pass"] ?? '';
    $dbname = ltrim($url["path"] ?? '', '/');

    try {
        $pdo = new PDO(
            "pgsql:host=$host;port=$port;dbname=$dbname",
            $user,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    } catch (PDOException $e) {
        die("Erreur de connexion Supabase : " . $e->getMessage());
    }

    define('BASE_URL', 'https://fleura-57g2.onrender.com');
}

// Définir les constantes communes
define('SITE_NAME', 'FLEURA');
define('CURRENCY', 'DA');
define('DELIVERY_FEE', 600);

// Alias pour compatibilité
define('SITE_URL', BASE_URL);

// Variables pour compatibilité avec l'ancien code
$conn = $pdo;
$db = $pdo;
$connect = $pdo;
$connexion = $pdo;
