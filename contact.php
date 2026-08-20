<?php
require_once __DIR__ . '/includes/functions.php';

$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if (!empty($name) && !empty($phone) && !empty($message)) {
        $sent = true;
    }
}

$page_title = 'Contact — FLEURA';
require_once __DIR__ . '/includes/header.php';
?>

<div class="about-page">
    <p class="eyebrow" style="text-align:center;">Nous contacter</p>
    <h1>Contact</h1>

    <div class="about-content">
        <p>Une question ? Une demande ? Notre équipe est disponible pour vous accompagner.</p>
    </div>

    <div class="about-info" style="margin-bottom:3rem;">
        <div class="about-info__row">
            <strong>Adresse</strong>
            Koléa, Algérie
        </div>
        <div class="about-info__row">
            <strong>Téléphone</strong>
            0670 06 37 31
        </div>
        <div class="about-info__row">
            <strong>Facebook</strong>
            <a href="https://www.facebook.com/Boutiquefleura/" target="_blank" rel="noopener" style="color:var(--color-accent);">Boutiquefleura</a>
        </div>
    </div>

    <?php if ($sent): ?>
        <div class="alert alert--success">
            Merci ! Votre message a été envoyé. Nous vous contacterons bientôt.
        </div>
    <?php endif; ?>

    <form class="contact-form" method="post" action="">
        <div class="form-field">
            <label>Nom complet *</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-field">
            <label>Téléphone *</label>
            <input type="tel" name="phone" required>
        </div>
        <div class="form-field">
            <label>Email</label>
            <input type="email" name="email">
        </div>
        <div class="form-field">
            <label>Message *</label>
            <textarea name="message" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn--primary btn--block">Envoyer le message</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
