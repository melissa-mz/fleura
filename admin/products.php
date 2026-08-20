<?php
ob_start();

$page_title = 'Gestion des produits';
require_once __DIR__ . '/includes/header.php';

// ============================================================
// Message d'erreur upload lisible
// ============================================================
function upload_error_message(int $code): string {
    switch ($code) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return 'Fichier trop volumineux';
        case UPLOAD_ERR_PARTIAL:
            return 'Upload interrompu, réessayez';
        case UPLOAD_ERR_NO_FILE:
            return 'Aucun fichier sélectionné';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Dossier temporaire manquant (config serveur)';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Écriture disque impossible';
        default:
            return 'Erreur inconnue (code ' . $code . ')';
    }
}

// ============================================================
// TRAITEMENT : suppression, ajout, modification
// ============================================================

// --- Suppression du produit ---
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    $stmt = $pdo->prepare("SELECT image FROM product_images WHERE product_id = ?");
    $stmt->execute([$id]);
    $images = $stmt->fetchAll();
    foreach ($images as $img) {
        if (!filter_var($img['image'], FILTER_VALIDATE_URL)) {
            $path = __DIR__ . '/../assets/images/products/' . $img['image'];
            if (file_exists($path)) unlink($path);
        }
    }
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM product_images WHERE product_id = ?")->execute([$id]);
    $_SESSION['flash'] = 'Produit supprimé.';
    header('Location: ' . SITE_URL . '/admin/products.php');
    exit;
}

// --- Suppression d'une image individuelle ---
if (isset($_GET['supprimer_image']) && isset($_GET['product_id'])) {
    $img_id = (int)$_GET['supprimer_image'];
    $product_id = (int)$_GET['product_id'];
    $stmt = $pdo->prepare("SELECT image FROM product_images WHERE id = ? AND product_id = ?");
    $stmt->execute([$img_id, $product_id]);
    $img = $stmt->fetch();
    if ($img && !filter_var($img['image'], FILTER_VALIDATE_URL)) {
        $path = __DIR__ . '/../assets/images/products/' . $img['image'];
        if (file_exists($path)) unlink($path);
    }
    $pdo->prepare("DELETE FROM product_images WHERE id = ? AND product_id = ?")->execute([$img_id, $product_id]);

    $stmt = $pdo->prepare("SELECT image FROM product_images WHERE product_id = ? ORDER BY id LIMIT 1");
    $stmt->execute([$product_id]);
    $new_main = $stmt->fetchColumn();
    $pdo->prepare("UPDATE products SET image = ? WHERE id = ?")->execute([$new_main, $product_id]);

    $_SESSION['flash'] = 'Image supprimée.';
    header('Location: ' . SITE_URL . '/admin/products.php?edit=' . $product_id);
    exit;
}

