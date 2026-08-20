<?php
// =====================================================
// FLEURA — En-tête du site (front-office)
// =====================================================
require_once __DIR__ . '/functions.php';
$cart_count = get_cart_count();
$categories = get_categories();
$clothing_types = get_clothing_types();
$current_file = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title ?? 'FLEURA — L\'élégance, simplement.') ?></title>
    <meta name="description" content="Boutique Fleura — Magasin de mode féminine à Koléa, Algérie. Découvrez nos collections de robes, vêtements, sacs, foulards et accessoires.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(SITE_URL) ?>/assets/css/style.css">
</head>
<body>
    <div class="page-wrapper">

    <!-- ============ NAVBAR ============ -->
    <header class="navbar" id="navbar">
        <div class="navbar__inner">
            <!-- Logo -->
            <a href="<?= e(SITE_URL) ?>/index.php" class="navbar__logo fleura-logo">
                <img 
                    src="<?= e(SITE_URL) ?>/assets/images/logo.png" 
                    alt="Fleura"
                >
            </a>

            <!-- Menu principal -->
            <nav class="navbar__menu" id="navMenu">
                <a href="<?= e(SITE_URL) ?>/index.php" class="<?= $current_file === 'index.php' ? 'active' : '' ?>">Accueil</a>
                <a href="<?= e(SITE_URL) ?>/shop.php" class="<?= $current_file === 'shop.php' && empty($_GET['type']) && empty($_GET['category']) && empty($_GET['filter']) ? 'active' : '' ?>">Collection</a>
                <a href="<?= e(SITE_URL) ?>/shop.php?filter=new">Nouveautés</a>

                <!-- Dropdown Articles -->
                <div class="navbar__dropdown" id="articlesDropdown">
                    <button type="button" class="navbar__dropdown-trigger">
                        Articles
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div class="navbar__dropdown-panel">
                        <?php foreach ($clothing_types as $type): ?>
                            <a href="<?= e(SITE_URL) ?>/shop.php?type=<?= urlencode($type) ?>" class="<?= (($_GET['type'] ?? '') === $type) ? 'active' : '' ?>"><?= e($type) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <a href="<?= e(SITE_URL) ?>/shop.php?category=3">Sacs</a>
                <a href="<?= e(SITE_URL) ?>/shop.php?category=4">Foulards</a>
                <a href="<?= e(SITE_URL) ?>/shop.php?category=5">Mules</a>
                <a href="<?= e(SITE_URL) ?>/shop.php?category=6">Accessoires</a>
            </nav>

            <!-- Actions (recherche, panier, admin) -->
            <div class="navbar__actions">
                <a href="<?= e(SITE_URL) ?>/shop.php" class="navbar__icon" aria-label="Recherche">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                </a>

                <a href="<?= e(SITE_URL) ?>/cart.php" class="navbar__icon navbar__cart" aria-label="Panier">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    <?php if ($cart_count > 0): ?>
                        <span class="navbar__cart-count"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>

                <!-- Icône Administration (cadenas) -->
                <a href="<?= e(SITE_URL) ?>/admin/login.php" class="navbar__icon" aria-label="Administration">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="4" y="11" width="16" height="10" rx="2" />
                        <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                    </svg>
                </a>

                <button class="navbar__burger" id="burger" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </header>

    <main>