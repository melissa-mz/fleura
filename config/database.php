<?php
// =====================================================
// FLEURA — Configuration de la base de données
// Compatible WAMP / localhost / Render
// =====================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Variables d’environnement (Render) ou valeurs par défaut (localhost)
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'fleura';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

define('SITE_NAME', 'FLEURA');

// ⚠️ MODIFICATION POUR LE TEST NG-ROK
define('SITE_URL', 'https://atypical-easiness-sequester.ngrok-free.dev/fleura');

define('CURRENCY', getenv('CURRENCY') ?: 'DA');
define('DELIVERY_FEE', (float)(getenv('DELIVERY_FEE') ?: 600));

try {
    $pdo = new PDO(
        "mysql:host=" . $host . ";dbname=" . $dbname . ";charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}