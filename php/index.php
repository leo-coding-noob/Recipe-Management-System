<?php
// ============================================================
// index.php — PUBLIC homepage, no login required
// ============================================================
session_start();
include 'config.php';

$category_filter = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
$search_query    = isset($_GET['q'])        ? $conn->real_escape_string($_GET['q'])        : '';

// Pagination
$per_page     = 9;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset       = ($current_page - 1) * $per_page;

// Build WHERE
$where = "WHERE 1=1";
if ($category_filter) $where .= " AND r.category = '$category_filter'";
if ($search_query)    $where .= " AND (r.title LIKE '%$search_query%' OR r.ingredients LIKE '%$search_query%')";

// Count total
$total_result = $conn->query("SELECT COUNT(*) as c FROM recipes r $where");
$total_recipes = $total_result->fetch_assoc()['c'];
$total_pages   = ceil($total_recipes / $per_page);



// Fetch recipes with author name
$sql = "SELECT r.*, u.username as author
        FROM recipes r
        JOIN users u ON r.user_id = u.id
        $where
        ORDER BY r.created_at DESC
        LIMIT $per_page OFFSET $offset";
$result = $conn->query($sql);

// All categories for filter bar
$cats_result = $conn->query("SELECT category, COUNT(*) as c FROM recipes GROUP BY category ORDER BY c DESC");

$page_title = "RecipeNest — Discover Recipes";
$extra_css  = ['home'];
include 'header.php';
?>

<!-- HERO -->
<section class="hero">
    <div class="hero-text">
        <p class="hero-eyebrow">Welcome to RecipeNest</p>
        <h1 class="hero-title">Discover &amp; Share<br><em>Delicious Recipes</em></h1>
        <p class="hero-sub">Browse hundreds of recipes. No account needed to explore — sign up only when you want to contribute.</p>
        <form class="hero-search" method="GET" action="index.php">
            <input type="text" name="q" placeholder="Search recipes, ingredients…" value="<?= htmlspecialchars($search_query) ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    <div class="hero-graphic">
        <div class="hero-blob">
            <span class="hero-emoji">🥘</span>
        </div>
    </div>
</section>

<!-- CATEGORY FILTER -->
<section class="category-bar-wrap">
    <div class="category-bar">
        <a href="index.php" class="cat-chip <?= !$category_filter ? 'active' : '' ?>">All</a>
        <?php
        $cats_result->data_seek(0);
        while ($cat = $cats_result->fetch_assoc()):
            $icons = ['Breakfast'=>'🌅','Lunch'=>'☀️','Dinner'=>'🌙','Snacks'=>'🍿','Dessert'=>'🍰','Uncategorized'=>'📋'];
            $icon  = $icons[$cat['category']] ?? '🍽';
        ?>
        <a href="index.php?category=<?= urlencode($cat['category']) ?><?= $search_query ? '&q='.urlencode($search_query) : '' ?>"
           class="cat-chip <?= $category_filter === $cat['category'] ? 'active' : '' ?>">
            <?= $icon ?> <?= htmlspecialchars($cat['category']) ?>
            <span class="cat-count"><?= $cat['c'] ?></span>
        </a>
        <?php endwhile; ?>
    </div>
</section>

