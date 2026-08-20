<?php
require_once __DIR__ . '/includes/header.php';

$order_id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'] ?? '';
    $valid = ['en_attente','confirmee','en_preparation','expediee','livree','annulee'];
    if (in_array($new_status, $valid)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
    }
    redirect('order-details.php?id=' . $order_id);
}

$stmt = $pdo->prepare("SELECT o.*, c.first_name, c.last_name, c.phone, c.email FROM orders o
    LEFT JOIN customers c ON o.customer_id = c.id WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    redirect('orders.php');
}

$stmt = $pdo->prepare("SELECT oi.*, p.name, p.image FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

$image_base = SITE_URL . '/assets/images/products/';
?>

<style>
/* --- Cacher les éléments indésirables de l'en-tête --- */
.admin-header {
    display: none !important;
}
.admin-page-content {
    padding-top: 0 !important;
}

/* --- Styles épurés pour le reste --- */
.order-detail {
    max-width: 1080px;
    margin: 0 auto;
    padding: 0 1.2rem 2rem;
}
.order-detail__back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    color: var(--color-text-muted, #9a9186);
    text-decoration: none;
    margin-bottom: 1.8rem;
    transition: color 0.2s;
}
.order-detail__back:hover {
    color: #5C1A2E;
}
.order-detail__grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}
.order-detail__card {
    background: #fff;
    padding: 1.2rem 1.5rem;
    border-radius: 10px;
    border: 1px solid rgba(92, 26, 46, 0.06);
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
}
.order-detail__card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 0.8rem;
    padding-bottom: 0.4rem;
    border-bottom: 1px solid rgba(92, 26, 46, 0.08);
}
.order-detail__card-header svg {
    flex-shrink: 0;
    color: #5C1A2E;
}
.order-detail__card-header h3 {
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: 1rem;
    font-weight: 500;
    color: #5C1A2E;
    margin: 0;
}
.order-detail__info-line {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
    font-size: 0.82rem;
    line-height: 1.7;
    padding: 0.05rem 0;
}
.order-detail__info-line .label {
    color: var(--color-text-muted, #9a9186);
    font-weight: 400;
    min-width: 85px;
}
.order-detail__info-line .value {
    font-weight: 500;
    color: var(--color-text, #211C17);
}
.order-detail__info-line .value.total {
    font-size: 1rem;
    font-weight: 700;
    color: #5C1A2E;
}
.order-detail__status-select {
    width: 100%;
    padding: 0.45rem 0.7rem;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.8rem;
    background: #f8fafc;
    margin-bottom: 0.7rem;
}
.order-detail__btn {
    background: #5C1A2E;
    color: #fff;
    border: none;
    padding: 0.45rem 1rem;
    border-radius: 30px;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    cursor: pointer;
    transition: background 0.2s;
    width: 100%;
}
.order-detail__btn:hover {
    background: #7a2c42;
}
.order-detail__table-wrap {
    background: #fff;
    border-radius: 10px;
    border: 1px solid rgba(92, 26, 46, 0.06);
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
}
.order-detail__table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.75rem;
}
.order-detail__table th {
    background: #f8fafc;
    padding: 0.4rem 0.8rem;
    text-align: left;
    font-size: 0.6rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--color-text-muted, #9a9186);
    border-bottom: 1px solid rgba(92, 26, 46, 0.06);
}
.order-detail__table td {
    padding: 0.4rem 0.8rem;
    border-bottom: 1px solid rgba(92, 26, 46, 0.04);
    vertical-align: middle;
}
.order-detail__table tr:last-child td {
    border-bottom: none;
}
.order-detail__thumb {
    width: 28px;
    height: 28px;
    object-fit: cover;
    border-radius: 4px;
    background: #f1f5f9;
}
.order-detail__section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 1.8rem 0 0.8rem;
}
.order-detail__section-title svg {
    color: #5C1A2E;
    flex-shrink: 0;
}
.order-detail__section-title h3 {
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: 1rem;
    font-weight: 500;
    color: #5C1A2E;
    margin: 0;
}
@media (max-width: 768px) {
    .order-detail__grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    .order-detail__card {
        padding: 1rem;
    }
    .order-detail__table {
        font-size: 0.65rem;
    }
    .order-detail__table th,
    .order-detail__table td {
        padding: 0.3rem 0.5rem;
    }
    .order-detail__thumb {
        width: 22px;
        height: 22px;
    }
    .order-detail__info-line {
        font-size: 0.75rem;
    }
    .order-detail__info-line .label {
        min-width: 70px;
    }
}
</style>

