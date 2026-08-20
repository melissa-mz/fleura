<?php
// =====================================================
// FLEURA — Fonctions utilitaires
// =====================================================

require_once __DIR__ . '/../config/database.php';

// Démarrer la session si pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -------------------------------------------------------
// Sécurité & formatage
// -------------------------------------------------------
function e($value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void {
    header("Location: " . $path);
    exit;
}

function format_price($price): string {
    return number_format((float)$price, 0, ',', ' ') . ' ' . CURRENCY;
}

function generate_order_number(): string {
    return 'FLR-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}

function get_color_hex(string $colorName): string {
    $map = [
        'noir'     => '#000000',
        'blanc'    => '#ffffff',
        'beige'    => '#d9c7a3',
        'gris'     => '#8c8c8c',
        'rouge'    => '#d32f2f',
        'bordeaux' => '#6d1f2f',
        'rose'     => '#f4a3c1',
        'bleu'     => '#1f5fa8',
        'marine'   => '#1a2b4a',
        'vert'     => '#2e7d32',
        'kaki'     => '#7a7a52',
        'jaune'    => '#f4d03f',
        'orange'   => '#e67e22',
        'marron'   => '#6d4c33',
        'camel'    => '#c19a6b',
        'violet'   => '#7d3c98',
        'doré'     => '#c9a227',
        'nude'     => '#e0b894',
    ];
    return $map[mb_strtolower(trim($colorName))] ?? '#cccccc';
}

// -------------------------------------------------------
// Authentification admin
// -------------------------------------------------------
function is_admin_logged_in(): bool {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function require_admin(): void {
    if (!is_admin_logged_in()) {
        redirect('login.php');
    }
}

// -------------------------------------------------------
// Catégories
// -------------------------------------------------------
function get_categories(): array {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
    return $stmt->fetchAll();
}

function get_category(int $id): ?array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch();
    return $result ?: null;
}

// -------------------------------------------------------
// Types de vêtements (pour le dropdown "Articles" de la navbar)
// -------------------------------------------------------
function get_clothing_types(): array {
    global $pdo;
    // category_id 1 = Robes, 2 = Vêtements -> tout ce qui est "habillement"
    $stmt = $pdo->query("SELECT DISTINCT type FROM products 
                          WHERE category_id IN (1,2) 
                          AND type IS NOT NULL AND type != '' 
                          ORDER BY type ASC");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// -------------------------------------------------------
// Produits
// -------------------------------------------------------
function get_products(array $filters = []): array {
    global $pdo;

    $sql = "SELECT p.*, c.name AS category_name FROM products p
            LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
    $params = [];

    if (!empty($filters['category'])) {
        $sql .= " AND p.category_id = ?";
        $params[] = (int)$filters['category'];
    }
    if (!empty($filters['type'])) {
        $sql .= " AND p.type = ?";
        $params[] = $filters['type'];
    }
    if (!empty($filters['search'])) {
        $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
        $search = '%' . $filters['search'] . '%';
        $params[] = $search;
        $params[] = $search;
    }
    if (!empty($filters['size'])) {
        $sql .= " AND p.sizes LIKE ?";
        $params[] = '%' . $filters['size'] . '%';
    }
    if (!empty($filters['color'])) {
        $sql .= " AND p.colors LIKE ?";
        $params[] = '%' . $filters['color'] . '%';
    }
    if (isset($filters['min_price']) && $filters['min_price'] !== '') {
        $sql .= " AND p.price >= ?";
        $params[] = (float)$filters['min_price'];
    }
    if (isset($filters['max_price']) && $filters['max_price'] !== '') {
        $sql .= " AND p.price <= ?";
        $params[] = (float)$filters['max_price'];
    }

    $sort = $filters['sort'] ?? 'newest';
    switch ($sort) {
        case 'price_asc':
            $sql .= " ORDER BY p.price ASC";
            break;
        case 'price_desc':
            $sql .= " ORDER BY p.price DESC";
            break;
        case 'newest':
        default:
            $sql .= " ORDER BY p.created_at DESC";
            break;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function get_product(int $id): ?array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p
                           LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch();
    return $result ?: null;
}

function get_product_images(int $productId): array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ?");
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}

function get_new_products(int $limit = 8): array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p
                           LEFT JOIN categories c ON p.category_id = c.id
                           WHERE p.is_new = TRUE ORDER BY p.created_at DESC LIMIT ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_products_by_category(int $categoryId, int $limit = 4): array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p
                           LEFT JOIN categories c ON p.category_id = c.id
                           WHERE p.category_id = ? ORDER BY p.created_at DESC LIMIT ?");
    $stmt->bindValue(1, $categoryId, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// -------------------------------------------------------
// Panier (session-based)
// -------------------------------------------------------
function get_cart(): array {
    return $_SESSION['cart'] ?? [];
}

function get_cart_count(): int {
    $count = 0;
    foreach (get_cart() as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

function get_cart_total(): float {
    $total = 0;
    foreach (get_cart() as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}
function add_to_cart(int $productId, int $quantity, string $size = '', string $color = ''): void {
    $product = get_product($productId);
    if (!$product) return;

    $key = $productId . '_' . $size . '_' . $color;
    if (isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$key] = [
            'product_id' => $productId,
            'name' => $product['name'],
            'price' => (float)$product['price'],
            'image' => $product['image'],
            'quantity' => $quantity,
            'size' => $size,
            'color' => $color,
        ];
    }
}

function update_cart_quantity(string $key, int $quantity): void {
    if (isset($_SESSION['cart'][$key])) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$key]);
        } else {
            $_SESSION['cart'][$key]['quantity'] = $quantity;
        }
    }
}

function remove_from_cart(string $key): void {
    unset($_SESSION['cart'][$key]);
}

// -------------------------------------------------------
// Témoignages (avis réels fournis)
// -------------------------------------------------------
function get_testimonials(): array {
    return [
        [
            'name' => 'Amina',
            'rating' => 5,
            'text' => 'Très belles collections et nouveautés fréquentes. Accueil chaleureux et respectueux.',
        ],
        [
            'name' => 'Yasmine',
            'rating' => 5,
            'text' => 'La qualité des vêtements est excellente. Je recommande vivement Boutique Fleura.',
        ],
        [
            'name' => 'Nadia',
            'rating' => 4,
            'text' => 'Belles pièces et bon service. La livraison à domicile est un vrai plus.',
        ],
    ];
}

// -------------------------------------------------------
// Images Instagram
// -------------------------------------------------------
function get_instagram_images(): array {
    return [
        'https://images.pexels.com/photos/28666282/pexels-photo-28666282.jpeg?auto=compress&cs=tinysrgb&h=650&w=940',
        'https://images.pexels.com/photos/3395708/pexels-photo-3395708.jpeg?auto=compress&cs=tinysrgb&h=650&w=940',
        'https://images.pexels.com/photos/20718606/pexels-photo-20718606.jpeg?auto=compress&cs=tinysrgb&h=650&w=940',
        'https://images.pexels.com/photos/28686638/pexels-photo-28686638.jpeg?auto=compress&cs=tinysrgb&h=650&w=940',
        'https://images.pexels.com/photos/11890856/pexels-photo-11890856.jpeg?auto=compress&cs=tinysrgb&h=650&w=940',
        'https://images.pexels.com/photos/887898/pexels-photo-887898.jpeg?auto=compress&cs=tinysrgb&h=650&w=940',
    ];
}

// -------------------------------------------------------
// Dashboard stats
// -------------------------------------------------------
function get_dashboard_stats(): array {
    global $pdo;

    $stats = [];

    $stmt = $pdo->query("SELECT COUNT(*) FROM orders");
    $stats['total_orders'] = (int)$stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'en_attente'");
    $stats['pending_orders'] = (int)$stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'confirmee'");
    $stats['confirmed_orders'] = (int)$stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'livree'");
    $stats['delivered_orders'] = (int)$stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != 'annulee'");
    $stats['revenue'] = (float)$stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $stats['total_products'] = (int)$stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE stock <= 5");
    $stats['low_stock'] = (int)$stmt->fetchColumn();

    return $stats;
}

function get_status_label(string $status): string {
    $labels = [
        'en_attente' => 'En attente',
        'confirmee' => 'Confirmée',
        'en_preparation' => 'En préparation',
        'expediee' => 'Expédiée',
        'livree' => 'Livrée',
        'annulee' => 'Annulée',
    ];
    return $labels[$status] ?? $status;
}