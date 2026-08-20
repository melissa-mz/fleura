<?php
require_once __DIR__ . '/includes/functions.php';

// Handle add/update/remove
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $product_id = (int)$_POST['product_id'];
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        $size = $_POST['size'] ?? '';
        $color = $_POST['color'] ?? '';
        add_to_cart($product_id, $quantity, $size, $color);
        redirect('cart.php');
    } elseif ($action === 'update') {
        $key = $_POST['key'] ?? '';
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        update_cart_quantity($key, $quantity);
        redirect('cart.php');
    } elseif ($action === 'remove') {
        $key = $_POST['key'] ?? '';
        remove_from_cart($key);
        redirect('cart.php');
    }
}

// Handle buy now
if (isset($_GET['buy_now'])) {
    $product_id = (int)$_GET['buy_now'];
    $product = get_product($product_id);
    if ($product) {
        add_to_cart($product_id, 1);
        redirect('checkout.php');
    }
}

$cart = get_cart();
$cart_total = get_cart_total();
$delivery_fee = DELIVERY_FEE;
$grand_total = $cart_total + $delivery_fee;

$page_title = 'Panier — FLEURA';
require_once __DIR__ . '/includes/header.php';

// Chemin de base pour les images
$image_base = SITE_URL . '/assets/images/products/';
?>

<div class="cart-page">
    <h1>Votre Panier</h1>

    <?php if (empty($cart)): ?>
        <div class="cart-empty">
            <p>Votre panier est vide.</p>
            <a href="<?= e(SITE_URL) ?>/shop.php" class="btn btn--primary">Découvrir la collection</a>
        </div>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Taille</th>
                    <th>Couleur</th>
                    <th>Quantité</th>
                    <th>Prix</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart as $key => $item): ?>
                    <tr>
                        <td>
                            <div class="cart-item">
                                <?php
                                // Gestion du chemin de l'image
                                $img_src = (strpos($item['image'], 'http') === 0) 
                                    ? $item['image'] 
                                    : $image_base . $item['image'];
                                ?>
                                <!-- IMAGE CLIQUABLE VERS LA PAGE PRODUIT -->
                                <a href="<?= e(SITE_URL) ?>/product.php?id=<?= (int)$item['product_id'] ?>">
                                    <img class="cart-item__img" src="<?= e($img_src) ?>" alt="<?= e($item['name']) ?>">
                                </a>
                                <div>
                                    <p class="cart-item__name"><?= e($item['name']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td><?= e($item['size'] ?: '—') ?></td>
                        <td><?= e($item['color'] ?: '—') ?></td>
                        <td>
                            <form method="post" action="" style="display:inline-flex;">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="key" value="<?= e($key) ?>">
                                <div class="cart-qty">
                                    <button type="submit" data-action="decrease" name="quantity" value="<?= $item['quantity'] - 1 ?>">−</button>
                                    <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" data-cart-key="<?= e($key) ?>" readonly>
                                    <button type="submit" data-action="increase" name="quantity" value="<?= $item['quantity'] + 1 ?>">+</button>
                                </div>
                            </form>
                        </td>
                        <td><?= format_price($item['price'] * $item['quantity']) ?></td>
                        <td>
                            <form method="post" action="" style="display:inline;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="key" value="<?= e($key) ?>">
                                <button type="submit" class="cart-remove" aria-label="Supprimer">×</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <div class="cart-summary__row">
                <span>Sous-total</span>
                <span><?= format_price($cart_total) ?></span>
            </div>
            <div class="cart-summary__row">
                <span>Livraison</span>
                <span><?= format_price($delivery_fee) ?></span>
            </div>
            <div class="cart-summary__row cart-summary__row--total">
                <span>Total</span>
                <span><?= format_price($grand_total) ?></span>
            </div>
            <a href="<?= e(SITE_URL) ?>/checkout.php" class="btn btn--primary btn--block" style="margin-top:1.5rem;">Passer la commande</a>
            <a href="<?= e(SITE_URL) ?>/shop.php" class="btn btn--outline btn--block" style="margin-top:0.5rem;">Continuer mes achats</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>