<?php

require_once __DIR__ . '/../includes/functions.php';

if (is_admin_logged_in()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {

        $error = 'Veuillez remplir tous les champs.';

    } else {

        global $pdo;

        $stmt = $pdo->prepare(
            "SELECT * FROM admins WHERE email = ?"
        );

        $stmt->execute([$email]);

        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];

            redirect('dashboard.php');

        } else {

            $error = 'Email ou mot de passe incorrect.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Administration — Fleura</title>


    <!-- =====================================================
         GOOGLE FONTS
    ====================================================== -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet"
    >


    <!-- =====================================================
         CSS PRINCIPAL
    ====================================================== -->

    <link
        rel="stylesheet"
        href="<?= e(SITE_URL) ?>/assets/css/style.css"
    >


    <style>

        /* =====================================================
           FLEURA — ADMIN LOGIN
        ===================================================== */

        :root {

            --burgundy: #641f35;
            --burgundy-dark: #4d1628;

            --text: #292326;
            --muted: #93878c;

            --border: #e7dfe2;

        }


        /* =====================================================
           RESET
        ===================================================== */

        * {
            box-sizing: border-box;
        }


        html,
        body {

            margin: 0;
            padding: 0;

            width: 100%;
            min-height: 100%;

        }


        body {

            background: #ffffff;

            color: var(--text);

            font-family: 'Inter', sans-serif;

        }


        /* =====================================================
           PAGE
        ===================================================== */

        .admin-login {

            min-height: 100vh;

            width: 100%;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 20px;

            background: #ffffff;

        }


        /* =====================================================
           CARD
        ===================================================== */

        .admin-login__box {

            width: 100%;

            max-width: 350px;

            padding: 25px 30px 21px;

            background: #ffffff;

            border: 1px solid var(--border);

            border-radius: 14px;

            text-align: center;

            box-shadow:
                0 12px 35px rgba(61, 26, 38, 0.06);

        }


        /* =====================================================
           LOGO
        ===================================================== */

        .admin-login__logo {

            width: 100%;

            display: flex;

            align-items: center;

            justify-content: center;

            margin-bottom: 8px;

        }


        .admin-login__logo img {

            width: 125px;

            height: 48px;

            object-fit: contain;

            display: block;

        }


        /* =====================================================
           TITLE
        ===================================================== */

        .admin-login__title {

            margin: 0 0 4px;

            font-family: 'Cormorant Garamond', serif;

            font-size: 1.45rem;

            line-height: 1.1;

            font-weight: 500;

            color: var(--burgundy);

        }


        /* =====================================================
           SUBTITLE
        ===================================================== */

        .admin-login__subtitle {

            margin: 0 0 18px;

            font-size: 0.60rem;

            line-height: 1.5;

            letter-spacing: 0.14em;

            text-transform: uppercase;

            color: var(--muted);

        }


        /* =====================================================
           ERROR
        ===================================================== */

        .admin-login__error {

            width: 100%;

            margin-bottom: 12px;

            padding: 8px 10px;

            border-radius: 7px;

            border: 1px solid #ecd7dd;

            background: #fbf3f5;

            color: var(--burgundy);

            font-size: 0.70rem;

            line-height: 1.4;

            text-align: left;

        }


        /* =====================================================
           FORM
        ===================================================== */

        .admin-login__form {

            width: 100%;

            text-align: left;

        }


        /* =====================================================
           FIELD
        ===================================================== */

        .admin-login__field {

            margin-bottom: 11px;

        }


        .admin-login__field label {

            display: block;

            margin-bottom: 5px;

            font-size: 0.62rem;

            font-weight: 500;

            letter-spacing: 0.07em;

            text-transform: uppercase;

            color: #62595d;

        }


        /* =====================================================
           INPUT
        ===================================================== */

        .admin-login__field input {

            width: 100%;

            height: 40px;

            padding: 0 11px;

            border: 1px solid var(--border);

            border-radius: 7px;

            outline: none;

            background: #ffffff;

            color: var(--text);

            font-family: 'Inter', sans-serif;

            font-size: 0.75rem;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;

        }


        .admin-login__field input::placeholder {

            color: #b4abad;

        }


        .admin-login__field input:hover {

            border-color: #d8ccd1;

        }


        .admin-login__field input:focus {

            border-color: var(--burgundy);

            box-shadow:
                0 0 0 2px rgba(100, 31, 53, 0.06);

        }


        /* =====================================================
           BUTTON
        ===================================================== */

        .admin-login__submit {

            width: 100%;

            height: 40px;

            margin-top: 3px;

            padding: 0 20px;

            border: none;

            border-radius: 7px;

            background: var(--burgundy);

            color: #ffffff;

            font-family: 'Inter', sans-serif;

            font-size: 0.68rem;

            font-weight: 500;

            letter-spacing: 0.07em;

            cursor: pointer;

            transition:
                background 0.2s ease,
                transform 0.2s ease,
                box-shadow 0.2s ease;

        }


        .admin-login__submit:hover {

            background: var(--burgundy-dark);

            transform: translateY(-1px);

            box-shadow:
                0 7px 18px rgba(100, 31, 53, 0.15);

        }


        .admin-login__submit:active {

            transform: translateY(0);

        }


        /* =====================================================
           RETURN HOME
        ===================================================== */

        .admin-login__back {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 5px;

            margin-top: 14px;

            color: var(--muted);

            text-decoration: none;

            font-size: 0.65rem;

            transition: color 0.2s ease;

        }


        .admin-login__back:hover {

            color: var(--burgundy);

        }


        .admin-login__back svg {

            transition:
                transform 0.2s ease;

        }


        .admin-login__back:hover svg {

            transform: translateX(-3px);

        }


        /* =====================================================
           BOTTOM TEXT
        ===================================================== */

        .admin-login__hint {

            margin: 13px 0 0;

            padding-top: 10px;

            border-top: 1px solid #f0ebed;

            font-size: 0.57rem;

            line-height: 1.5;

            color: #aaa1a6;

        }


        /* =====================================================
           MOBILE
        ===================================================== */

        @media (max-width: 480px) {

            .admin-login {

                padding: 15px;

            }


            .admin-login__box {

                max-width: 340px;

                padding: 22px 24px 19px;

            }


            .admin-login__logo img {

                width: 115px;

                height: 43px;

            }


            .admin-login__title {

                font-size: 1.35rem;

            }

        }

    </style>

</head>


<body>


    <!-- =====================================================
         LOGIN
    ===================================================== -->

    <div class="admin-login">


        <div class="admin-login__box">


            <!-- =================================================
                 LOGO
            ================================================== -->

            <div class="admin-login__logo">

                <img
                    src="<?= e(SITE_URL) ?>/assets/images/logo.png"
                    alt="Fleura"
                >

            </div>


            <!-- =================================================
                 TITRE
            ================================================== -->

            <h1 class="admin-login__title">

                Espace administration

            </h1>


            <p class="admin-login__subtitle">

                Gestion de votre boutique

            </p>


            <!-- =================================================
                 ERREUR
            ================================================== -->

            <?php if ($error): ?>

                <div class="admin-login__error">

                    <?= e($error) ?>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 FORMULAIRE
            ================================================== -->

            <form
                method="post"
                action=""
                class="admin-login__form"
            >


                <!-- EMAIL -->

                <div class="admin-login__field">

                    <label for="email">

                        Email

                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Votre adresse email"
                        autocomplete="email"
                        value="<?= e($_POST['email'] ?? '') ?>"
                        required
                    >

                </div>


                <!-- MOT DE PASSE -->

                <div class="admin-login__field">

                    <label for="password">

                        Mot de passe

                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Votre mot de passe"
                        autocomplete="current-password"
                        required
                    >

                </div>


                <!-- BOUTON -->

                <button
                    type="submit"
                    class="admin-login__submit"
                >

                    Se connecter

                </button>


            </form>


            <!-- =================================================
                 RETOUR À L'ACCUEIL
            ================================================== -->

            <a
                href="<?= e(SITE_URL) ?>/index.php"
                class="admin-login__back"
            >

                <svg
                    width="13"
                    height="13"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                    aria-hidden="true"
                >

                    <path d="M19 12H5"/>

                    <path d="M12 19l-7-7 7-7"/>

                </svg>

                Retour à l'accueil

            </a>


            <!-- =================================================
                 TEXTE DISCRET
            ================================================== -->

            <p class="admin-login__hint">

                Accès réservé à l'administration de Fleura.

            </p>


        </div>


    </div>


</body>

</html>