<?php
// ============================================================
// INCLURE LES FONCTIONS (chemin corrigé)
// ============================================================
require_once __DIR__ . '/../includes/functions.php';

// ============================================================
// TRAITEMENT AJAX (recherche instantanée)
// ============================================================
$is_ajax = isset($_GET['ajax']) && $_GET['ajax'] === '1';

if ($is_ajax) {
    // Nettoyer tout buffer pour éviter les erreurs d'en-têtes
    if (ob_get_level()) ob_end_clean();

    $search = $_GET['search'] ?? '';
    $status_filter = $_GET['status'] ?? '';

    // Construction de la requête
    $sql = "SELECT o.*, c.first_name, c.last_name, c.phone FROM orders o
            LEFT JOIN customers c ON o.customer_id = c.id
            WHERE 1=1";
    $params = [];

    if (!empty($status_filter)) {
        $sql .= " AND o.status = ?";
        $params[] = $status_filter;
    }

    if (!empty($search)) {
        $sql .= " AND (o.order_number LIKE ? 
                    OR c.first_name LIKE ? 
                    OR c.last_name LIKE ? 
                    OR c.phone LIKE ?)";
        $s = '%' . $search . '%';
        $params[] = $s;
        $params[] = $s;
        $params[] = $s;
        $params[] = $s;
    }

    $sql .= " ORDER BY o.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();

    // Fonction de rendu du tableau pour AJAX
    function renderAjaxOrders($orders) {
        if (empty($orders)): ?>
            <tr><td colspan="9" style="text-align:center;padding:2.5rem;color:#9a9186;font-style:italic;">Aucune commande trouvée.</td></tr>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= e($order['order_number']) ?></td>
                    <td><?= e($order['first_name'] . ' ' . $order['last_name']) ?></td>
                    <td><?= e($order['phone']) ?></td>
                    <td><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                    <td><?= format_price($order['total']) ?></td>
                    <td><?= $order['delivery_type'] === 'domicile' ? 'Domicile' : 'Bureau' ?></td>
                    <td>Espèces</td>
                    <td><span class="status-badge status-badge--<?= e($order['status']) ?>"><?= get_status_label($order['status']) ?></span></td>
                    <td>
                        <a href="<?= SITE_URL ?>/admin/order-details.php?id=<?= (int)$order['id'] ?>" class="link-detail">
                            Voir
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif;
    }

    // Renvoyer uniquement le HTML du tableau
    header('Content-Type: text/html; charset=utf-8');
    renderAjaxOrders($orders);
    exit;
}

// ============================================================
// PAGE NORMALE (premier chargement)
// ============================================================
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT o.*, c.first_name, c.last_name, c.phone FROM orders o
        LEFT JOIN customers c ON o.customer_id = c.id
        WHERE 1=1";
$params = [];

if (!empty($status_filter)) {
    $sql .= " AND o.status = ?";
    $params[] = $status_filter;
}

if (!empty($search)) {
    $sql .= " AND (o.order_number LIKE ? 
                OR c.first_name LIKE ? 
                OR c.last_name LIKE ? 
                OR c.phone LIKE ?)";
    $s = '%' . $search . '%';
    $params[] = $s;
    $params[] = $s;
    $params[] = $s;
    $params[] = $s;
}

$sql .= " ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();
$total_orders = count($orders);

// Inclure le header pour la page normale
require_once __DIR__ . '/includes/header.php';

// Fonction de rendu pour la page normale
function renderOrdersTable($orders) {
    if (empty($orders)): ?>
        <tr><td colspan="9" style="text-align:center;padding:2.5rem;color:#9a9186;font-style:italic;">Aucune commande trouvée.</td></tr>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= e($order['order_number']) ?></td>
                <td><?= e($order['first_name'] . ' ' . $order['last_name']) ?></td>
                <td><?= e($order['phone']) ?></td>
                <td><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                <td><?= format_price($order['total']) ?></td>
                <td><?= $order['delivery_type'] === 'domicile' ? 'Domicile' : 'Bureau' ?></td>
                <td>Espèces</td>
                <td><span class="status-badge status-badge--<?= e($order['status']) ?>"><?= get_status_label($order['status']) ?></span></td>
                <td>
                    <a href="<?= SITE_URL ?>/admin/order-details.php?id=<?= (int)$order['id'] ?>" class="link-detail">
                        Voir
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif;
}
?>

