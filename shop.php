<?php
require_once __DIR__ . '/includes/functions.php';

$filters = [
    'category' => $_GET['category'] ?? '',
    'search'   => $_GET['search'] ?? '',
    'sort'     => $_GET['sort'] ?? 'newest',
    'type'     => $_GET['type'] ?? '',
    'size'     => $_GET['size'] ?? '',
    'color'    => $_GET['color'] ?? '',
];

if (isset($_GET['filter']) && $_GET['filter'] === 'new') {
    $filters['sort'] = 'newest';
}

$products = get_products($filters);

$per_page = 12;
$total = count($products);
$pages = max(1, (int) ceil($total / $per_page));
$current_page = max(1, (int) ($_GET['page'] ?? 1));
if ($current_page > $pages) $current_page = $pages;
$offset = ($current_page - 1) * $per_page;
$page_products = array_slice($products, $offset, $per_page);

$page_title = 'Collection — FLEURA';
require_once __DIR__ . '/includes/header.php';

$image_base = SITE_URL . '/assets/images/products/';
?>

<div class="shop-page">

    <!-- HEADER COLLECTION -->
    <div class="shop-heading">
        <div class="shop-heading__text">
            <h1>Notre Collection</h1>
            
        </div>
        <div class="shop-heading__count">
            <?= $total ?> article<?= $total > 1 ? 's' : '' ?>
        </div>
    </div>

    <!-- PRODUITS -->
    <div class="shop-results">

        <?php if (empty($page_products)): ?>

            <div class="shop-empty">
                <div class="shop-empty__icon">♡</div>
                <h2>Aucun article trouvé</h2>
                <p>Aucun produit ne correspond à votre recherche.</p>
                <a href="<?= e(SITE_URL) ?>/shop.php" class="shop-empty__btn">
                    Voir toute la collection
                </a>
            </div>

        <?php else: ?>

            <div class="shop-products">

                <?php foreach ($page_products as $product): ?>

                    <div class="product-card">

                        <div class="product-card__image-wrap">

                            <?php
                            $images = get_product_images((int) $product['id']);
                            $secondary = !empty($images) ? $images[0]['image'] : $product['image'];

                            $primary_src = (strpos($product['image'], 'http') === 0)
                                ? $product['image']
                                : $image_base . $product['image'];

                            $secondary_src = (strpos($secondary, 'http') === 0)
                                ? $secondary
                                : $image_base . $secondary;
                            ?>

                            <img class="product-card__image product-card__image--primary"
                                 src="<?= e($primary_src) ?>"
                                 alt="<?= e($product['name']) ?>">

                            <img class="product-card__image product-card__image--secondary"
                                 src="<?= e($secondary_src) ?>"
                                 alt="<?= e($product['name']) ?>">

                            <?php if (!empty($product['promo_price']) && $product['promo_price'] > 0): ?>
                                <span class="product-card__badge product-card__badge--promo">Promo</span>
                            <?php elseif ($product['is_new']): ?>
                                <span class="product-card__badge product-card__badge--accent">New</span>
                            <?php endif; ?>

                            <!-- ACTIONS AU SURVOL -->
                            <div class="product-card__actions">
                                <a href="<?= e(SITE_URL) ?>/product.php?id=<?= (int) $product['id'] ?>"
                                   class="product-card__action-btn">
                                    Voir l'article
                                </a>
                                <form method="post" action="<?= e(SITE_URL) ?>/cart.php"
                                      class="product-card__add-form">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit"
                                            class="product-card__action-btn product-card__action-btn--dark">
                                        Ajouter au panier
                                    </button>
                                </form>
                            </div>

                        </div>

                        <!-- INFORMATIONS -->
                        <a href="<?= e(SITE_URL) ?>/product.php?id=<?= (int) $product['id'] ?>"
                           class="product-card__info">

                            <p class="product-card__name"><?= e($product['name']) ?></p>

                            <?php if (!empty($product['colors'])): ?>
                                <p class="product-card__meta"><?= e($product['colors']) ?></p>
                            <?php endif; ?>

                            <p class="product-card__price">
                                <?php if (!empty($product['promo_price']) && $product['promo_price'] > 0): ?>
                                    <span class="product-card__old-price"><?= format_price($product['price']) ?></span>
                                    <span class="product-card__promo-price"><?= format_price($product['promo_price']) ?></span>
                                <?php else: ?>
                                    <?= format_price($product['price']) ?>
                                <?php endif; ?>
                            </p>

                        </a>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

        <!-- PAGINATION -->
        <?php if ($pages > 1): ?>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $current_page - 1])) ?>"
                       class="pagination__arrow" aria-label="Page précédente">←</a>
                <?php endif; ?>
                <div class="pagination__numbers">
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
                           class="<?= $i === $current_page ? 'current' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
                <?php if ($current_page < $pages): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $current_page + 1])) ?>"
                       class="pagination__arrow" aria-label="Page suivante">→</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>