// --- Ajout / modification ---
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $promo_price = !empty($_POST['promo_price']) ? (float)$_POST['promo_price'] : null;
    $stock = (int)($_POST['stock'] ?? 0);
    $sizes = trim($_POST['sizes'] ?? '');

    // --- Couleurs ---
    $selected_colors = $_POST['colors'] ?? [];
    $other_color = trim($_POST['color_other'] ?? '');
    if (!empty($other_color)) {
        $selected_colors[] = $other_color;
    }
    $colors_array = array_unique(array_filter(array_map('trim', $selected_colors)));
    $colors = implode(',', $colors_array);

    $type = trim($_POST['type'] ?? '');
    $is_new = isset($_POST['is_new']) ? 1 : 0;

    if ($category_id != 2) {
        $type = '';
    }

    $base = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name)));
    $timestamp = time();
    $file_basename = $base . '-' . $timestamp;

    // --- Créer le dossier d'upload si inexistant ---
    $upload_dir = __DIR__ . '/../assets/images/products/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    if (!is_writable($upload_dir)) {
        $errors[] = "Le dossier d'upload n'est pas accessible en écriture : " . $upload_dir;
    }

    // ============================================================
    // UPLOAD — un seul champ "images[]" pour toutes les photos.
    // La première devient l'image principale automatiquement.
    // ============================================================
    $uploaded_images = [];
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $total = count($_FILES['images']['name']);
        for ($i = 0; $i < $total; $i++) {
            if ($_FILES['images']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
                $errors[] = "Erreur upload image " . ($i + 1) . " : " . upload_error_message($_FILES['images']['error'][$i]);
                continue;
            }
            if (!is_uploaded_file($_FILES['images']['tmp_name'][$i]) || $_FILES['images']['size'][$i] == 0) {
                $errors[] = "Fichier invalide ou vide pour l'image " . ($i + 1);
                continue;
            }

            $ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $errors[] = "Extension non autorisée : " . $_FILES['images']['name'][$i];
                continue;
            }

            $filename = $file_basename . '-' . uniqid() . '.' . $ext;
            $dest = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $dest)) {
                $uploaded_images[] = $filename;
            } else {
                $errors[] = "Erreur lors du déplacement de l'image " . ($i + 1) . " (permissions ou dossier manquant)";
            }
        }
    }

    // --- Validation ---
    if (empty($name)) $errors[] = 'Le nom est requis.';
    if ($category_id <= 0) $errors[] = 'La catégorie est requise.';
    if ($price <= 0) $errors[] = 'Le prix doit être supérieur à 0.';
    if (!$id && empty($uploaded_images)) $errors[] = 'Au moins une image est requise pour un nouveau produit.';

    if (empty($errors)) {
        $main_image = null;
        if ($id) {
            $stmt = $pdo->prepare("SELECT image FROM product_images WHERE product_id = ? ORDER BY id LIMIT 1");
            $stmt->execute([$id]);
            $main_image = $stmt->fetchColumn();
            if (empty($main_image) && !empty($uploaded_images)) {
                $main_image = $uploaded_images[0];
            }
        } else {
            if (!empty($uploaded_images)) {
                $main_image = $uploaded_images[0];
            }
        }

        if ($id) {
            $sql = "UPDATE products SET 
                        category_id = ?, name = ?, description = ?, price = ?, promo_price = ?,
                        stock = ?, sizes = ?, colors = ?, type = ?, image = ?, is_new = ?
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $category_id, $name, $description, $price, $promo_price,
                $stock, $sizes, $colors, $type, $main_image, $is_new,
                $id
            ]);
            $product_id = $id;

            if (!empty($uploaded_images)) {
                $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image) VALUES (?, ?)");
                foreach ($uploaded_images as $img) {
                    $stmt->execute([$product_id, $img]);
                }
            }
            $_SESSION['flash'] = 'Produit mis à jour.';
        } else {
            $sql = "INSERT INTO products (category_id, name, description, price, promo_price,
                                          stock, sizes, colors, type, image, is_new)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $category_id, $name, $description, $price, $promo_price,
                $stock, $sizes, $colors, $type, $main_image, $is_new
            ]);
            $product_id = $pdo->lastInsertId();

            if (!empty($uploaded_images)) {
                $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image) VALUES (?, ?)");
                foreach ($uploaded_images as $img) {
                    $stmt->execute([$product_id, $img]);
                }
            }
            $_SESSION['flash'] = 'Produit ajouté.';
        }
        header('Location: ' . SITE_URL . '/admin/products.php');
        exit;
    }
}

// ============================================================
// RÉCUPÉRATION DES DONNÉES
// ============================================================
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$products = $pdo->query("SELECT p.*, c.name AS category_name FROM products p
                         LEFT JOIN categories c ON p.category_id = c.id
                         ORDER BY p.created_at DESC")->fetchAll();

$types = $pdo->query("SELECT DISTINCT type FROM products WHERE type IS NOT NULL AND type != '' ORDER BY type")->fetchAll(PDO::FETCH_COLUMN);

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch();
    if (!$edit) {
        $_SESSION['flash'] = 'Produit introuvable.';
        header('Location: ' . SITE_URL . '/admin/products.php');
        exit;
    }
}

$form_data = $edit ?? $_POST ?? [];

// --- Liste des couleurs ---
$color_list = [
    'Noir'    => '#000000',
    'Blanc'   => '#ffffff',
    'Rouge'   => '#d32f2f',
    'Bordeaux'=> '#6d1f2f',
    'Rose'    => '#f4a3c1',
    'Bleu'    => '#1f5fa8',
    'Marine'  => '#1a2b4a',
    'Vert'    => '#2e7d32',
    'Kaki'    => '#7a7a52',
    'Jaune'   => '#f4d03f',
    'Orange'  => '#e67e22',
    'Marron'  => '#6d4c33',
    'Camel'   => '#c19a6b',
    'Violet'  => '#7d3c98',
    'Doré'    => '#c9a227',
    'Nude'    => '#e0b894',
    'Gris'    => '#8c8c8c',
    'Beige'   => '#d9c7a3',
    'Argent'  => '#c0c0c0',
    'Multicolore' => '#ff6b6b',
];