<div class="order-detail">
    <!-- Retour -->
    <a href="<?= e(SITE_URL) ?>/admin/orders.php" class="order-detail__back">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
        Retour aux commandes
    </a>

    <!-- Deux colonnes -->
    <div class="order-detail__grid">

        <!-- Carte client -->
        <div class="order-detail__card">
            <div class="order-detail__card-header">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2a6 6 0 0 0-6 6c0 4 6 10 6 10s6-6 6-10a6 6 0 0 0-6-6z"/><circle cx="12" cy="8" r="2.5"/></svg>
                <h3>Client</h3>
            </div>
            <div class="order-detail__info-line">
                <span class="label">Nom</span>
                <span class="value"><?= e($order['first_name'] . ' ' . $order['last_name']) ?></span>
            </div>
            <div class="order-detail__info-line">
                <span class="label">Téléphone</span>
                <span class="value"><?= e($order['phone']) ?></span>
            </div>
            <div class="order-detail__info-line">
                <span class="label">Email</span>
                <span class="value"><?= e($order['email'] ?: '—') ?></span>
            </div>
            <div class="order-detail__info-line">
                <span class="label">Wilaya</span>
                <span class="value"><?= e($order['wilaya'] ?: '—') ?></span>
            </div>
            <div class="order-detail__info-line">
                <span class="label">Commune</span>
                <span class="value"><?= e($order['commune'] ?: '—') ?></span>
            </div>
            <div class="order-detail__info-line">
                <span class="label">Livraison</span>
                <span class="value"><?= $order['delivery_type'] === 'domicile' ? 'Domicile' : 'Bureau' ?></span>
            </div>
            <?php if ($order['delivery_type'] === 'domicile'): ?>
                <div class="order-detail__info-line">
                    <span class="label">Adresse</span>
                    <span class="value"><?= e($order['address'] ?: '—') ?></span>
                </div>
            <?php else: ?>
                <div class="order-detail__info-line">
                    <span class="label">Entreprise</span>
                    <span class="value"><?= e($order['company_name'] ?: '—') ?></span>
                </div>
                <div class="order-detail__info-line">
                    <span class="label">Adresse bureau</span>
                    <span class="value"><?= e($order['office_address'] ?: '—') ?></span>
                </div>
                <div class="order-detail__info-line">
                    <span class="label">Tél bureau</span>
                    <span class="value"><?= e($order['office_phone'] ?: '—') ?></span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Carte commande -->
        <div class="order-detail__card">
            <div class="order-detail__card-header">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="6" width="20" height="14" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/></svg>
                <h3>Commande</h3>
            </div>
            <form method="post">
                <select name="status" class="order-detail__status-select">
                    <?php foreach (['en_attente','confirmee','en_preparation','expediee','livree','annulee'] as $s): ?>
                        <option value="<?= e($s) ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= get_status_label($s) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="order-detail__btn">Mettre à jour</button>
            </form>
            <div style="margin-top:0.8rem; border-top:1px solid rgba(92,26,46,0.08); padding-top:0.8rem;">
                <div class="order-detail__info-line">
                    <span class="label">N° commande</span>
                    <span class="value"><?= e($order['order_number']) ?></span>
                </div>
                <div class="order-detail__info-line">
                    <span class="label">Date</span>
                    <span class="value"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                </div>
                <div class="order-detail__info-line">
                    <span class="label">Sous-total</span>
                    <span class="value"><?= format_price($order['subtotal']) ?></span>
                </div>
                <div class="order-detail__info-line">
                    <span class="label">Livraison</span>
                    <span class="value"><?= format_price($order['delivery_fee']) ?></span>
                </div>
                <div class="order-detail__info-line">
                    <span class="label total">Total</span>
                    <span class="value total"><?= format_price($order['total']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des articles -->
    <div class="order-detail__section-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16v16H4z"/><path d="M8 8h8v8H8z"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>
        <h3>Articles commandés</h3>
    </div>
    <div class="order-detail__table-wrap">
        <table class="order-detail__table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Image</th>
                    <th>Taille</th>
                    <th>Couleur</th>
                    <th>Qté</th>
                    <th>Prix unit.</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['name']) ?></td>
                        <td><img src="<?= $image_base . e($item['image']) ?>" alt="" class="order-detail__thumb"></td>
                        <td><?= e($item['size'] ?: '—') ?></td>
                        <td><?= e($item['color'] ?: '—') ?></td>
                        <td><?= (int)$item['quantity'] ?></td>
                        <td><?= format_price($item['price']) ?></td>
                        <td><?= format_price($item['price'] * $item['quantity']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>