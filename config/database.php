<?php
// =====================================================
// FLEURA — Configuration de la base de données
// =====================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Valeurs par défaut (local) ---
$host = getenv('DB_HOST') ?: '127.0.0.1';
$dbname = getenv('DB_NAME') ?: 'fleura';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$port = getenv('DB_PORT') ?: '3306';

// --- Constantes ---
define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost/fleura');
define('CURRENCY', getenv('CURRENCY') ?: 'DA');
define('DELIVERY_FEE', (float)(getenv('DELIVERY_FEE') ?: 600));

// --- Initialisation du driver (pour rassurer Intelephense) ---
$driver = 'unknown';

try {
    // --- Connexion en ligne (Supabase PostgreSQL) ---
    if (getenv('DB_HOST')) {
        // Résoudre l'adresse IPv4 (contourne le problème IPv6)
        $hostaddr = gethostbyname($host);
        // Si la résolution échoue, on garde l'hôte original
        if ($hostaddr === $host) {
            $hostaddr = $host;
        }
        // DSN : on utilise hostaddr pour forcer IPv4, et sslmode=require
        $dsn = "pgsql:hostaddr=$hostaddr;port=$port;dbname=$dbname;sslmode=require";
        $driver = 'pgsql';
    } else {
        // --- Local (MySQL) ---
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $driver = 'mysql';
    }

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

} catch (PDOException $e) {
    die("Erreur de connexion ($driver) : " . $e->getMessage());
}