$existing_colors = [];
if (!empty($form_data['colors'])) {
    if (is_array($form_data['colors'])) {
        $existing_colors = array_map('trim', $form_data['colors']);
    } else {
        $existing_colors = array_map('trim', explode(',', $form_data['colors']));
    }
    $existing_colors = array_filter($existing_colors);
}

$existing_images = [];
if (!empty($edit['id'])) {
    $stmt = $pdo->prepare("SELECT id, image FROM product_images WHERE product_id = ? ORDER BY id");
    $stmt->execute([$edit['id']]);
    $existing_images = $stmt->fetchAll();
}
?>

<link rel="stylesheet" href="<?= SITE_URL ?>/admin/assets/admin-products.css">

<style>
/* --- Styles des boules de couleur --- */
.color-checkbox-group {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    margin-top: 6px;
}
.color-swatch-item {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    gap: 4px;
    cursor: pointer;
    position: relative;
    width: 52px;
}
.color-swatch-item input[type="checkbox"] {
    display: none;
}
.color-swatch-item .swatch-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid #d1d5db;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    position: relative;
}
.color-swatch-item input[type="checkbox"]:checked + .swatch-circle {
    border-color: #1a1a1a;
    border-width: 3px;
    box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.08);
}
.color-swatch-item input[type="checkbox"]:checked + .swatch-circle::after {
    content: "✓";
    font-size: 20px;
    font-weight: bold;
    color: #000;
    text-shadow: 0 0 4px rgba(255, 255, 255, 0.6);
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
.color-swatch-item input[type="checkbox"]:checked + .swatch-circle[data-dark="1"]::after {
    color: #fff;
    text-shadow: 0 0 4px rgba(0, 0, 0, 0.6);
}
.color-swatch-item:hover .swatch-circle {
    transform: scale(1.05);
    border-color: #9ca3af;
}
.color-swatch-item .swatch-label {
    font-size: 0.62rem;
    color: #5C605F;
    display: block;
    text-align: center;
    line-height: 1.2;
    margin-left: 0;
}
.color-other-group {
    margin-top: 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.color-other-group input[type="text"] {
    width: 200px;
    padding: 6px 10px;
    border: 1.5px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.85rem;
    background: #f8fafc;
}
.color-other-group input[type="text"]:focus {
    outline: none;
    border-color: #5C1A2E;
    background: #fff;
}
.text-muted {
    color: #5C605F;
    font-size: 0.75rem;
}

/* --- Zone d'upload --- */
.upload-drop {
    position: relative;
    border: 1.5px dashed #d8ccd1;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    background: #f8fafc;
    cursor: pointer;
    transition: border-color 0.2s ease;
}
.upload-drop:hover { border-color: #5C1A2E; }
.upload-drop input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
}
.upload-drop__text {
    font-size: 0.8rem;
    color: #5C605F;
}

/* --- Images existantes (édition) --- */
.product-images-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}
.product-images-preview .img-item {
    position: relative;
    display: inline-block;
}
.product-images-preview .img-item img {
    width: 64px;
    height: 64px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}
.product-images-preview .img-item .delete-img {
    position: absolute;
    top: -6px;
    right: -6px;
    background: #ef4444;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    line-height: 20px;
    text-align: center;
    cursor: pointer;
    transition: transform 0.2s;
}
.product-images-preview .img-item .delete-img:hover {
    transform: scale(1.15);
}

/* --- Prévisualisation des nouvelles photos sélectionnées (avant envoi) --- */
.image-previews {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}
.image-previews .preview-item {
    position: relative;
    display: inline-block;
}
.image-previews .preview-item img {
    width: 64px;
    height: 64px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #22c55e;
}
.image-previews .preview-item .remove-preview {
    position: absolute;
    top: -6px;
    right: -6px;
    background: #ef4444;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    line-height: 20px;
    text-align: center;
    cursor: pointer;
    transition: transform 0.2s;
}
.image-previews .preview-item .remove-preview:hover {
    transform: scale(1.15);
}
.image-previews .preview-item .preview-main-badge {
    position: absolute;
    bottom: -6px;
    left: 50%;
    transform: translateX(-50%);
    background: #5C1A2E;
    color: #fff;
    font-size: 0.55rem;
    padding: 1px 6px;
    border-radius: 999px;
    white-space: nowrap;
}
</style>

<?php if (isset($_SESSION['flash'])): ?>
    <div class="flash-message"><?= e($_SESSION['flash']) ?></div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
<?php if (!empty($errors)): ?>
    <div class="error-message">
        <?php foreach ($errors as $err): ?><p style="margin:0;">• <?= e($err) ?></p><?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="products-layout">
    <!-- COLONNE GAUCHE : LISTE DES PRODUITS -->
    <div class="products-list">
        <div style="padding:16px 16px 0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
            <h3 style="margin:0; font-size:1.1rem;">Tous les produits</h3>
            <span style="font-size:0.8rem; color:var(--admin-text-light, #5C605F);"><?= count($products) ?> article(s)</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Cat.</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--admin-text-light, #5C605F);">Aucun produit.</td></tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($p['image'])): ?>
                                        <?php if (filter_var($p['image'], FILTER_VALIDATE_URL)): ?>
                                            <img src="<?= e($p['image']) ?>" class="product-thumb">
                                        <?php else: ?>
                                            <img src="<?= SITE_URL ?>/assets/images/products/<?= e($p['image']) ?>" class="product-thumb">
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($p['name']) ?>
                                    <?php if ($p['stock'] == 0): ?>
                                        <span class="status-pill status-annulee" style="font-size:0.6rem;">Rupture</span>
                                    <?php endif; ?>
                                    <?php if ($p['is_new']): ?>
                                        <span class="status-pill status-confirmee" style="font-size:0.6rem;">Nouveau</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($p['category_name'] ?? '—') ?></td>
                                <td>
                                    <?php if (!empty($p['promo_price']) && $p['promo_price'] > 0): ?>
                                        <span style="text-decoration:line-through;color:#b0b0b0;margin-right:4px;font-size:0.85rem;"><?= format_price($p['price']) ?></span>
                                        <span style="color:#211C17;font-weight:400;"><?= format_price($p['promo_price']) ?></span>
                                    <?php else: ?>
                                        <?= format_price($p['price']) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="color:<?= $p['stock'] <= 5 && $p['stock'] > 0 ? '#f59e0b' : ($p['stock'] == 0 ? '#ef4444' : '#16a34a') ?>;font-weight:600;">
                                        <?= (int)$p['stock'] ?>
                                    </span>
                                </td>
                                <td style="text-align:center;">
                                    <div class="actions">
                                        <a href="<?= SITE_URL ?>/admin/products.php?edit=<?= (int)$p['id'] ?>">Modifier</a>
                                        <a href="<?= SITE_URL ?>/admin/products.php?supprimer=<?= (int)$p['id'] ?>" class="delete" onclick="return confirm('Supprimer ce produit ?');">Suppr.</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- COLONNE DROITE : FORMULAIRE (ajout / édition) -->
    <div class="form-panel">
        <h3><?= $edit ? 'Modifier le produit' : 'Nouveau produit' ?></h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">

            <div class="form-group">
                <label>Nom *</label>
                <input type="text" name="name" required value="<?= e($form_data['name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Catégorie *</label>
                <select name="category_id" id="category_id" required>
                    <option value="">— Choisir —</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= (($form_data['category_id'] ?? 0) == $c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" id="type-group" style="<?= (($form_data['category_id'] ?? 0) == 2) ? 'display:block;' : 'display:none;' ?>">
                <label>Type (sous-catégorie)</label>
                <select name="type" id="type_select">
                    <option value="">— Aucun —</option>
                    <?php foreach ($types as $t): ?>
                        <option value="<?= e($t) ?>" <?= (($form_data['type'] ?? '') == $t) ? 'selected' : '' ?>><?= e($t) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted" style="display:block;margin-top:4px;">Choisissez une sous-catégorie pour filtrer les articles dans le menu.</small>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"><?= e($form_data['description'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Prix (DA) *</label>
                    <input type="number" step="0.01" name="price" required value="<?= e($form_data['price'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Prix promo (DA)</label>
                    <input type="number" step="0.01" name="promo_price" value="<?= e($form_data['promo_price'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Stock *</label>
                <input type="number" name="stock" required value="<?= e($form_data['stock'] ?? 0) ?>">
            </div>

            <div class="form-group">
                <label>Tailles (séparées par virgules)</label>
                <input type="text" name="sizes" placeholder="S,M,L,XL" value="<?= e($form_data['sizes'] ?? '') ?>">
            </div>

            <!-- COULEURS -->
            <div class="form-group">
                <label>Couleurs</label>
                <div class="color-checkbox-group">
                    <?php foreach ($color_list as $color_name => $hex): ?>
                        <?php
                            $is_checked = in_array($color_name, $existing_colors);
                            $r = hexdec(substr($hex, 1, 2));
                            $g = hexdec(substr($hex, 3, 2));
                            $b = hexdec(substr($hex, 5, 2));
                            $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
                            $dark = $brightness < 128 ? 1 : 0;
                        ?>
                        <label class="color-swatch-item">
                            <input type="checkbox" name="colors[]" value="<?= e($color_name) ?>" <?= $is_checked ? 'checked' : '' ?>>
                            <span class="swatch-circle" style="background-color: <?= e($hex) ?>;" data-dark="<?= $dark ?>"></span>
                            <span class="swatch-label"><?= e($color_name) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="color-other-group">
                    <input type="text" name="color_other" placeholder="Autre couleur..." value="<?= e($_POST['color_other'] ?? '') ?>">
                    <small style="font-size:0.7rem;color:#5C605F;">Ajoutez une couleur personnalisée</small>
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- UPLOAD — un seul champ, plusieurs photos, preview + retrait -->
            <!-- ========================================================= -->
            <div class="form-group">
                <label>Photos du produit <?= $edit ? '' : '*' ?></label>

                <label class="upload-drop" for="imageInput">
                    <input type="file" name="images[]" accept=".jpg,.jpeg,.png,.webp" multiple id="imageInput">
                    <div class="upload-drop__text">
                        📷 Cliquez pour choisir une ou plusieurs photos<br>
                        <span class="text-muted">JPG, PNG, WEBP — la première photo devient l'image principale</span>
                    </div>
                </label>

                <!-- Aperçu des photos sélectionnées, pas encore envoyées -->
                <div id="imagePreviews" class="image-previews"></div>

                <?php if (!empty($existing_images)): ?>
                    <div class="text-muted" style="margin-top:14px;margin-bottom:4px;">Photos actuelles du produit :</div>
                    <div class="product-images-preview">
                        <?php foreach ($existing_images as $img): ?>
                            <div class="img-item">
                                <?php if (filter_var($img['image'], FILTER_VALIDATE_URL)): ?>
                                    <img src="<?= e($img['image']) ?>" alt="image">
                                <?php else: ?>
                                    <img src="<?= SITE_URL ?>/assets/images/products/<?= e($img['image']) ?>" alt="image">
                                <?php endif; ?>
                                <a href="<?= SITE_URL ?>/admin/products.php?supprimer_image=<?= (int)$img['id'] ?>&product_id=<?= (int)$edit['id'] ?>" class="delete-img" onclick="return confirm('Supprimer cette image ?');">✕</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <small class="text-muted">Cliquez sur ✕ pour supprimer une photo existante.</small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_new" <?= !empty($form_data['is_new']) ? 'checked' : '' ?>>
                    Marquer comme nouveauté
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <?= $edit ? 'Enregistrer les modifications' : 'Ajouter le produit' ?>
            </button>

            <?php if ($edit): ?>
                <a href="<?= SITE_URL ?>/admin/products.php" class="btn btn-secondary btn-block">Annuler</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion affichage du champ "Type"
    const categorySelect = document.getElementById('category_id');
    const typeGroup = document.getElementById('type-group');
    const typeSelect = document.getElementById('type_select');

    function toggleTypeField() {
        const catId = parseInt(categorySelect.value);
        if (catId === 2) {
            typeGroup.style.display = 'block';
        } else {
            typeGroup.style.display = 'none';
            typeSelect.value = '';
        }
    }

    categorySelect.addEventListener('change', toggleTypeField);
    toggleTypeField();

    // ============================================================
    // PRÉVISUALISATION AVEC SUPPRESSION INDIVIDUELLE AVANT ENVOI
    // ============================================================
    const imageInput = document.getElementById('imageInput');
    const previewContainer = document.getElementById('imagePreviews');
    let selectedFiles = [];

    function updatePreviews() {
        previewContainer.innerHTML = '';
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'preview-item';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Aperçu">
                    <button type="button" class="remove-preview" data-index="${index}">✕</button>
                    ${index === 0 ? '<span class="preview-main-badge">Principale</span>' : ''}
                `;
                previewContainer.appendChild(div);
                div.querySelector('.remove-preview').addEventListener('click', function() {
                    const idx = parseInt(this.dataset.index);
                    selectedFiles.splice(idx, 1);
                    const dt = new DataTransfer();
                    selectedFiles.forEach(f => dt.items.add(f));
                    imageInput.files = dt.files;
                    updatePreviews();
                });
            };
            reader.readAsDataURL(file);
        });
    }

    imageInput.addEventListener('change', function() {
        const files = Array.from(this.files);
        selectedFiles = selectedFiles.concat(files);
        const dt = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        this.files = dt.files;
        updatePreviews();
        // On ne réinitialise pas this.value pour garder la sélection cumulée
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php ob_end_flush(); ?>