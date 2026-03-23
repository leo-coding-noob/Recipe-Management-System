<?php
// ============================================================
// view.php — Public recipe detail page
// ============================================================
session_start();
include 'config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) { header("Location: index.php"); exit; }

$result = $conn->query("SELECT r.*, u.username as author FROM recipes r JOIN users u ON r.user_id=u.id WHERE r.id=$id");
if ($result->num_rows === 0) { header("Location: index.php"); exit; }
$row = $result->fetch_assoc();

// Is this favorited?
$is_fav = false;
if (isset($_SESSION['user_id'])) {
    $uid     = $_SESSION['user_id'];
    $fav_chk = $conn->query("SELECT id FROM favorites WHERE user_id=$uid AND recipe_id=$id");
    $is_fav  = $fav_chk->num_rows > 0;
}

// Related recipes (same category, exclude this one)
$cat_e   = $conn->real_escape_string($row['category']);
$related = $conn->query("SELECT r.*, u.username as author FROM recipes r JOIN users u ON r.user_id=u.id
                          WHERE r.category='$cat_e' AND r.id != $id ORDER BY RAND() LIMIT 3");

$page_title = htmlspecialchars($row['title']) . " — RecipeNest";
$extra_css  = ['view'];
include 'header.php';
?>

<article class="recipe-detail">

    <!-- HERO IMAGE / HEADER -->
    <div class="detail-hero <?= empty($row['image']) ? 'no-img' : '' ?>">
        <?php if (!empty($row['image'])): ?>
            <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>" class="detail-hero-img">
            <div class="detail-hero-overlay"></div>
        <?php endif; ?>
        <div class="detail-hero-content">
            <a href="index.php?category=<?= urlencode($row['category']) ?>" class="detail-cat-tag"><?= htmlspecialchars($row['category']) ?></a>
            <h1 class="detail-title"><?= htmlspecialchars($row['title']) ?></h1>
            <div class="detail-meta-row">
                <span class="detail-author">by <strong><?= htmlspecialchars($row['author']) ?></strong></span>
                <?php if ($row['prep_time'] || $row['cook_time']): ?>
                    <span class="detail-time">⏱ <?= $row['prep_time'] + $row['cook_time'] ?> min total</span>
                <?php endif; ?>
                <?php if ($row['servings']): ?>
                    <span class="detail-serves">👥 <?= $row['servings'] ?> servings</span>
                <?php endif; ?>
                <span class="detail-date">📅 <?= date('M j, Y', strtotime($row['created_at'])) ?></span>
            </div>
        </div>
    </div>

    <!-- ACTION BAR -->
    <div class="detail-action-bar">
        <a href="index.php" class="btn-ghost">← All Recipes</a>
        <div class="detail-btn-group">
            <?php if (isset($_SESSION['user_id'])): ?>
                <button class="btn-fav-lg <?= $is_fav ? 'active' : '' ?>"
                        onclick="toggleFavorite(<?= $id ?>, this)"
                        data-id="<?= $id ?>">
                    <?= $is_fav ? '⭐ Saved' : '♡ Save' ?>
                </button>
                <?php if ($_SESSION['user_id'] == $row['user_id']): ?>
                    <a href="edit.php?id=<?= $id ?>" class="btn-outline-sm">✏ Edit</a>
                    <a href="delete.php?id=<?= $id ?>" class="btn-danger-sm"
                       onclick="return confirm('Permanently delete this recipe?')">🗑 Delete</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="login.php" class="btn-outline-sm">Login to save</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- BODY -->
    <div class="detail-body">

        <!-- TIME CARDS -->
        <?php if ($row['prep_time'] || $row['cook_time'] || $row['servings']): ?>
        <div class="time-cards">
            <?php if ($row['prep_time']): ?>
            <div class="time-card">
                <span class="tc-label">Prep Time</span>
                <span class="tc-value"><?= $row['prep_time'] ?> min</span>
            </div>
            <?php endif; ?>
            <?php if ($row['cook_time']): ?>
            <div class="time-card">
                <span class="tc-label">Cook Time</span>
                <span class="tc-value"><?= $row['cook_time'] ?> min</span>
            </div>
            <?php endif; ?>
            <?php if ($row['prep_time'] && $row['cook_time']): ?>
            <div class="time-card highlight">
                <span class="tc-label">Total Time</span>
                <span class="tc-value"><?= $row['prep_time'] + $row['cook_time'] ?> min</span>
            </div>
            <?php endif; ?>
            <?php if ($row['servings']): ?>
            <div class="time-card">
                <span class="tc-label">Servings</span>
                <span class="tc-value"><?= $row['servings'] ?></span>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="detail-two-col">
            <!-- INGREDIENTS -->
            <div class="ingredients-box">
                <h2>🥕 Ingredients</h2>
                <ul class="ingredients-list">
                    <?php foreach (explode(",", $row['ingredients']) as $ing): ?>
                        <li><?= htmlspecialchars(trim($ing)) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- INSTRUCTIONS -->
            <div class="instructions-box">
                <h2>📋 Instructions</h2>
                <ol class="instructions-list">
                    <?php
                    $steps = array_filter(array_map('trim', explode("\n", $row['instructions'])));
                    foreach ($steps as $step):
                    ?>
                        <li><?= nl2br(htmlspecialchars($step)) ?></li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>

    </div>
</article>

<!-- RELATED RECIPES -->
<?php if ($related->num_rows > 0): ?>
<section class="related-section">
    <h2>More <?= htmlspecialchars($row['category']) ?> Recipes</h2>
    <div class="related-grid">
        <?php while ($rel = $related->fetch_assoc()): ?>
        <a href="view.php?id=<?= $rel['id'] ?>" class="related-card">
            <?php if (!empty($rel['image'])): ?>
                <img src="../uploads/<?= htmlspecialchars($rel['image']) ?>" alt="">
            <?php else: ?>
                <div class="related-placeholder">🍽</div>
            <?php endif; ?>
            <div class="related-info">
                <h4><?= htmlspecialchars($rel['title']) ?></h4>
                <span>by <?= htmlspecialchars($rel['author']) ?></span>
            </div>
        </a>
        <?php endwhile; ?>
    </div>
</section>
<?php endif; ?>

<?php include 'footer.php'; ?>
