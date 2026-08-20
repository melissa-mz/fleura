<?php
require_once __DIR__ . '/includes/functions.php';

$product_id = (int)($_GET['id'] ?? 0);
$product = get_product($product_id);

if (!$product) {
    redirect('shop.php');
}

// ============================================================
// RÉCUPÉRATION DES IMAGES (depuis product_images)
// ============================================================
$stmt = $pdo->prepare("SELECT image FROM product_images WHERE product_id = ? ORDER BY id");
$stmt->execute([$product_id]);
$images = $stmt->fetchAll();
$all_images = array_column($images, 'image');

// Fallback : si aucune image dans product_images, on utilise l'image du produit
if (empty($all_images) || empty(array_filter($all_images))) {
    $all_images = array_filter([$product['image']]);
}

// Si encore vide, on met une image par défaut
if (empty($all_images)) {
    $all_images = ['default.jpg'];
}

$sizes = array_filter(array_map('trim', explode(',', $product['sizes'] ?? '')));
$colors = array_filter(array_map('trim', explode(',', $product['colors'] ?? '')));

$page_title = e($product['name']) . ' — FLEURA';
require_once __DIR__ . '/includes/header.php';

$image_base = SITE_URL . '/assets/images/products/';
$first_image = $all_images[0];
?>

<style>
/* ============================================================
   GALERIE PRODUIT : image grande + miniatures en dessous
   ============================================================ */
