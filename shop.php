<?php
require_once __DIR__ . '/includes/functions.php';

$categories = get_categories();
$clothing_types = get_clothing_types();

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

<div class="shop-header">
    <p class="eyebrow">Boutique</p>
    <h1><?= !empty($filters['type']) ? e($filters['type']) : 'Collection' ?></h1>
    <p>Découvrez l'ensemble de nos pièces</p>
</div>

<div class="shop-layout">

    <aside class="shop-filters" id="shopFilters">
        <form method="get" action="">
            <?php if (isset($_GET['filter'])): ?>
                <input type="hidden" name="filter" value="<?= e($_GET['filter']) ?>">
            <?php endif; ?>

            <div class="filter-group">
                <h4>Recherche</h4>
                <input type="text" name="search" value="<?= e($filters['search']) ?>" placeholder="Rechercher...">
            </div>

            <div class="filter-group">
                <h4>Catégories</h4>
                <?php foreach ($categories as $cat): ?>
                    <label>
                        <input type="radio" name="category" value="<?= (int) $cat['id'] ?>" <?= (string) $filters['category'] === (string) $cat['id'] ? 'checked' : '' ?> onchange="this.form.submit()">
                        <?= e($cat['name']) ?>
                    </label>
                <?php endforeach; ?>
                <label>
                    <input type="radio" name="category" value="" <?= empty($filters['category']) ? 'checked' : '' ?> onchange="this.form.submit()">
                    Toutes
                </label>
            </div>

            <?php if (!empty($clothing_types)): ?>
                <div class="filter-group">
                    <h4>Type d'article</h4>
                    <?php foreach ($clothing_types as $type): ?>
                        <label>
                            <input type="radio" name="type" value="<?= e($type) ?>" <?= $filters['type'] === $type ? 'checked' : '' ?> onchange="this.form.submit()">
                            <?= e($type) ?>
                        </label>
                    <?php endforeach; ?>
                    <label>
                        <input type="radio" name="type" value="" <?= empty($filters['type']) ? 'checked' : '' ?> onchange="this.form.submit()">
                        Tous
                    </label>
                </div>
            <?php endif; ?>

            <div class="filter-group">
                <h4>Taille</h4>
                <?php $sizes = ['S','M','L','XL','Unique','36','37','38','39','40']; ?>
                <?php foreach ($sizes as $size): ?>
                    <label>
                        <input type="radio" name="size" value="<?= e($size) ?>" <?= $filters['size'] === $size ? 'checked' : '' ?> onchange="this.form.submit()">
                        <?= e($size) ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="filter-group">
                <h4>Couleur</h4>
                <?php $colors = ['Rouge','Noir','Blanc','Marron','Beige','Or','Argent','Jaune','Nude','Multicolore']; ?>
                <?php foreach ($colors as $color): ?>
                    <label>
                        <input type="radio" name="color" value="<?= e($color) ?>" <?= $filters['color'] === $color ? 'checked' : '' ?> onchange="this.form.submit()">
                        <?= e($color) ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn btn--primary btn--block">Filtrer</button>
            <a href="<?= e(SITE_URL) ?>/shop.php" class="shop-reset-btn">Réinitialiser</a>
        </form>
    </aside>

    <div class="shop-results">

        <div class="shop-products">
            <?php if (empty($page_products)): ?>
                <p class="shop-empty">Aucun produit ne correspond à votre recherche.</p>
            <?php else: ?>
                <?php foreach ($page_products as $product): ?>
                    <div class="product-card">

                        <div class="product-card__image-wrap">
                            <?php
                            $images = get_product_images((int) $product['id']);
                            $secondary = !empty($images) ? $images[0]['image'] : $product['image'];

                            $primary_src = (strpos($product['image'], 'http') === 0) ? $product['image'] : $image_base . $product['image'];
                            $secondary_src = (strpos($secondary, 'http') === 0) ? $secondary : $image_base . $secondary;
                            ?>

                            <img class="product-card__image product-card__image--primary" src="<?= e($primary_src) ?>" alt="<?= e($product['name']) ?>">
                            <img class="product-card__image product-card__image--secondary" src="<?= e($secondary_src) ?>" alt="<?= e($product['name']) ?>">

                            <?php if (!empty($product['promo_price']) && $product['promo_price'] > 0): ?>
                                <span class="product-card__badge" style="background:#ef4444;">Promo</span>
                            <?php elseif ($product['is_new']): ?>
                                <span class="product-card__badge product-card__badge--accent">New</span>
                            <?php endif; ?>

                            <div class="product-card__actions">
                                <a href="<?= e(SITE_URL) ?>/product.php?id=<?= (int) $product['id'] ?>" class="product-card__action-btn">Voir</a>
                                <form method="post" action="<?= e(SITE_URL) ?>/cart.php" class="product-card__add-form">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="product-card__action-btn">Ajouter</button>
                                </form>
                            </div>
                        </div>

                        <a href="<?= e(SITE_URL) ?>/product.php?id=<?= (int) $product['id'] ?>" class="product-card__info">
                            <p class="product-card__name"><?= e($product['name']) ?></p>
                            <p class="product-card__meta"><?= e($product['colors']) ?></p>
                            <p class="product-card__price">
                                <?php if (!empty($product['promo_price']) && $product['promo_price'] > 0): ?>
                                    <span style="text-decoration:line-through;color:#b0b0b0;margin-right:6px;font-size:0.85rem;"><?= format_price($product['price']) ?></span>
                                    <span style="color:#211C17;font-weight:400;"><?= format_price($product['promo_price']) ?></span>
                                <?php else: ?>
                                    <?= format_price($product['price']) ?>
                                <?php endif; ?>
                            </p>
                        </a>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if ($pages > 1): ?>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $current_page - 1])) ?>" class="pagination__arrow" aria-label="Page précédente">←</a>
                <?php endif; ?>
                <div class="pagination__numbers">
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="<?= $i === $current_page ? 'current' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
                <?php if ($current_page < $pages): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $current_page + 1])) ?>" class="pagination__arrow" aria-label="Page suivante">→</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>