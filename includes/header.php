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

    <title>
        <?= e($page_title ?? 'FLEURA — L\'élégance, simplement.') ?>
    </title>

    <link rel="icon" type="image/png" href="<?= e(SITE_URL) ?>/assets/images/logo.png">

    <meta
        name="description"
        content="Boutique Fleura — Magasin de mode féminine à Koléa, Algérie. Découvrez nos collections de robes, vêtements, sacs, foulards et accessoires."
    >

    <!-- =================================================
         GOOGLE FONTS
         ================================================= -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- =================================================
         CSS PRINCIPAL
         ================================================= -->

    <link
        rel="stylesheet"
        href="<?= e(SITE_URL) ?>/assets/css/style.css"
    >

</head>


<body>

<div class="page-wrapper">


    <!-- =================================================
         NAVBAR
         ================================================= -->

    <header class="navbar" id="navbar">

        <div class="navbar__inner">


            <!-- =================================================
                 LOGO
                 ================================================= -->

            <a
                href="<?= e(SITE_URL) ?>/index.php"
                class="navbar__logo fleura-logo"
            >

                <img
                    src="<?= e(SITE_URL) ?>/assets/images/logo.png"
                    alt="Fleura"
                >

            </a>


            <!-- =================================================
                 MENU
                 ================================================= -->

            <nav
                class="navbar__menu"
                id="navMenu"
            >


                <!-- ================= ACCUEIL ================= -->

                <a
                    href="<?= e(SITE_URL) ?>/index.php"
                    class="<?= $current_file === 'index.php' ? 'active' : '' ?>"
                >
                    Accueil
                </a>


                <!-- ================= COLLECTION ================= -->

                <a
                    href="<?= e(SITE_URL) ?>/shop.php"
                    class="<?=
                        $current_file === 'shop.php'
                        && empty($_GET['type'])
                        && empty($_GET['category'])
                        && empty($_GET['filter'])
                        ? 'active'
                        : ''
                    ?>"
                >
                    Collection
                </a>


                <!-- ================= NOUVEAUTÉS ================= -->

                <a
                    href="<?= e(SITE_URL) ?>/shop.php?filter=new"
                    class="<?=
                        $current_file === 'shop.php'
                        && ($_GET['filter'] ?? '') === 'new'
                        ? 'active'
                        : ''
                    ?>"
                >
                    Nouveautés
                </a>


                <!-- =================================================
                     ARTICLES
                     ================================================= -->

                <div
                    class="navbar__dropdown"
                    id="articlesDropdown"
                >

                    <button
                        type="button"
                        class="navbar__dropdown-trigger"
                        aria-expanded="false"
                    >

                        <span>Articles</span>

                        <svg
                            width="10"
                            height="10"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >

                            <path d="m6 9 6 6 6-6"/>

                        </svg>

                    </button>


                    <div class="navbar__dropdown-panel">

                        <?php foreach ($clothing_types as $type): ?>

                            <a
                                href="<?= e(SITE_URL) ?>/shop.php?type=<?= urlencode($type) ?>"
                                class="<?=
                                    (($_GET['type'] ?? '') === $type)
                                    ? 'active'
                                    : ''
                                ?>"
                            >

                                <?= e($type) ?>

                            </a>

                        <?php endforeach; ?>

                    </div>

                </div>


                <!-- ================= SACS ================= -->

                <a
                    href="<?= e(SITE_URL) ?>/shop.php?category=3"
                    class="<?=
                        (($_GET['category'] ?? '') == '3')
                        ? 'active'
                        : ''
                    ?>"
                >
                    Sacs
                </a>


                <!-- ================= FOULARDS ================= -->

                <a
                    href="<?= e(SITE_URL) ?>/shop.php?category=4"
                    class="<?=
                        (($_GET['category'] ?? '') == '4')
                        ? 'active'
                        : ''
                    ?>"
                >
                    Foulards
                </a>


                <!-- ================= MULES ================= -->

                <a
                    href="<?= e(SITE_URL) ?>/shop.php?category=5"
                    class="<?=
                        (($_GET['category'] ?? '') == '5')
                        ? 'active'
                        : ''
                    ?>"
                >
                    Mules
                </a>


                <!-- ================= ACCESSOIRES ================= -->

                <a
                    href="<?= e(SITE_URL) ?>/shop.php?category=6"
                    class="<?=
                        (($_GET['category'] ?? '') == '6')
                        ? 'active'
                        : ''
                    ?>"
                >
                    Accessoires
                </a>

            </nav>


            <!-- =================================================
                 ACTIONS
                 ================================================= -->

            <div class="navbar__actions">


                <!-- ================= RECHERCHE ================= -->

                <button
                    type="button"
                    class="navbar__icon"
                    id="searchToggle"
                    aria-label="Recherche"
                    aria-expanded="false"
                    aria-controls="searchPanel"
                >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="11" cy="11" r="7" />
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                </button>


                <!-- ================= PANIER ================= -->

                <a
                    href="<?= e(SITE_URL) ?>/cart.php"
                    class="navbar__icon navbar__cart"
                    aria-label="Panier"
                >

                    <svg
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                    >

                        <path
                            d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"
                        />

                        <path d="M3 6h18"/>

                        <path d="M16 10a4 4 0 0 1-8 0"/>

                    </svg>


                    <?php if ($cart_count > 0): ?>

                        <span class="navbar__cart-count">
                            <?= $cart_count ?>
                        </span>

                    <?php endif; ?>

                </a>


                <!-- ================= ADMINISTRATION ================= -->

                <a
                    href="<?= e(SITE_URL) ?>/admin/login.php"
                    class="navbar__icon"
                    aria-label="Administration"
                >

                    <svg
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                    >

                        <rect
                            x="4"
                            y="11"
                            width="16"
                            height="10"
                            rx="2"
                        />

                        <path d="M8 11V7a4 4 0 0 1 8 0v4"/>

                    </svg>

                </a>


                <!-- =================================================
                     BURGER MOBILE
                     ================================================= -->

                <button
                    class="navbar__burger"
                    id="burger"
                    type="button"
                    aria-label="Ouvrir le menu"
                    aria-expanded="false"
                    aria-controls="navMenu"
                >

                    <span></span>
                    <span></span>
                    <span></span>

                </button>


            </div>

        </div>


        <!-- =================================================
             PANNEAU RECHERCHE
             ================================================= -->

        <div class="search-panel" id="searchPanel">
            <div class="search-panel__inner">

                <form action="<?= e(SITE_URL) ?>/shop.php" method="get" class="search-panel__form" id="searchForm">

                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="11" cy="11" r="7" />
                        <path d="m21 21-4.3-4.3"/>
                    </svg>

                    <input
                        type="text"
                        name="search"
                        id="searchInput"
                        placeholder="Rechercher une robe, un sac, un foulard…"
                        autocomplete="off"
                        value="<?= e($_GET['search'] ?? '') ?>"
                    >

                    <button type="submit" class="search-panel__submit" aria-label="Lancer la recherche">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M13 6l6 6-6 6"/>
                        </svg>
                    </button>

                </form>

                <button type="button" class="search-panel__close" id="searchClose" aria-label="Fermer la recherche">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M18 6 6 18M6 6l12 12"/>
                    </svg>
                </button>

            </div>
        </div>

    </header>

    <div class="search-overlay" id="searchOverlay"></div>


    <!-- =================================================
         CONTENU
         ================================================= -->

    <main>