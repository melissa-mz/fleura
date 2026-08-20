<?php
// =====================================================
// FLEURA — Configuration de la base de données
// Fonctionne en local (MySQL) et sur Render (Supabase)
// =====================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Valeurs par défaut (pour le développement local)
$host = getenv('DB_HOST') ?: '127.0.0.1';
$dbname = getenv('DB_NAME') ?: 'fleura';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$port = getenv('DB_PORT') ?: '3306'; // MySQL par défaut

// Définitions des constantes (SITE_URL, etc.)
define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost/fleura');
define('CURRENCY', getenv('CURRENCY') ?: 'DA');
define('DELIVERY_FEE', (float)(getenv('DELIVERY_FEE') ?: 600));

try {
    // Si un DB_HOST est défini (donc sur Render), on utilise PostgreSQL
    if (getenv('DB_HOST')) {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    } else {
        // Sinon, on est en local → MySQL
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    }

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}