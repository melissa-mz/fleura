<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$admin_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($page_title ?? 'Administration') ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- CSS principal -->
    <link
        rel="stylesheet"
        href="<?= e(SITE_URL) ?>/assets/css/style.css"
    >

    <style>

        .admin-page-content {
            max-width: var(--container);
            margin: 0 auto;
            padding: var(--space-5) var(--space-4) var(--space-8);
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;

            margin-bottom: var(--space-5);

            padding-bottom: var(--space-3);

            border-bottom: 1px solid var(--color-border);
        }


        .admin-header h2 {
            font-family: var(--font-serif);
            font-size: 1.8rem;
            font-weight: 500;

            color: var(--color-text);

            margin: 0;
        }


        .admin-header span {
            font-size: 0.8rem;
            color: var(--color-text-muted);
        }


        .navbar__actions {
            gap: 1rem;
        }

        .admin-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 65px;
}

.admin-logo img {
    width: 180px;
    height: 65px;
    object-fit: contain;
    display: block;
}


        .navbar__menu {
            display: flex;
            align-items: center;
            gap: 2rem;
        }


        .navbar__menu a {
            position: relative;

            text-decoration: none;

            font-size: 0.78rem;

            color: var(--color-text-muted);

            transition:
                color 0.3s ease;
        }


        .navbar__menu a:hover {
            color: var(--color-text);
        }


        .navbar__menu a.active {
            color: var(--color-text);
        }


        .navbar__menu a.active::after {
            content: "";

            position: absolute;

            left: 0;
            right: 0;

            bottom: -8px;

            height: 1px;

            background: var(--color-text);
        }

        .admin-user-name {
            font-size: 0.75rem;

            color: var(--color-text-muted);

            white-space: nowrap;
        }


        .admin-logout {
            display: flex;

            align-items: center;
            justify-content: center;

            width: 38px;
            height: 38px;

            color: var(--color-text);

            border-radius: 50%;

            transition:
                background 0.3s ease,
                transform 0.3s ease;
        }


        .admin-logout:hover {
            background: var(--color-bg-soft);

            transform: translateY(-2px);
        }

        .navbar__burger {
            border: none;

            background: transparent;

            cursor: pointer;

            padding: 8px;
        }


        .navbar__burger span {
            display: block;

            width: 22px;
            height: 1px;

            margin: 5px 0;

            background: var(--color-text);
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 850px) {

            .navbar__menu {
                display: none;
            }

            .admin-header {
                align-items: flex-start;

                flex-direction: column;

                gap: 8px;
            }

        }


        @media (max-width: 560px) {

            .admin-page-content {
                padding-left: 20px;
                padding-right: 20px;
            }

            .admin-logo img {
                width: 105px;
                height: 42px;
            }

            .admin-user-name {
                display: none;
            }

        }

    </style>

</head>


<body>


<div class="page-wrapper">


    <!-- =====================================================
         NAVBAR ADMIN
    ===================================================== -->

    <nav class="navbar">

        <div class="navbar__inner">


            <a
                href="<?= e(SITE_URL) ?>/admin/dashboard.php"
                class="navbar__logo admin-logo"
                aria-label="Fleura Administration"
            >

                <img
                    src="<?= e(SITE_URL) ?>/assets/images/logo.png"
                    alt="Fleura"
                >

            </a>


            <div class="navbar__menu">


                <!-- DASHBOARD -->

                <a
                    href="<?= e(SITE_URL) ?>/admin/dashboard.php"
                    class="<?= $admin_page === 'dashboard.php' ? 'active' : '' ?>"
                >
                    Tableau de bord
                </a>


                <!-- COMMANDES -->

                <a
                    href="<?= e(SITE_URL) ?>/admin/orders.php"
                    class="<?= 
                        $admin_page === 'orders.php' ||
                        $admin_page === 'order-details.php'
                        ? 'active'
                        : ''
                    ?>"
                >
                    Commandes
                </a>


                <!-- PRODUITS -->

                <a
                    href="<?= e(SITE_URL) ?>/admin/products.php"
                    class="<?= 
                        in_array(
                            $admin_page,
                            [
                                'products.php',
                                'add-product.php',
                                'edit-product.php'
                            ]
                        )
                        ? 'active'
                        : ''
                    ?>"
                >
                    Produits
                </a>


            </div>


            <!-- =================================================
                 ACTIONS
            ================================================= -->

            <div class="navbar__actions">


                <!-- ADMIN CONNECTÉ -->

                <span class="admin-user-name">

                    <?= e($admin_name) ?>

                </span>


                <!-- DÉCONNEXION -->

                <a
                    href="<?= e(SITE_URL) ?>/admin/logout.php"
                    class="navbar__icon admin-logout"
                    title="Déconnexion"
                    aria-label="Déconnexion"
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
                            d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"
                        />

                        <polyline
                            points="16 17 21 12 16 7"
                        />

                        <line
                            x1="21"
                            y1="12"
                            x2="9"
                            y2="12"
                        />

                    </svg>

                </a>


            </div>


            <!-- =================================================
                 BURGER MOBILE
            ================================================= -->

            <button
                class="navbar__burger"
                id="navBurger"
                type="button"
                aria-label="Ouvrir le menu"
            >

                <span></span>
                <span></span>
                <span></span>

            </button>


        </div>

    </nav>


    <!-- =====================================================
         CONTENU ADMIN
    ===================================================== -->

    <main>

        <div class="admin-page-content">


            <!-- =================================================
                 TITRE DE LA PAGE
            ================================================= -->

            <div class="admin-header">

                <h2>
                    <?= e($page_title ?? 'Administration') ?>
                </h2>


                <span>

                    Connecté :
                    <?= e($admin_name) ?>

                </span>

            </div>