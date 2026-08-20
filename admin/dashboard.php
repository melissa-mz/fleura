<?php
require_once __DIR__ . '/includes/header.php';
$stats = get_dashboard_stats();
$recent_orders = $pdo->query("SELECT o.*, c.first_name, c.last_name FROM orders o
    LEFT JOIN customers c ON o.customer_id = c.id
    ORDER BY o.created_at DESC LIMIT 5")->fetchAll();
?>

<div class="admin-stats">
    <div class="admin-stat">
        <div class="admin-stat__label">Total commandes</div>
        <div class="admin-stat__value"><?= (int)$stats['total_orders'] ?></div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat__label">En attente</div>
        <div class="admin-stat__value admin-stat__value--accent"><?= (int)$stats['pending_orders'] ?></div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat__label">Confirmées</div>
        <div class="admin-stat__value"><?= (int)$stats['confirmed_orders'] ?></div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat__label">Livrées</div>
        <div class="admin-stat__value"><?= (int)$stats['delivered_orders'] ?></div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat__label">Produits</div>
        <div class="admin-stat__value"><?= (int)$stats['total_products'] ?></div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat__label">Stock faible</div>
        <div class="admin-stat__value admin-stat__value--accent"><?= (int)$stats['low_stock'] ?></div>
    </div>
</div>

<h3 class="admin-section-title">Commandes récentes</h3>
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>N° Commande</th>
                <th>Cliente</th>
                <th>Date</th>
                <th>Montant</th>
                <th>Statut</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($recent_orders)): ?>
                <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--color-text-muted);">Aucune commande pour le moment.</td></tr>
            <?php else: ?>
                <?php foreach ($recent_orders as $order): ?>
                    <tr>
                        <td data-label="N° Commande"><?= e($order['order_number']) ?></td>
                        <td data-label="Cliente"><?= e($order['first_name'] . ' ' . $order['last_name']) ?></td>
                        <td data-label="Date"><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                        <td data-label="Montant"><?= format_price($order['total']) ?></td>
                        <td data-label="Statut"><span class="status-badge status-badge--<?= e($order['status']) ?>"><?= get_status_label($order['status']) ?></span></td>
                        <td data-label="Détails"><a href="<?= e(SITE_URL) ?>/admin/order-details.php?id=<?= (int)$order['id'] ?>" class="link-view">Voir →</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>