<!-- RESULTS INFO -->
<section class="recipes-section">
    <div class="section-header">
        <h2 class="section-title">
            <?php if ($search_query): ?>
                Results for "<?= htmlspecialchars($search_query) ?>"
            <?php elseif ($category_filter): ?>
                <?= htmlspecialchars($category_filter) ?> Recipes
            <?php else: ?>
                All Recipes
            <?php endif; ?>
            <span class="count-badge"><?= $total_recipes ?></span>
        </h2>
        <?php if ($search_query || $category_filter): ?>
            <a href="index.php" class="clear-filter">Clear filter ×</a>
        <?php endif; ?>
    </div>

    <!-- RECIPE GRID -->
    <?php if ($total_recipes === 0): ?>
        <div class="empty-state">
            <span class="empty-icon">🥗</span>
            <h3>No recipes found</h3>
            <p>Try a different search or <a href="index.php">browse all recipes</a></p>
        </div>
    <?php else: ?>
    <div class="recipe-grid" id="recipeGrid">
        <?php while ($row = $result->fetch_assoc()):
            $img = !empty($row['image']) ? "../uploads/" . htmlspecialchars($row['image']) : null;
            $cat_colors = ['Breakfast'=>'var(--cat-breakfast)','Lunch'=>'var(--cat-lunch)','Dinner'=>'var(--cat-dinner)',
                           'Snacks'=>'var(--cat-snacks)','Dessert'=>'var(--cat-dessert)','Uncategorized'=>'var(--cat-other)'];
            $cat_color  = $cat_colors[$row['category']] ?? 'var(--cat-other)';
        ?>
        <article class="recipe-card" data-category="<?= htmlspecialchars($row['category']) ?>">
            <a href="view.php?id=<?= $row['id'] ?>" class="card-img-link">
                <?php if ($img): ?>
                    <img src="<?= $img ?>" alt="<?= htmlspecialchars($row['title']) ?>" class="card-img" loading="lazy">
                <?php else: ?>
                    <div class="card-img-placeholder">
                        <span><?= ['🍲','🥘','🍛','🥗','🍜','🍝'][($row['id'] - 1) % 6] ?></span>
                    </div>
                <?php endif; ?>
                <span class="card-category" style="background:<?= $cat_color ?>"><?= htmlspecialchars($row['category']) ?></span>
            </a>
            <div class="card-body">
                <h3 class="card-title">
                    <a href="view.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a>
                </h3>
                <div class="card-meta">
                    <?php if ($row['prep_time'] || $row['cook_time']): ?>
                        <span class="meta-item">⏱ <?= ($row['prep_time'] + $row['cook_time']) ?> min</span>
                    <?php endif; ?>
                    <?php if ($row['servings']): ?>
                        <span class="meta-item">👥 <?= $row['servings'] ?> servings</span>
                    <?php endif; ?>
                </div>
                <p class="card-author">by <?= htmlspecialchars($row['author']) ?></p>
                <div class="card-actions">
                    <a href="view.php?id=<?= $row['id'] ?>" class="btn-view">View Recipe</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <button class="btn-fav <?= '' ?>"
                                onclick="toggleFavorite(<?= $row['id'] ?>, this)"
                                title="Add to favorites">♡</button>
                    <?php endif; ?>
                </div>
            </div>
        </article>
        <?php endwhile; ?>
    </div>

    <!-- PAGINATION -->
    <?php if ($total_pages > 1): ?>
    <style>
        .pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 40px 0 20px;
            flex-wrap: wrap;
        }
        .page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 42px;
            height: 42px;
            padding: 0 14px;
            border-radius: 10px;
            border: 1.5px solid #e2e0f0;
            background: #fff;
            color: #444;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            font-family: 'DM Sans', sans-serif;
        }
        .page-btn:hover {
            border-color: #6c63ff;
            color: #6c63ff;
            background: #f4f3ff;
        }
        .page-btn.active {
            background: #6c63ff;
            color: #fff;
            border-color: #6c63ff;
            font-weight: 600;
        }
        .page-btn.prev-next {
            padding: 0 18px;
            font-weight: 600;
            letter-spacing: 0.01em;
        }
        .page-info {
            font-size: 13px;
            color: #888;
            margin: 0 8px;
            font-family: 'DM Sans', sans-serif;
        }
    </style>
    <div class="pagination">
        <?php if ($current_page > 1): ?>
            <a href="?page=<?= $current_page - 1 ?><?= $category_filter ? '&category='.urlencode($category_filter) : '' ?><?= $search_query ? '&q='.urlencode($search_query) : '' ?>" class="page-btn prev-next">← Prev</a>
        <?php else: ?>
            <span class="page-btn prev-next" style="opacity:0.35;cursor:default;pointer-events:none">← Prev</span>
        <?php endif; ?>

        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
            <a href="?page=<?= $p ?><?= $category_filter ? '&category='.urlencode($category_filter) : '' ?><?= $search_query ? '&q='.urlencode($search_query) : '' ?>"
               class="page-btn <?= $p === $current_page ? 'active' : '' ?>"><?= $p ?></a>
        <?php endfor; ?>

        <span class="page-info">Page <?= $current_page ?> of <?= $total_pages ?></span>

        <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?= $current_page + 1 ?><?= $category_filter ? '&category='.urlencode($category_filter) : '' ?><?= $search_query ? '&q='.urlencode($search_query) : '' ?>" class="page-btn prev-next">Next →</a>
        <?php else: ?>
            <span class="page-btn prev-next" style="opacity:0.35;cursor:default;pointer-events:none">Next →</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</section>

<!-- CTA for guests -->
<?php if (!isset($_SESSION['user_id'])): ?>
<section class="cta-banner">
    <div class="cta-content">
        <h2>Have a recipe to share?</h2>
        <p>Create a free account and add your own recipes to RecipeNest.</p>
        <div class="cta-btns">
            <a href="signup.php" class="btn-primary">Create Free Account</a>
            <a href="login.php" class="btn-outline">Login</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'footer.php'; ?>