<style>
/* --- Cacher l'en-tête admin --- */
.admin-header {
    display: none !important;
}
.admin-page-content {
    padding-top: 0 !important;
}

/* --- Conteneur principal --- */
.orders-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1.5rem 1.2rem 3rem;
}

/* --- En-tête --- */
.orders-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 0.8rem;
}
.orders-header h1 {
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: 1.6rem;
    font-weight: 500;
    color: #1C1F1F;
    margin: 0;
}
.orders-header .count {
    font-size: 0.85rem;
    color: #9a9186;
    background: #f8fafc;
    padding: 0.3rem 1rem;
    border-radius: 30px;
    white-space: nowrap;
}

/* --- Filtres en carte (sans bouton Filtrer) --- */
.orders-filters-card {
    background: #fff;
    padding: 1.2rem 1.5rem;
    border-radius: 12px;
    border: 1px solid rgba(92, 26, 46, 0.06);
    box-shadow: 0 2px 12px rgba(0,0,0,0.03);
    margin-bottom: 2rem;
}
.orders-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 0.8rem;
    align-items: center;
}
.orders-filters input,
.orders-filters select {
    padding: 0.5rem 0.9rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.85rem;
    background: #f8fafc;
    transition: all 0.2s;
    color: #1C1F1F;
}
.orders-filters input:focus,
.orders-filters select:focus {
    outline: none;
    border-color: #5C1A2E;
    box-shadow: 0 0 0 3px rgba(92,26,46,0.06);
    background: #fff;
}
.orders-filters input {
    flex: 1;
    min-width: 200px;
}
.orders-filters .btn-reset {
    background: transparent;
    color: #9a9186;
    border: 1px solid #e2e8f0;
    padding: 0.45rem 1.5rem;
    border-radius: 30px;
    font-size: 0.7rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
    white-space: nowrap;
}
.orders-filters .btn-reset:hover {
    border-color: #5C1A2E;
    color: #5C1A2E;
}

/* --- Tableau (reste un VRAI tableau, scroll horizontal sur mobile) --- */
.orders-table-wrap {
    background: #fff;
    border-radius: 12px;
    border: 1px solid rgba(92, 26, 46, 0.06);
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    box-shadow: 0 2px 12px rgba(0,0,0,0.03);
}
.orders-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.8rem;
}
.orders-table th,
.orders-table td {
    white-space: nowrap;
}
.orders-table th {
    background: #f8fafc;
    padding: 0.7rem 1rem;
    text-align: left;
    font-size: 0.6rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #9a9186;
    border-bottom: 1px solid rgba(92, 26, 46, 0.06);
}
.orders-table td {
    padding: 0.7rem 1rem;
    border-bottom: 1px solid rgba(92, 26, 46, 0.04);
    vertical-align: middle;
    color: #1C1F1F;
}
.orders-table tr:last-child td {
    border-bottom: none;
}
.orders-table tr:hover td {
    background: #fcf9f7;
}

/* --- Badges --- */
.orders-table .status-badge {
    display: inline-block;
    padding: 0.2rem 0.9rem;
    border-radius: 30px;
    font-size: 0.6rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    white-space: nowrap;
}
.status-badge--en_attente {
    background: #f1ede8;
    color: #7a7268;
}
.status-badge--confirmee {
    background: #e8ddd5;
    color: #5C1A2E;
}
.status-badge--en_preparation {
    background: #d9c7a3;
    color: #5C4A30;
}
.status-badge--expediee {
    background: #5C1A2E;
    color: #fff;
}
.status-badge--livree {
    background: #d4d4c8;
    color: #3d4a3a;
}
.status-badge--annulee {
    background: #f0e0dd;
    color: #8b3a3a;
}

