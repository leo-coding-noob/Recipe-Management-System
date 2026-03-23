<?php
// ============================================================
// favorites.php — User's saved favorites
// ============================================================
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
include 'config.php';

$uid = $_SESSION['user_id'];
$favs = $conn->query("
    SELECT r.*, u.username as author
    FROM favorites f
    JOIN recipes r ON f.recipe_id = r.id
    JOIN users u ON r.user_id = u.id
    WHERE f.user_id = $uid
    ORDER BY f.created_at DESC
");

$page_title = "My Favorites — RecipeNest";
$extra_css  = ['home'];
include 'header.php';
?>

<div class="form-page" style="max-width:1000px">
    <div class="form-header">
        <a href="dashboard.php" class="back-link">← Dashboard</a>
        <h1>⭐ My Favorites</h1>
        <p>Recipes you've saved for quick access.</p>
    </div>

    <?php if ($favs->num_rows === 0): ?>
        <div class="empty-state" style="margin-top:40px">
            <span class="empty-icon">⭐</span>
            <h3>No favorites yet</h3>
            <p>Browse recipes and click the ♡ button to save them here.</p>
            <a href="index.php" class="btn-primary" style="margin-top:16px;display:inline-block">Browse Recipes</a>
        </div>
    <?php else: ?>
    <div class="recipe-grid" style="margin-top:24px">
        <?php while ($r = $favs->fetch_assoc()):
            $img = !empty($r['image']) ? "../uploads/" . htmlspecialchars($r['image']) : null;
        ?>
        <article class="recipe-card">
            <a href="view.php?id=<?= $r['id'] ?>" class="card-img-link">
                <?php if ($img): ?>
                    <img src="<?= $img ?>" alt="" class="card-img" loading="lazy">
                <?php else: ?>
                    <div class="card-img-placeholder"><span>🍽</span></div>
                <?php endif; ?>
                <span class="card-category"><?= htmlspecialchars($r['category']) ?></span>
            </a>
            <div class="card-body">
                <h3 class="card-title"><a href="view.php?id=<?= $r['id'] ?>"><?= htmlspecialchars($r['title']) ?></a></h3>
                <p class="card-author">by <?= htmlspecialchars($r['author']) ?></p>
                <div class="card-actions">
                    <a href="view.php?id=<?= $r['id'] ?>" class="btn-view">View</a>
                    <button class="btn-fav active" onclick="toggleFavorite(<?= $r['id'] ?>, this)" title="Remove from favorites">⭐</button>
                </div>
            </div>
        </article>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
