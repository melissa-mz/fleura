<?php
// --- MODIFICATION AJOUTÉE (pour que le CSS sache que c'est la page d'accueil) ---
$body_class = 'homepage'; 
// --------------------------------------------------------------------------------

require_once __DIR__ . '/includes/functions.php';
$categories = get_categories();
$new_products = get_new_products(8);
$testimonials = get_testimonials();
$instagram_images = get_instagram_images();

$page_title = 'FLEURA — L\'élégance, simplement.';
require_once __DIR__ . '/includes/header.php';

// Chemin de base pour les images
$image_base = SITE_URL . '/assets/images/products/';
?>

<!-- ============ HERO SPLIT ÉDITORIAL ============ -->
<section class="hero hero--split">
    <div class="hero__text">
        <h1 class="hero__brand hero__brand--thin">
            <span>F</span>
            <span>L</span>
            <span>E</span>
            <span>U</span>
            <span>R</span>
            <span>A</span>
        </h1>
        <a href="<?= e(SITE_URL) ?>/shop.php" class="btn btn--hero-dark">DÉCOUVRIR</a>
    </div>

    <div class="hero__frame">
        <video class="hero__frame-video" autoplay muted loop playsinline preload="auto">
            <source src="<?= e(SITE_URL) ?>/assets/videos/hero2.mp4" type="video/mp4">
        </video>
        <div class="hero__frame-border"></div>
    </div>
</section>