.orders-table .link-detail {
    font-size: 0.75rem;
    color: #5C1A2E;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}
.orders-table .link-detail:hover {
    color: #7a2c42;
}
.orders-table .link-detail svg {
    width: 14px;
    height: 14px;
    transition: transform 0.2s;
    flex-shrink: 0;
}
.orders-table .link-detail:hover svg {
    transform: translateX(3px);
}

/* ============================================================
   RESPONSIVE MOBILE — reste un tableau, juste compact + scroll
   ============================================================ */
@media (max-width: 768px) {
    .orders-container {
        padding: 0.8rem !important;
    }
    .orders-header h1 {
        font-size: 1.3rem !important;
    }
    .orders-filters-card {
        padding: 0.8rem 1rem !important;
    }
    .orders-filters {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    .orders-filters input,
    .orders-filters select {
        min-width: 0 !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    .orders-filters .btn-reset {
        width: 100% !important;
        text-align: center !important;
    }

    /* Le tableau garde ses colonnes, on réduit juste pour que ça scroll proprement */
    .orders-table {
        font-size: 0.72rem !important;
    }
    .orders-table th,
    .orders-table td {
        padding: 0.5rem 0.7rem !important;
    }
    .orders-table th {
        font-size: 0.56rem !important;
    }
    .orders-table .status-badge {
        font-size: 0.55rem !important;
        padding: 3px 9px !important;
    }
    .orders-table .link-detail {
        font-size: 0.68rem !important;
    }

    /* Colonne N° commande fixée à gauche pour garder le repère pendant le scroll */
    .orders-table th:first-child,
    .orders-table td:first-child {
        position: sticky !important;
        left: 0 !important;
        background: #fff !important;
        z-index: 2 !important;
        box-shadow: 2px 0 4px rgba(0,0,0,0.04) !important;
    }
    .orders-table thead th:first-child {
        background: #f8fafc !important;
        z-index: 3 !important;
    }
}

@media (max-width: 480px) {
    .orders-container {
        padding-left: 8px !important;
        padding-right: 8px !important;
    }
    .orders-table {
        font-size: 0.68rem !important;
    }
    .orders-table th,
    .orders-table td {
        padding: 0.4rem 0.55rem !important;
    }
}
</style>

<div class="orders-container">
    <!-- En-tête -->
    <div class="orders-header">
        <h1>Commandes</h1>
        <span class="count" id="orderCount"><?= $total_orders ?> commande(s)</span>
    </div>

    <!-- Filtres (sans bouton Filtrer) -->
    <div class="orders-filters-card">
        <form id="filtersForm" class="orders-filters">
            <input type="text" name="search" id="searchInput" placeholder="Rechercher (n°, nom, téléphone)" value="<?= e($search) ?>" autocomplete="off">
            <select name="status" id="statusSelect">
                <option value="">Tous les statuts</option>
                <?php foreach (['en_attente','confirmee','en_preparation','expediee','livree','annulee'] as $s): ?>
                    <option value="<?= e($s) ?>" <?= $status_filter === $s ? 'selected' : '' ?>><?= get_status_label($s) ?></option>
                <?php endforeach; ?>
            </select>
            <a href="<?= e(SITE_URL) ?>/admin/orders.php" class="btn-reset">Réinitialiser</a>
        </form>
    </div>

    <!-- Tableau -->
    <div class="orders-table-wrap">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>N° Commande</th>
                    <th>Cliente</th>
                    <th>Téléphone</th>
                    <th>Date</th>
                    <th>Montant</th>
                    <th>Livraison</th>
                    <th>Paiement</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="ordersTableBody">
                <?php renderOrdersTable($orders); ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusSelect = document.getElementById('statusSelect');
    const tableBody = document.getElementById('ordersTableBody');
    const orderCount = document.getElementById('orderCount');
    let timeoutId = null;

    function fetchOrders() {
        const search = searchInput.value;
        const status = statusSelect.value;
        const url = '<?= SITE_URL ?>/admin/orders.php?ajax=1&search=' + encodeURIComponent(search) + '&status=' + encodeURIComponent(status);

        fetch(url)
            .then(response => response.text())
            .then(html => {
                tableBody.innerHTML = html;
                // Mettre à jour le compteur
                const rows = tableBody.querySelectorAll('tr');
                const hasEmpty = tableBody.querySelector('tr td[colspan]') !== null;
                const count = hasEmpty ? 0 : rows.length;
                orderCount.textContent = count + ' commande(s)';
            })
            .catch(error => console.error('Erreur de chargement:', error));
    }

    function triggerSearch() {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(fetchOrders, 150);
    }

    searchInput.addEventListener('input', triggerSearch);
    statusSelect.addEventListener('change', fetchOrders);
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>