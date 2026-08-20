<?php
// =====================================================
// FLEURA — Configuration de la base de données
// Compatible WAMP / localhost
// =====================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---- LIGNE TEMPORAIRE : vide le panier corrompu ----
// À RETIRER — maintenant que tout fonctionne, on commente cette ligne.
// $_SESSION['cart'] = [];
// -----------------------------------------------------

define('DB_HOST', 'localhost');
define('DB_NAME', 'fleura');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SITE_NAME', 'FLEURA');
define('SITE_URL', 'http://localhost/fleura');
define('CURRENCY', 'DA');

// Frais de livraison par défaut (en DA)
define('DELIVERY_FEE', 600);

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}