.product-gallery {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.product-gallery__main {
    position: relative;
    aspect-ratio: 1 / 1; /* carré, tu peux changer en 3/4 */
    overflow: hidden;
    background: var(--color-surface, #f8fafc);
    border-radius: 8px;
}
.product-gallery__main img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: pointer;
}
.product-gallery__thumbs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.product-gallery__thumb {
    width: 80px;
    height: 80px;
    overflow: hidden;
    border-radius: 6px;
    border: 2px solid transparent;
    cursor: pointer;
    transition: border-color 0.2s, transform 0.2s;
    background: var(--color-surface, #f8fafc);
    flex-shrink: 0;
}
.product-gallery__thumb:hover {
    transform: scale(1.05);
}
.product-gallery__thumb.active {
    border-color: var(--color-fuchsia, #5C1A2E);
}
.product-gallery__thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
@media (max-width: 480px) {
    .product-gallery__thumb {
        width: 60px;
        height: 60px;
    }
}
</style>

<div class="product-page">
    <div class="product-detail">
        <!-- ============================================================ -->
        <!-- GALERIE (image principale + miniatures)                       -->
        <!-- ============================================================ -->
        <div class="product-gallery">
            <!-- Image principale -->
            <div class="product-gallery__main" style="position:relative;">
                <?php if (!empty($product['promo_price']) && $product['promo_price'] > 0): ?>
                    <div style="position:absolute; top:15px; left:15px; background:#ef4444; color:#fff; padding:4px 14px; border-radius:50px; font-weight:600; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em; z-index:10;">Promo</div>
                <?php elseif ($product['is_new']): ?>
                    <div style="position:absolute; top:15px; left:15px; background:#5C1A2E; color:#fff; padding:4px 14px; border-radius:50px; font-weight:600; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em; z-index:10;">Nouveau</div>
                <?php endif; ?>
                <img id="galleryMain" src="<?= (filter_var($first_image, FILTER_VALIDATE_URL) ? '' : $image_base) . e($first_image) ?>" alt="<?= e($product['name']) ?>">
            </div>

            <!-- Miniatures (si plus d'une image) -->
            <?php if (count($all_images) > 1): ?>
                <div class="product-gallery__thumbs">
                    <?php foreach ($all_images as $idx => $img): ?>
                        <?php if (!empty($img)): ?>
                            <div class="product-gallery__thumb <?= $idx === 0 ? 'active' : '' ?>" data-full="<?= (filter_var($img, FILTER_VALIDATE_URL) ? '' : $image_base) . e($img) ?>">
                                <img src="<?= (filter_var($img, FILTER_VALIDATE_URL) ? '' : $image_base) . e($img) ?>" alt="<?= e($product['name']) ?>">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- ============================================================ -->
        <!-- INFOS PRODUIT (inchangées)                                    -->
        <!-- ============================================================ -->
        <div class="product-info">
            <p class="product-info__category"><?= e($product['category_name'] ?? '') ?></p>
            <h1 class="product-info__name"><?= e($product['name']) ?></h1>

            <!-- PRIX -->
            <?php if (!empty($product['promo_price']) && $product['promo_price'] > 0): ?>
                <p class="product-info__price" style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <span style="text-decoration:line-through; color:#b0b0b0; font-size:1rem;"><?= format_price($product['price']) ?></span>
                    <span style="color:#211C17; font-size:1rem;"><?= format_price($product['promo_price']) ?></span>
                </p>
            <?php else: ?>
                <p class="product-info__price"><?= format_price($product['price']) ?></p>
            <?php endif; ?>

            <p class="product-info__availability <?= $product['stock'] > 0 ? 'in-stock' : 'out-stock' ?>">
                <?= $product['stock'] > 0 ? '✓ En stock' : '✗ Rupture de stock' ?>
            </p>
            <p class="product-info__desc"><?= e($product['description']) ?></p>

            <form method="post" action="<?= e(SITE_URL) ?>/cart.php">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">

                <?php if (!empty($sizes)): ?>
                    <div class="product-options">
                        <span class="product-options__label">Taille</span>
                        <div class="product-options__choices">
                            <?php foreach ($sizes as $size): ?>
                                <label class="product-options__choice">
                                    <input type="radio" name="size" value="<?= e($size) ?>" style="display:none;" <?= $size === $sizes[0] ? 'checked' : '' ?>>
                                    <?= e($size) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- COULEURS -->
                <?php if (!empty($colors)): ?>
                    <div class="product-options">
                        <span class="product-options__label">Couleur</span>
                        <div class="product-options__choices product-options__choices--colors">
                            <?php foreach ($colors as $color):
                                $hex = get_color_hex($color);
                                $r = hexdec(substr($hex, 1, 2));
                                $g = hexdec(substr($hex, 3, 2));
                                $b = hexdec(substr($hex, 5, 2));
                                $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
                                $dark = $brightness < 128 ? 1 : 0;
                            ?>
                                <label class="color-swatch <?= $color === $colors[0] ? 'selected' : '' ?>">
                                    <input type="radio" name="color" value="<?= e($color) ?>" <?= $color === $colors[0] ? 'checked' : '' ?>>
                                    <span class="swatch-dot" style="background-color: <?= e($hex) ?>;" data-dark="<?= $dark ?>"></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="product-quantity">
                    <span class="product-options__label" style="margin:0;">Quantité</span>
                    <div class="qty-selector">
                        <button type="button" data-action="decrease">−</button>
                        <input type="number" name="quantity" value="1" min="1" max="<?= (int)$product['stock'] ?>">
                        <button type="button" data-action="increase">+</button>
                    </div>
                </div>

                <div class="product-actions">
                    <button type="submit" class="btn btn--primary" style="flex:1;">Ajouter au panier</button>
                </div>
            </form>

            <div class="product-assurance">
                <div class="product-assurance__item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 7h13l1 3h4v6h-2a2 2 0 1 1-4 0H9a2 2 0 1 1-4 0H3V7Z"/></svg>
                    Livraison à domicile ou au bureau
                </div>
                <div class="product-assurance__item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg>
                    Paiement à la livraison — Espèces
                </div>
            </div>
        </div>
    </div>

    <!-- TABS -->
    <div class="product-tabs">
        <div class="product-tabs__nav">
            <button class="active" data-tab="details">Détails</button>
            <button data-tab="composition">Composition</button>
            <button data-tab="sizes">Tailles</button>
            <button data-tab="shipping">Livraison</button>
        </div>
        <div class="product-tabs__panel active" id="tab-details">
            <p><?= e($product['description']) ?></p>
        </div>
        <div class="product-tabs__panel" id="tab-composition">
            <p>Composition: matériaux de qualité supérieure, sélectionnés avec soin pour garantir confort et élégance.</p>
        </div>
        <div class="product-tabs__panel" id="tab-sizes">
            <p>Tailles disponibles: <?= e($product['sizes']) ?></p>
            <p>Couleurs disponibles: <?= e($product['colors']) ?></p>
        </div>
        <div class="product-tabs__panel" id="tab-shipping">
            <p>Livraison à domicile ou au bureau à travers les wilayas d'Algérie. Paiement en espèces à la réception de votre commande.</p>
        </div>
    </div>
</div>

<script>
// ============================================================
// GALERIE : clic sur miniature → changement image principale
// ============================================================
document.querySelectorAll('.product-gallery__thumb').forEach(thumb => {
    thumb.addEventListener('click', function() {
        document.querySelectorAll('.product-gallery__thumb').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        const full = this.getAttribute('data-full');
        document.getElementById('galleryMain').src = full;
    });
});

// ============================================================
// QUANTITÉ : boutons + et -
// ============================================================
document.querySelectorAll('.qty-selector button').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = this.closest('.qty-selector').querySelector('input');
        let val = parseInt(input.value) || 1;
        if (this.dataset.action === 'decrease') {
            if (val > 1) val--;
        } else {
            val++;
        }
        const max = parseInt(input.getAttribute('max')) || 999;
        if (val > max) val = max;
        input.value = val;
    });
});

// ============================================================
// TAILLE : gestion de la classe "selected"
// ============================================================
document.querySelectorAll('.product-options__choice').forEach(choice => {
    choice.addEventListener('click', function() {
        const parent = this.closest('.product-options__choices');
        parent.querySelectorAll('.product-options__choice').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        this.querySelector('input[type="radio"]').checked = true;
    });
});

// ============================================================
// COULEUR : clic sur une boule
// ============================================================
document.querySelectorAll('.color-swatch').forEach(swatch => {
    swatch.addEventListener('click', function(e) {
        const parent = this.closest('.product-options__choices--colors');
        parent.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('selected'));
        this.classList.add('selected');
        this.querySelector('input[type="radio"]').checked = true;
    });
});

// ============================================================
// TABS : afficher/masquer les panneaux
// ============================================================
document.querySelectorAll('.product-tabs__nav button').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.product-tabs__nav button').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const tab = this.dataset.tab;
        document.querySelectorAll('.product-tabs__panel').forEach(p => p.classList.remove('active'));
        document.getElementById('tab-' + tab).classList.add('active');
    });
});

// ============================================================
// PLEIN ÉCRAN : clic sur l'image principale
// ============================================================
document.getElementById('galleryMain').addEventListener('click', function() {
    if (this.requestFullscreen) {
        this.requestFullscreen();
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>