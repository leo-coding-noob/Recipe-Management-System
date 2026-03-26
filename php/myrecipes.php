<?php
// ============================================================
// myrecipes.php — Show only recipes added by logged-in user
// ============================================================

session_start();
include 'config.php';

$uid = $_SESSION['user_id'] ?? 0;
if (!$uid) {
    header("Location: login.php");
    exit;
}

// Fetch recipes added by this user
$result = $conn->query("
    SELECT r.*, u.username as author 
    FROM recipes r 
    JOIN users u ON r.user_id=u.id 
    WHERE r.user_id=$uid
    ORDER BY r.created_at DESC
");

$page_title = "My Recipes — RecipeNest";
$extra_css  = ['index']; // reuse the same CSS as index.php
include 'header.php';
?>
<style>
/* Container grid */
.recipes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px; /* spacing between cards */
}

/* Each recipe card */
.recipe-card {
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.recipe-card:hover {
    transform: translateY(-3px);
}

/* Recipe image */
.recipe-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

/* Placeholder for missing image */
.recipe-placeholder {
    width: 100%;
    height: 180px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    background: #f4f4f4;
    color: #888;
}

/* Recipe info */
.recipe-info {
    padding: 12px 15px;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.recipe-info h3 {
    margin: 0;
    font-size: 1.1rem;
    color: #333;
}

.recipe-info span {
    font-size: 0.9rem;
    color: #555;
}

/* Actions */
.recipe-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.recipe-actions a {
    text-decoration: none;
    font-size: 0.85rem;
    color: #6c63ff;
    border: 1px solid #6c63ff;
    padding: 3px 8px;
    border-radius: 5px;
    transition: all 0.2s;
}

.recipe-actions a:hover {
    background: #6c63ff;
    color: #fff;
}
</style>

<main class="recipes-main">
    <h1>👨‍🍳 My Recipes</h1>
    <p>Recipes you added to RecipeNest.</p>

    <?php if ($result->num_rows === 0): ?>
        <p>You haven't added any recipes yet. <a href="add.php">Add your first recipe</a>!</p>
    <?php else: ?>
        <div class="recipes-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="recipe-card">
                    <?php if (!empty($row['image'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                    <?php else: ?>
                        <div class="recipe-placeholder">🍽</div>
                    <?php endif; ?>
                    <div class="recipe-info">
                        <h3><?= htmlspecialchars($row['title']) ?></h3>
                        <span><?= htmlspecialchars($row['category']) ?></span>
                        <span>⏱ <?= $row['prep_time'] + $row['cook_time'] ?> min</span>
                        <span>👥 <?= $row['servings'] ?></span>
                        <span>by <?= htmlspecialchars($row['author']) ?></span>
                        <div class="recipe-actions">
                            <a href="edit.php?id=<?= $row['id'] ?>">Edit</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this recipe?')">Del</a>
                            <a href="view.php?id=<?= $row['id'] ?>">View</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>