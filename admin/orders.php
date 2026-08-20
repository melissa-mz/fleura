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
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <span class="link-detail__text">Voir</span>
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
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <span class="link-detail__text">Voir</span>
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

/* --- Tableau (desktop) --- */
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
   RESPONSIVE MOBILE — TOUTES LES COLONNES VISIBLES, SANS SCROLL
   ============================================================ */
@media (max-width: 768px) {
    .orders-container {
        padding: 0.6rem !important;
    }
    .orders-header {
        margin-bottom: 1rem !important;
    }
    .orders-header h1 {
        font-size: 1.15rem !important;
    }
    .orders-header .count {
        font-size: 0.65rem !important;
        padding: 0.25rem 0.7rem !important;
    }
    .orders-filters-card {
        padding: 0.7rem 0.8rem !important;
        margin-bottom: 1rem !important;
    }
    .orders-filters {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 0.5rem !important;
    }
    .orders-filters input,
    .orders-filters select {
        min-width: 0 !important;
        width: 100% !important;
        box-sizing: border-box !important;
        font-size: 0.78rem !important;
    }
    .orders-filters .btn-reset {
        width: 100% !important;
        text-align: center !important;
    }

    /* Le wrapper ne scrolle plus horizontalement : tout doit tenir */
    .orders-table-wrap {
        overflow-x: hidden !important;
        border-radius: 8px !important;
    }

    /* Largeurs fixes en % qui totalisent 100% -> jamais de dépassement */
    .orders-table {
        table-layout: fixed !important;
        width: 100% !important;
        font-size: 0.72rem !important;
    }
    .orders-table th,
    .orders-table td {
        padding: 0.5rem 0.4rem !important;
        white-space: normal !important;
        overflow-wrap: break-word !important;
        word-break: break-word !important;
        line-height: 1.3 !important;
        vertical-align: middle !important;
    }
    .orders-table th {
        font-size: 0.58rem !important;
        letter-spacing: 0 !important;
    }

    /* N° commande, Montant, Paiement et Statut masqués sur mobile : déjà visibles dans le détail de la commande */
    .orders-table th:nth-child(1), .orders-table td:nth-child(1) { display: none !important; }  /* N° */
    .orders-table th:nth-child(5), .orders-table td:nth-child(5) { display: none !important; }  /* Montant */
    .orders-table th:nth-child(7), .orders-table td:nth-child(7) { display: none !important; }  /* Paiement */
    .orders-table th:nth-child(8), .orders-table td:nth-child(8) { display: none !important; }  /* Statut */

    /* Répartition des largeurs sur les 5 colonnes restantes (total = 100%) */
    .orders-table th:nth-child(2), .orders-table td:nth-child(2) { width: 28%; }  /* Cliente */
    .orders-table th:nth-child(3), .orders-table td:nth-child(3) { width: 24%; }  /* Téléphone */
    .orders-table th:nth-child(4), .orders-table td:nth-child(4) { width: 18%; }  /* Date */
    .orders-table th:nth-child(6), .orders-table td:nth-child(6) { width: 18%; }  /* Livraison */
    .orders-table th:nth-child(9), .orders-table td:nth-child(9) { width: 12%; }  /* Action */

    .orders-table .status-badge {
        font-size: 0.44rem !important;
        padding: 2px 5px !important;
        white-space: normal !important;
        line-height: 1.2 !important;
    }

    /* Action : icône seule, texte "Voir" caché pour gagner de la place */
    .orders-table .link-detail {
        justify-content: center !important;
        gap: 0 !important;
    }
    .orders-table .link-detail svg {
        width: 15px !important;
        height: 15px !important;
    }
    .orders-table .link-detail__text {
        display: none !important;
    }
}

@media (max-width: 380px) {
    .orders-table {
        font-size: 0.66rem !important;
    }
    .orders-table th {
        font-size: 0.52rem !important;
    }
    .orders-table th,
    .orders-table td {
        padding: 0.45rem 0.3rem !important;
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
                    <th>N°</th>
                    <th>Cliente</th>
                    <th>Tél.</th>
                    <th>Date</th>
                    <th>Montant</th>
                    <th>Livr.</th>
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