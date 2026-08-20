<?php
require_once __DIR__ . '/includes/functions.php';

// ============================================================
// LISTE DES 69 WILAYAS D'ALGÉRIE
// ============================================================
$wilayas = [
    '01' => 'Adrar', '02' => 'Chlef', '03' => 'Laghouat', '04' => 'Oum El Bouaghi',
    '05' => 'Batna', '06' => 'Béjaïa', '07' => 'Biskra', '08' => 'Béchar',
    '09' => 'Blida', '10' => 'Bouira', '11' => 'Tamanrasset', '12' => 'Tébessa',
    '13' => 'Tlemcen', '14' => 'Tiaret', '15' => 'Tizi Ouzou', '16' => 'Alger',
    '17' => 'Djelfa', '18' => 'Jijel', '19' => 'Sétif', '20' => 'Saïda',
    '21' => 'Skikda', '22' => 'Sidi Bel Abbès', '23' => 'Annaba', '24' => 'Guelma',
    '25' => 'Constantine', '26' => 'Médéa', '27' => 'Mostaganem', '28' => 'M\'Sila',
    '29' => 'Mascara', '30' => 'Ouargla', '31' => 'Oran', '32' => 'El Bayadh',
    '33' => 'Illizi', '34' => 'Bordj Bou Arréridj', '35' => 'Boumerdès', '36' => 'El Tarf',
    '37' => 'Tindouf', '38' => 'Tissemsilt', '39' => 'El Oued', '40' => 'Khenchela',
    '41' => 'Souk Ahras', '42' => 'Tipaza', '43' => 'Mila', '44' => 'Aïn Defla',
    '45' => 'Naâma', '46' => 'Aïn Témouchent', '47' => 'Ghardaïa', '48' => 'Relizane',
    '49' => 'Timimoun', '50' => 'Bordj Badji Mokhtar', '51' => 'Ouled Djellal',
    '52' => 'Béni Abbès', '53' => 'In Salah', '54' => 'In Guezzam', '55' => 'Touggourt',
    '56' => 'Djanet', '57' => 'El M\'Ghair', '58' => 'El Meniaa', '59' => 'El Guerrara',
    '60' => 'Berriane', '61' => 'Mécheria', '62' => 'Aïn Sefra', '63' => 'El Abiodh Sidi Cheikh',
    '64' => 'Béni Saf', '65' => 'Sidi Ali', '66' => 'Sidi Abdelli', '67' => 'El Kala'
];

$cart = get_cart();
if (empty($cart)) {
    redirect('cart.php');
}

$cart_total = get_cart_total();
$delivery_fee = DELIVERY_FEE;
$grand_total = $cart_total + $delivery_fee;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $delivery_type = $_POST['delivery_type'] ?? 'domicile';
    $wilaya = trim($_POST['wilaya'] ?? '');
    $commune = trim($_POST['commune'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $company_name = trim($_POST['company_name'] ?? '');
    $office_phone = trim($_POST['office_phone'] ?? '');

    if (empty($first_name)) $errors[] = 'Prénom requis.';
    if (empty($last_name)) $errors[] = 'Nom requis.';
    if (empty($phone)) $errors[] = 'Téléphone requis.';
    if (empty($wilaya)) $errors[] = 'Wilaya requise.';
    if (empty($commune)) $errors[] = 'Commune requise.';
    if (empty($address)) $errors[] = 'Adresse complète requise.';

    if (empty($errors)) {
        global $pdo;
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT id FROM customers WHERE phone = ?");
            $stmt->execute([$phone]);
            $customer = $stmt->fetch();

            if ($customer) {
                $customer_id = $customer['id'];
            } else {
                $stmt = $pdo->prepare("INSERT INTO customers (first_name, last_name, phone, email) VALUES (?, ?, ?, ?)");
                $stmt->execute([$first_name, $last_name, $phone, $email ?: null]);
                $customer_id = $pdo->lastInsertId();
            }

            foreach ($cart as $item) {
                $product = get_product((int)$item['product_id']);
                if (!$product || $product['stock'] < $item['quantity']) {
                    throw new Exception("Stock insuffisant pour: " . ($product['name'] ?? 'produit'));
                }
            }

            $order_number = generate_order_number();

            // ============================================================
            // INSERTION SANS address_complement (ni office_address)
            // ============================================================
            $stmt = $pdo->prepare("INSERT INTO orders
                (customer_id, order_number, delivery_type, wilaya, commune, address,
                 company_name, office_phone, payment_method, subtotal, delivery_fee, total, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'especes', ?, ?, ?, 'en_attente')");

            $stmt->execute([
                $customer_id,
                $order_number,
                $delivery_type,
                $wilaya,
                $commune,
                $address,
                $delivery_type === 'bureau' ? $company_name : null,
                $delivery_type === 'bureau' ? $office_phone : null,
                $cart_total,
                $delivery_fee,
                $grand_total
            ]);

            $order_id = $pdo->lastInsertId();

            $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, size, color, price) VALUES (?, ?, ?, ?, ?, ?)");
            $stock_stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

            foreach ($cart as $item) {
                $item_stmt->execute([
                    $order_id,
                    (int)$item['product_id'],
                    (int)$item['quantity'],
                    $item['size'] ?: null,
                    $item['color'] ?: null,
                    (float)$item['price']
                ]);
                $stock_stmt->execute([(int)$item['quantity'], (int)$item['product_id']]);
            }

            $pdo->commit();
            unset($_SESSION['cart']);
            redirect('order-success.php?order=' . urlencode($order_number));

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Erreur: " . $e->getMessage();
        }
    }
}

