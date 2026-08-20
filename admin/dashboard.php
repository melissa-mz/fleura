<?php
require_once __DIR__ . '/includes/header.php';
$stats = get_dashboard_stats();
?>

<div class="admin-stats">
    <div class="admin-stat">
        <div class="admin-stat__label">Total commandes</div>
        <div class="admin-stat__value"><?= (int)$stats['total_orders'] ?></div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat__label">Produits</div>
        <div class="admin-stat__value"><?= (int)$stats['total_products'] ?></div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>