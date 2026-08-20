<?php
require_once __DIR__ . '/includes/header.php';

$categories = get_categories();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image = trim($_POST['image'] ?? '');
        if (empty($name)) {
            $errors[] = 'Le nom est requis.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO categories (name, description, image) VALUES (?, ?, ?)");
            $stmt->execute([$name, $description, $image]);
            redirect('categories.php');
        }
    } elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $stmt = $pdo->prepare("UPDATE categories SET name=?, description=?, image=? WHERE id=?");
        $stmt->execute([$name, $description, $image, $id]);
        redirect('categories.php');
    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        redirect('categories.php');
    }
}
?>

<?php if (!empty($errors)): ?>
    <div class="alert alert--error"><?php foreach ($errors as $e) echo '<p>' . e($e) . '</p>'; ?></div>
<?php endif; ?>

<div style="margin-bottom:2rem;">
    <h3 style="margin-bottom:1rem;">Ajouter une catégorie</h3>
    <form class="admin-form" method="post" action="">
        <input type="hidden" name="action" value="add">
        <div class="form-field">
            <label>Nom *</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-field">
            <label>Description</label>
            <textarea name="description" rows="2"></textarea>
        </div>
        <div class="form-field">
            <label>Image (URL)</label>
            <input type="text" name="image" placeholder="https://...">
        </div>
        <button type="submit" class="btn btn--primary">Ajouter</button>
    </form>
</div>

<h3 style="margin-bottom:1rem;">Catégories existantes</h3>
<table class="admin-table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Nom</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <td><img src="<?= e($cat['image']) ?>" alt="" class="admin-thumb"></td>
                <td><?= e($cat['name']) ?></td>
                <td style="max-width:300px;"><?= e($cat['description']) ?></td>
                <td>
                    <div class="admin-actions">
                        <a href="#" onclick="editCat(<?= (int)$cat['id'] ?>, '<?= e($cat['name']) ?>', '<?= e($cat['description']) ?>', '<?= e($cat['image']) ?>');return false;">Modifier</a>
                        <form method="post" action="" style="display:inline;" onsubmit="return confirm('Supprimer cette catégorie ? Les produits associés seront aussi supprimés.');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                            <button type="submit" class="danger">Supprimer</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:2000;align-items:center;justify-content:center;">
    <div style="background:#fff;padding:2rem;max-width:500px;width:90%;">
        <h3 style="margin-bottom:1rem;">Modifier la catégorie</h3>
        <form method="post" action="">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-field">
                <label>Nom</label>
                <input type="text" name="name" id="edit_name" required>
            </div>
            <div class="form-field">
                <label>Description</label>
                <textarea name="description" id="edit_desc" rows="2"></textarea>
            </div>
            <div class="form-field">
                <label>Image (URL)</label>
                <input type="text" name="image" id="edit_image">
            </div>
            <button type="submit" class="btn btn--primary btn--block">Enregistrer</button>
            <button type="button" class="btn btn--outline btn--block" style="margin-top:0.5rem;" onclick="document.getElementById('editModal').style.display='none';">Annuler</button>
        </form>
    </div>
</div>

<script>
function editCat(id, name, desc, image) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_desc').value = desc;
    document.getElementById('edit_image').value = image;
    document.getElementById('editModal').style.display = 'flex';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
