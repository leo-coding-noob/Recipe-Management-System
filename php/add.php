<?php
// ============================================================
// add.php — Protected: must be logged in
// ============================================================
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=add");
    exit;
}
include 'config.php';

$error   = "";
$success = "";

if (isset($_POST['submit'])) {
    $uid          = $_SESSION['user_id'];
    $title        = $conn->real_escape_string(trim($_POST['title']));
    $category     = $conn->real_escape_string($_POST['category']);
    $ingredients  = $conn->real_escape_string(trim($_POST['ingredients']));
    $instructions = $conn->real_escape_string(trim($_POST['instructions']));
    $prep_time    = intval($_POST['prep_time']);
    $cook_time    = intval($_POST['cook_time']);
    $servings     = intval($_POST['servings']);

    if (!$title || !$ingredients || !$instructions) {
        $error = "Please fill in all required fields.";
    } else {
        // Image upload
        $imageName = "NULL";
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === 0) {
            $allowed   = ['image/jpeg','image/png','image/jpg','image/webp','image/gif'];
            $max_size  = 5 * 1024 * 1024; // 5MB
            if (!in_array($_FILES['image']['type'], $allowed)) {
                $error = "Only JPG, PNG, WEBP images are allowed.";
            } elseif ($_FILES['image']['size'] > $max_size) {
                $error = "Image must be under 5MB.";
            } else {
                $ext       = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $imageName = "'" . time() . "_" . uniqid() . "." . $ext . "'";
                $target    = "../uploads/" . trim($imageName, "'");
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                    $error = "Failed to upload image. Check uploads folder permissions.";
                    $imageName = "NULL";
                }
            }
        }

        if (!$error) {
            $sql = "INSERT INTO recipes (user_id, title, category, ingredients, instructions, image, prep_time, cook_time, servings)
                    VALUES ($uid, '$title', '$category', '$ingredients', '$instructions', $imageName, $prep_time, $cook_time, $servings)";
            if ($conn->query($sql)) {
                logActivity($conn, $uid, 'added', $title);
                header("Location: dashboard.php?added=1");
                exit;
            } else {
                $error = "Database error: " . $conn->error;
            }
        }
    }
}

$page_title = "Add Recipe — RecipeNest";
$extra_css  = ['form'];
include 'header.php';
?>

<div class="form-page">
    <div class="form-header">
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        <h1>Add New Recipe</h1>
        <p>Share your recipe with the RecipeNest community.</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error">⚠ <?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="recipe-form">

        <!-- Image Upload -->
        <div class="form-section">
            <h2 class="section-label">Recipe Photo</h2>
            <div class="image-upload-area" id="uploadArea" onclick="document.getElementById('imageInput').click()">
                <div class="upload-inner" id="uploadInner">
                    <span class="upload-icon">📷</span>
                    <p>Click to upload a photo</p>
                    <small>JPG, PNG, WEBP up to 5MB</small>
                </div>
                <img id="imagePreview" class="image-preview hidden" alt="Preview">
            </div>
            <input type="file" name="image" id="imageInput" accept="image/*" style="display:none"
                   onchange="previewImage(this)">
        </div>

        <!-- Basic Info -->
        <div class="form-section">
            <h2 class="section-label">Basic Info</h2>
            <div class="form-row">
                <div class="form-group full">
                    <label>Recipe Title <span class="req">*</span></label>
                    <input type="text" name="title" required placeholder="e.g. Grandma's Dal Bhat"
                           value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Category <span class="req">*</span></label>
                    <select name="category">
                        <?php foreach (['Breakfast','Lunch','Dinner','Snacks','Dessert','Uncategorized'] as $cat): ?>
                            <option value="<?= $cat ?>" <?= (isset($_POST['category']) && $_POST['category'] === $cat) ? 'selected' : '' ?>>
                                <?= $cat ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Servings</label>
                    <input type="number" name="servings" min="1" max="100" value="<?= isset($_POST['servings']) ? intval($_POST['servings']) : 2 ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Prep Time (minutes)</label>
                    <input type="number" name="prep_time" min="0" value="<?= isset($_POST['prep_time']) ? intval($_POST['prep_time']) : 0 ?>">
                </div>
                <div class="form-group">
                    <label>Cook Time (minutes)</label>
                    <input type="number" name="cook_time" min="0" value="<?= isset($_POST['cook_time']) ? intval($_POST['cook_time']) : 0 ?>">
                </div>
            </div>
        </div>

        <!-- Ingredients -->
        <div class="form-section">
            <h2 class="section-label">Ingredients <span class="req">*</span></h2>
            <p class="field-hint">Separate each ingredient with a comma: <em>Chicken, Onion, Garlic, Salt</em></p>
            <textarea name="ingredients" required rows="4" placeholder="Chicken, Onion, Garlic, Ginger, Tomato, Salt, Pepper…"><?= isset($_POST['ingredients']) ? htmlspecialchars($_POST['ingredients']) : '' ?></textarea>
        </div>

        <!-- Instructions -->
        <div class="form-section">
            <h2 class="section-label">Instructions <span class="req">*</span></h2>
            <p class="field-hint">Write step by step. Each new line becomes a new step.</p>
            <textarea name="instructions" required rows="7" placeholder="Step 1: Heat oil in a pan…&#10;Step 2: Add onions and fry until golden…"><?= isset($_POST['instructions']) ? htmlspecialchars($_POST['instructions']) : '' ?></textarea>
        </div>

        <div class="form-actions">
            <a href="dashboard.php" class="btn-outline">Cancel</a>
            <button type="submit" name="submit" class="btn-primary">Publish Recipe →</button>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
