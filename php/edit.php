<?php
// ============================================================
// edit.php — Only the recipe's author can edit
// ============================================================
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
include 'config.php';

$uid = $_SESSION['user_id'];
$id  = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) { header("Location: dashboard.php"); exit; }

$result = $conn->query("SELECT * FROM recipes WHERE id=$id");
if ($result->num_rows === 0) { header("Location: dashboard.php"); exit; }
$recipe = $result->fetch_assoc();

// Only the author can edit
if ($recipe['user_id'] != $uid) {
    header("Location: index.php");
    exit;
}

$error = "";
if (isset($_POST['update'])) {
    $title        = $conn->real_escape_string(trim($_POST['title']));
    $category     = $conn->real_escape_string($_POST['category']);
    $ingredients  = $conn->real_escape_string(trim($_POST['ingredients']));
    $instructions = $conn->real_escape_string(trim($_POST['instructions']));
    $prep_time    = intval($_POST['prep_time']);
    $cook_time    = intval($_POST['cook_time']);
    $servings     = intval($_POST['servings']);
    $imageName    = $recipe['image'];

    if (!$title || !$ingredients || !$instructions) {
        $error = "Please fill in all required fields.";
    } else {
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === 0) {
            $allowed  = ['image/jpeg','image/png','image/jpg','image/webp'];
            $max_size = 5 * 1024 * 1024;
            if (in_array($_FILES['image']['type'], $allowed) && $_FILES['image']['size'] <= $max_size) {
                $ext       = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $newName   = time() . "_" . uniqid() . "." . $ext;
                $target    = "../uploads/" . $newName;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                    // Delete old image
                    if ($imageName && file_exists("../uploads/$imageName")) {
                        @unlink("../uploads/$imageName");
                    }
                    $imageName = $newName;
                }
            }
        }

        $imgVal = $imageName ? "'$imageName'" : "NULL";
        $sql = "UPDATE recipes SET
                    title='$title', category='$category',
                    ingredients='$ingredients', instructions='$instructions',
                    image=$imgVal, prep_time=$prep_time,
                    cook_time=$cook_time, servings=$servings
                WHERE id=$id AND user_id=$uid";
        if ($conn->query($sql)) {
            logActivity($conn, $uid, 'edited', $title);
            header("Location: dashboard.php?edited=1");
            exit;
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}

$page_title = "Edit Recipe — RecipeNest";
$extra_css  = ['form'];
include 'header.php';
?>

<div class="form-page">
    <div class="form-header">
        <a href="view.php?id=<?= $id ?>" class="back-link">← Back to Recipe</a>
        <h1>Edit Recipe</h1>
        <p>Update your recipe details below.</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error">⚠ <?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="recipe-form">

        <!-- Current image -->
        <div class="form-section">
            <h2 class="section-label">Recipe Photo</h2>
            <?php if ($recipe['image']): ?>
                <div class="current-img-wrap">
                    <img src="../uploads/<?= htmlspecialchars($recipe['image']) ?>" class="current-img" alt="Current">
                    <span class="current-img-label">Current photo</span>
                </div>
            <?php endif; ?>
            <div class="image-upload-area" id="uploadArea" onclick="document.getElementById('imageInput').click()">
                <div class="upload-inner" id="uploadInner">
                    <span class="upload-icon">📷</span>
                    <p><?= $recipe['image'] ? 'Click to change photo' : 'Click to upload a photo' ?></p>
                    <small>JPG, PNG, WEBP up to 5MB</small>
                </div>
                <img id="imagePreview" class="image-preview hidden" alt="New Preview">
            </div>
            <input type="file" name="image" id="imageInput" accept="image/*" style="display:none"
                   onchange="previewImage(this)">
        </div>

        <div class="form-section">
            <h2 class="section-label">Basic Info</h2>
            <div class="form-row">
                <div class="form-group full">
                    <label>Recipe Title <span class="req">*</span></label>
                    <input type="text" name="title" required value="<?= htmlspecialchars($recipe['title']) ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Category</label>
                    <select name="category">
                        <?php foreach (['Breakfast','Lunch','Dinner','Snacks','Dessert','Uncategorized'] as $cat): ?>
                            <option value="<?= $cat ?>" <?= $recipe['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Servings</label>
                    <input type="number" name="servings" min="1" value="<?= $recipe['servings'] ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Prep Time (min)</label>
                    <input type="number" name="prep_time" min="0" value="<?= $recipe['prep_time'] ?>">
                </div>
                <div class="form-group">
                    <label>Cook Time (min)</label>
                    <input type="number" name="cook_time" min="0" value="<?= $recipe['cook_time'] ?>">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2 class="section-label">Ingredients <span class="req">*</span></h2>
            <p class="field-hint">Separate with commas: <em>Chicken, Onion, Salt</em></p>
            <textarea name="ingredients" required rows="4"><?= htmlspecialchars($recipe['ingredients']) ?></textarea>
        </div>

        <div class="form-section">
            <h2 class="section-label">Instructions <span class="req">*</span></h2>
            <textarea name="instructions" required rows="7"><?= htmlspecialchars($recipe['instructions']) ?></textarea>
        </div>

        <div class="form-actions">
            <a href="view.php?id=<?= $id ?>" class="btn-outline">Cancel</a>
            <button type="submit" name="update" class="btn-primary">Save Changes →</button>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
