<?php
require_once __DIR__ . '/includes/functions.php';

$order_number = $_GET['order'] ?? '';

if (empty($order_number)) {
    redirect('index.php');
}

$page_title = 'Commande confirmée — FLEURA';
require_once __DIR__ . '/includes/header.php';
?>

<div class="order-success">
    <div class="order-success__icon">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
    </div>
    <h1>Commande Confirmée</h1>
    <p class="order-success__number">N° <?= e($order_number) ?></p>
    <p>Merci pour votre commande ! Nous vous contacterons bientôt pour confirmer la livraison. Votre paiement en espèces se fera à la réception.</p>
    <a href="<?= e(SITE_URL) ?>/shop.php" class="btn btn--primary">Continuer mes achats</a>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