<!-- ============ NOTRE COLLECTION (Curtain Effect) ============ -->
<section class="collection-section">
    <div class="container">
        <p class="eyebrow reveal" style="text-align:center;">La Maison Fleura</p>
        <h2 class="section-title reveal">Notre Collection</h2>
        <p class="section-subtitle reveal">Des pièces pensées pour révéler votre style.</p>
    </div>
    <div class="collection-grid">
        <?php foreach ($categories as $index => $cat): ?>
            <div class="curtain-card reveal <?= $index < 3 ? 'reveal-delay-' . ($index + 1) : '' ?>">
                <div class="curtain-card__center">
                    <h3><?= e($cat['name']) ?></h3>
                    <p>Découvrir la collection</p>
                    <a href="<?= e(SITE_URL) ?>/shop.php?category=<?= (int)$cat['id'] ?>" class="btn btn--primary">Explorer →</a>
                </div>
                <div class="curtain-card__image-wrap">
                    <div class="curtain-card__half curtain-card__half--left" style="background-image: url('<?= e($cat['image']) ?>')"></div>
                    <div class="curtain-card__half curtain-card__half--right" style="background-image: url('<?= e($cat['image']) ?>')"></div>
                </div>
                <div class="curtain-card__overlay"></div>
                <div class="curtain-card__label">
                    <h3><?= e($cat['name']) ?></h3>
                    <p><?= e($cat['description']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ============ LES NOUVEAUTÉS ============ -->
<section class="new-section">
    <div class="container">
        <p class="eyebrow reveal" style="text-align:center;">Dernières arrivées</p>
        <h2 class="section-title reveal">Les Nouveautés</h2>
        <p class="section-subtitle reveal">Découvrez les dernières pièces de notre collection.</p>
    </div>
    <div class="product-grid">
        <?php foreach ($new_products as $product): ?>
            <div class="product-card reveal">
                <div class="product-card__image-wrap">
                    <?php
                    $images = get_product_images((int)$product['id']);
                    $secondary = !empty($images) ? $images[0]['image'] : $product['image'];

                    // Gestion des chemins d'images
                    $primary_src = (strpos($product['image'], 'http') === 0) ? $product['image'] : $image_base . $product['image'];
                    $secondary_src = (strpos($secondary, 'http') === 0) ? $secondary : $image_base . $secondary;
                    ?>
                    <img class="product-card__image product-card__image--primary" src="<?= e($primary_src) ?>" alt="<?= e($product['name']) ?>">
                    <img class="product-card__image product-card__image--secondary" src="<?= e($secondary_src) ?>" alt="<?= e($product['name']) ?>">
                    
                    <!-- Badge : promo prioritaire -->
                    <?php if (!empty($product['promo_price']) && $product['promo_price'] > 0): ?>
                        <span class="product-card__badge" style="background:#ef4444;">Promo</span>
                    <?php elseif ($product['is_new']): ?>
                        <span class="product-card__badge product-card__badge--accent">NEW</span>
                    <?php endif; ?>

                    <div class="product-card__actions">
                        <a href="<?= e(SITE_URL) ?>/product.php?id=<?= (int)$product['id'] ?>" class="product-card__action-btn">Voir</a>
                        <form method="post" action="<?= e(SITE_URL) ?>/cart.php" style="flex:1;">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="product-card__action-btn" style="width:100%;border:none;">Ajouter</button>
                        </form>
                    </div>
                </div>
                <div class="product-card__info">
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
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div style="text-align:center; margin-top:3rem;" class="reveal">
        <a href="<?= e(SITE_URL) ?>/shop.php?filter=new" class="btn btn--outline">Voir toutes les nouveautés</a>
    </div>
</section>

<!-- ============ AVANTAGES ============ -->
<section class="advantages">
    <div class="advantages__grid">
        <div class="advantage reveal">
            <div class="advantage__icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M3 7h13l1 3h4v6h-2a2 2 0 1 1-4 0H9a2 2 0 1 1-4 0H3V7Z"/><path d="M16 7V4h-3"/></svg>
            </div>
            <h4>Livraison à Domicile</h4>
            <p>Recevez vos commandes directement chez vous.</p>
        </div>
        <div class="advantage reveal reveal-delay-1">
            <div class="advantage__icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M3 21h18M5 21V7l8-4 8 4v14"/><path d="M9 21v-4h6v4"/></svg>
            </div>
            <h4>Livraison au Bureau</h4>
            <p>Faites livrer votre commande à votre lieu de travail.</p>
        </div>
        <div class="advantage reveal reveal-delay-2">
            <div class="advantage__icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg>
            </div>
            <h4>Paiement à la Livraison</h4>
            <p>Paiement en espèces à la réception de votre commande.</p>
        </div>
        <div class="advantage reveal reveal-delay-3">
            <div class="advantage__icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2Z"/></svg>
            </div>
            <h4>Service Client</h4>
            <p>Une équipe disponible pour vous accompagner.</p>
        </div>
    </div>
</section>

<!-- ============ TÉMOIGNAGES ============ -->
<section class="testimonials">
    <div class="container">
        <p class="eyebrow reveal" style="text-align:center;">Avis clients</p>
        <h2 class="section-title reveal">Elles parlent de Fleura</h2>
        <div class="testimonials__rating reveal">
            <div class="testimonials__stars">★★★★★</div>
            <div class="testimonials__score">4,8 / 5</div>
            <div class="testimonials__count">8 avis</div>
        </div>
    </div>
    <div class="testimonials__grid">
        <?php foreach ($testimonials as $t): ?>
            <div class="testimonial-card reveal">
                <div class="testimonial-card__stars"><?= str_repeat('★', $t['rating']) . str_repeat('☆', 5 - $t['rating']) ?></div>
                <p class="testimonial-card__text">"<?= e($t['text']) ?>"</p>
                <p class="testimonial-card__name"><?= e($t['name']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- =====================================================
     FLEURA — SECTION RESEAUX SOCIAUX (2 cartes)
     ===================================================== -->
<section class="social-section">
    <div class="container">
        <p class="eyebrow reveal">Rejoignez-nous</p>
        <h2 class="section-title reveal">Suivez l'univers Fleura</h2>
        <p class="section-subtitle reveal">Nos dernières collections et coulisses, en ligne.</p>
    </div>

    <div class="social-cards">
        <!-- Instagram -->
        <div class="social-card social-card--instagram reveal">
            <div class="social-card__inner">
                <div class="social-card__head">
                    <div class="social-card__icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="3" y="3" width="18" height="18" rx="5"/>
                            <circle cx="12" cy="12" r="4"/>
                            <circle cx="17.2" cy="6.8" r="1"/>
                        </svg>
                    </div>
                    <div>
                        <p class="social-card__handle">@Boutiquefleura</p>
                        <p class="social-card__platform">Instagram</p>
                    </div>
                </div>

                <div class="social-card__thumbs">
                    <?php foreach (array_slice($instagram_images, 0, 3) as $img): ?>
                        <img src="<?= e($img) ?>" alt="Fleura Instagram">
                    <?php endforeach; ?>
                </div>

                <p class="social-card__text">Découvrez nos nouveautés, nos collections et l'univers Fleura au quotidien.</p>

                <a href="https://www.instagram.com/Boutiquefleura" target="_blank" rel="noopener" class="social-card__btn">
                    Ouvrir Instagram
                </a>
            </div>
        </div>

        <!-- Facebook -->
        <div class="social-card social-card--facebook reveal reveal-delay-1">
            <div class="social-card__inner">
                <div class="social-card__head">
                    <div class="social-card__icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M13.5 21v-7.5h2.5l.5-3h-3V8.5c0-.9.3-1.5 1.6-1.5H16V4.2C15.7 4.1 14.8 4 13.8 4c-2.1 0-3.5 1.3-3.5 3.7V10.5H8v3h2.3V21h3.2Z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="social-card__handle">Boutique Fleura</p>
                        <p class="social-card__platform">Facebook</p>
                    </div>
                </div>

                <div class="social-card__thumbs">
                    <?php foreach (array_slice($instagram_images, 3, 3) as $img): ?>
                        <img src="<?= e($img) ?>" alt="Fleura Facebook">
                    <?php endforeach; ?>
                </div>

                <p class="social-card__text">Suivez notre page pour ne rien manquer de nos actualités et de nos offres.</p>

                <a href="https://www.facebook.com/Boutiquefleura/" target="_blank" rel="noopener" class="social-card__btn">
                    Ouvrir Facebook
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>