$page_title = 'Commande — FLEURA';
require_once __DIR__ . '/includes/header.php';

// Chemin de base pour les images
$image_base = SITE_URL . '/assets/images/products/';
?>

<style>
/* ============================================================
   STYLE SPÉCIFIQUE POUR LE SELECT MODERNE
   ============================================================ */
.custom-select {
    position: relative;
}
.custom-select select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    width: 100%;
    padding: 0.8rem 2.8rem 0.8rem 1rem;
    border: 1px solid var(--color-border, #E8DBCF);
    border-radius: 0;
    background: #fff;
    font-size: 0.95rem;
    color: var(--color-text, #211C17);
    cursor: pointer;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='10' viewBox='0 0 14 10'%3E%3Cpath d='M1 1l6 6 6-6' stroke='%234A4A4A' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1.2rem center;
    background-size: 14px 10px;
}
.custom-select select:hover {
    border-color: var(--color-text-muted, #9a9186);
}
.custom-select select:focus {
    outline: none;
    border-color: var(--color-fuchsia, #5C1A2E);
    box-shadow: 0 0 0 3px rgba(92,26,46,0.08);
}
.custom-select select option {
    padding: 0.5rem 1rem;
    background: #fff;
    color: var(--color-text);
}
.custom-select select option:hover {
    background: var(--color-surface, #FCFAF7);
}
</style>

<div class="checkout-page">
    <h1>Finaliser ma commande</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert--error">
            <?php foreach ($errors as $err): ?>
                <p style="margin:0;">• <?= e($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="checkout-layout">
        <!-- ============================================================ -->
        <!-- FORMULAIRE                                                    -->
        <!-- ============================================================ -->
        <form class="checkout-form" method="post" action="">
            <fieldset>
                <legend>Informations personnelles</legend>
                <div class="form-row">
                    <div class="form-field">
                        <label>Prénom *</label>
                        <input type="text" name="first_name" value="<?= e($_POST['first_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-field">
                        <label>Nom *</label>
                        <input type="text" name="last_name" value="<?= e($_POST['last_name'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-field">
                        <label>Téléphone *</label>
                        <input type="tel" name="phone" value="<?= e($_POST['phone'] ?? '') ?>" required>
                    </div>
                    <div class="form-field">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>">
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Adresse de livraison</legend>

                <div class="delivery-choice">
                    <label class="delivery-option <?= (!isset($_POST['delivery_type']) || $_POST['delivery_type'] === 'domicile') ? 'selected' : '' ?>">
                        <input type="radio" name="delivery_type" value="domicile" <?= (!isset($_POST['delivery_type']) || $_POST['delivery_type'] === 'domicile') ? 'checked' : '' ?> style="display:none;">
                        <h4>Domicile</h4>
                        <p>Livraison à votre domicile</p>
                    </label>
                    <label class="delivery-option <?= (isset($_POST['delivery_type']) && $_POST['delivery_type'] === 'bureau') ? 'selected' : '' ?>">
                        <input type="radio" name="delivery_type" value="bureau" <?= (isset($_POST['delivery_type']) && $_POST['delivery_type'] === 'bureau') ? 'checked' : '' ?> style="display:none;">
                        <h4>Bureau</h4>
                        <p>Livraison à votre lieu de travail</p>
                    </label>
                </div>

                <div class="form-row">
                    <div class="form-field custom-select">
                        <label>Wilaya *</label>
                        <select name="wilaya" required>
                            <option value="">— Sélectionnez —</option>
                            <?php foreach ($wilayas as $code => $name): ?>
                                <option value="<?= e($name) ?>" <?= (isset($_POST['wilaya']) && $_POST['wilaya'] === $name) ? 'selected' : '' ?>>
                                    <?= e($code) ?> — <?= e($name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Commune *</label>
                        <input type="text" name="commune" value="<?= e($_POST['commune'] ?? '') ?>" placeholder="Ex: El Harrach..." required>
                    </div>
                </div>

                <div class="form-field">
                    <label>Adresse complète *</label>
                    <input type="text" name="address" value="<?= e($_POST['address'] ?? '') ?>" placeholder="Numéro, rue, cité..." required>
                </div>

                <div id="bureau-fields" style="<?= (isset($_POST['delivery_type']) && $_POST['delivery_type'] === 'bureau') ? 'display:block;' : 'display:none;' ?>">
                    <div class="form-field">
                        <label>Nom de l'entreprise <span style="font-weight:normal;color:var(--color-text-muted);">(optionnel)</span></label>
                        <input type="text" name="company_name" value="<?= e($_POST['company_name'] ?? '') ?>" placeholder="Nom de votre entreprise...">
                    </div>
                    <div class="form-field">
                        <label>Téléphone du bureau <span style="font-weight:normal;color:var(--color-text-muted);">(optionnel)</span></label>
                        <input type="tel" name="office_phone" value="<?= e($_POST['office_phone'] ?? '') ?>" placeholder="Téléphone professionnel...">
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Paiement</legend>
                <div class="payment-method-box">
                    <div>
                        <h4>Paiement à la livraison — Espèces</h4>
                        <p>Payez en espèces à la réception de votre commande.</p>
                    </div>
                </div>
            </fieldset>

            <button type="submit" class="btn btn--primary btn--block btn--lg">Confirmer ma commande</button>
        </form>

        <!-- RÉSUMÉ DE LA COMMANDE -->
        <aside class="order-summary">
            <h3>Résumé de la commande</h3>
            <?php foreach ($cart as $item): ?>
                <div class="order-summary__item">
                    <img src="<?= $image_base . e($item['image']) ?>" alt="<?= e($item['name']) ?>">
                    <div class="order-summary__item-info">
                        <p class="order-summary__item-name"><?= e($item['name']) ?></p>
                        <p class="order-summary__item-meta">
                            <?php if (!empty($item['size'])): ?>T. <?= e($item['size']) ?><?php endif; ?>
                            <?php if (!empty($item['color'])): ?> · <?= e($item['color']) ?><?php endif; ?>
                            × <?= (int)$item['quantity'] ?>
                        </p>
                    </div>
                    <p class="order-summary__item-price"><?= format_price($item['price'] * $item['quantity']) ?></p>
                </div>
            <?php endforeach; ?>

            <div class="order-summary__totals">
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
            </div>
        </aside>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const options = document.querySelectorAll('.delivery-option');
    const bureauFields = document.getElementById('bureau-fields');

    options.forEach(opt => {
        opt.addEventListener('click', function() {
            options.forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            const radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
            if (radio && radio.value === 'bureau') {
                bureauFields.style.display = 'block';
            } else {
                bureauFields.style.display = 'none';
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>