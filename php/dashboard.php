<?php
// ============================================================
// dashboard.php — Logged-in user's personal dashboard
// ============================================================
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
include 'config.php';

$uid = $_SESSION['user_id'];

// Stats
$total_all   = $conn->query("SELECT COUNT(*) as c FROM recipes")->fetch_assoc()['c'];
$total_mine  = $conn->query("SELECT COUNT(*) as c FROM recipes WHERE user_id=$uid")->fetch_assoc()['c'];
$total_favs  = $conn->query("SELECT COUNT(*) as c FROM favorites WHERE user_id=$uid")->fetch_assoc()['c'];

// Category breakdown (all recipes)
$cats = $conn->query("SELECT category, COUNT(*) as c FROM recipes GROUP BY category ORDER BY c DESC");
$cat_data = [];
while ($c = $cats->fetch_assoc()) $cat_data[] = $c;
$max_cat = $cat_data[0]['c'] ?? 1;

// My recent recipes
$my_recipes = $conn->query("SELECT * FROM recipes WHERE user_id=$uid ORDER BY created_at DESC LIMIT 6");

// Recent activity
$activity = $conn->query("SELECT * FROM activity_log WHERE user_id=$uid ORDER BY created_at DESC LIMIT 8");

// Favorite recipes
$fav_recipes = $conn->query("
    SELECT r.*, u.username as author
    FROM favorites f
    JOIN recipes r ON f.recipe_id = r.id
    JOIN users u ON r.user_id = u.id
    WHERE f.user_id = $uid
    ORDER BY f.created_at DESC
    LIMIT 4
");

// All recipes count per month for mini chart (last 6 months)
$months_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month_label = date('M', strtotime("-$i months"));
    $month_num   = date('Y-m', strtotime("-$i months"));
    $cnt = $conn->query("SELECT COUNT(*) as c FROM recipes WHERE user_id=$uid AND DATE_FORMAT(created_at,'%Y-%m')='$month_num'")->fetch_assoc()['c'];
    $months_data[] = ['label' => $month_label, 'count' => $cnt];
}
$max_month = max(array_column($months_data, 'count')) ?: 1;

$page_title = "Dashboard — RecipeNest";
$extra_css  = ['dashboard'];
$body_class = 'has-sidebar';
include 'header.php';
?>

<div class="dashboard-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-profile">
            <div class="profile-avatar"><?= strtoupper(substr($_SESSION['username'], 0, 1)) ?></div>
            <div>
                <div class="profile-name"><?= htmlspecialchars($_SESSION['username']) ?></div>
                <div class="profile-role">Member</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="slink active"><span class="slink-icon">◈</span> Dashboard</a>
            <a href="index.php"     class="slink"><span class="slink-icon">◉</span> Browse All</a>
            <a href="index.php?my=1" class="slink"><span class="slink-icon">◎</span> My Recipes</a>
            <a href="add.php"       class="slink"><span class="slink-icon">✦</span> Add Recipe</a>
            <a href="favorites.php" class="slink"><span class="slink-icon">♡</span> Favorites</a>
        </nav>
        <a href="logout.php" class="sidebar-logout">↩ Logout</a>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="dash-main">

        <!-- Top bar -->
        <div class="dash-topbar">
            <div>
                <h1 class="dash-title">Dashboard</h1>
                <p class="dash-date"><?= date('l, F j, Y') ?></p>
            </div>
            <a href="add.php" class="btn-primary">+ Add Recipe</a>
        </div>

        <!-- STAT CARDS -->
        <div class="stat-grid">
            <div class="stat-card" style="--accent:#6c63ff">
                <div class="stat-top">
                    <span class="stat-icon">📋</span>
                    <span class="stat-label">Total Recipes</span>
                </div>
                <div class="stat-value"><?= $total_all ?></div>
                <div class="stat-sub">In the system</div>
            </div>
            <div class="stat-card" style="--accent:#2ec4b6">
                <div class="stat-top">
                    <span class="stat-icon">👨‍🍳</span>
                    <span class="stat-label">My Recipes</span>
                </div>
                <div class="stat-value"><?= $total_mine ?></div>
                <div class="stat-sub">Recipes you added</div>
            </div>
            <div class="stat-card" style="--accent:#f4a261">
                <div class="stat-top">
                    <span class="stat-icon">⭐</span>
                    <span class="stat-label">Favorites</span>
                </div>
                <div class="stat-value"><?= $total_favs ?></div>
                <div class="stat-sub">Saved recipes</div>
            </div>
            <div class="stat-card" style="--accent:#e63946">
                <div class="stat-top">
                    <span class="stat-icon">🏷</span>
                    <span class="stat-label">Categories</span>
                </div>
                <div class="stat-value"><?= count($cat_data) ?></div>
                <div class="stat-sub">Recipe types</div>
            </div>
        </div>

        <!-- MIDDLE ROW: My recipes + activity -->
        <div class="dash-row">

            <!-- MY RECENT RECIPES -->
            <div class="dash-card dash-card--wide">
                <div class="dash-card-header">
                    <h2>My Recipes</h2>
                    <a href="index.php?my=1" class="card-link">View all →</a>
                </div>
                <?php if ($my_recipes->num_rows === 0): ?>
                    <div class="empty-recipes">
                        <span>🍽</span>
                        <p>You haven't added any recipes yet.</p>
                        <a href="add.php" class="btn-primary" style="margin-top:12px;display:inline-block">Add your first</a>
                    </div>
                <?php else: ?>
                <div class="recipe-list">
                    <?php while ($r = $my_recipes->fetch_assoc()):
                        $emojis = ['🍲','🥘','🍛','🥗','🍜','🍝','🥞','🍰','🍿','🌮'];
                        $emo = $emojis[$r['id'] % 10];
                        $cat_cls = strtolower(str_replace(' ', '-', $r['category']));
                    ?>
                    <div class="rlist-item">
                        <div class="rlist-thumb <?= $cat_cls ?>"><?= $emo ?></div>
                        <div class="rlist-info">
                            <a href="view.php?id=<?= $r['id'] ?>" class="rlist-title"><?= htmlspecialchars($r['title']) ?></a>
                            <div class="rlist-meta">
                                <span class="tag"><?= htmlspecialchars($r['category']) ?></span>
                                <span><?= date('M j, Y', strtotime($r['created_at'])) ?></span>
                            </div>
                        </div>
                        <div class="rlist-actions">
                            <a href="edit.php?id=<?= $r['id'] ?>" class="action-btn edit">Edit</a>
                            <a href="delete.php?id=<?= $r['id'] ?>" class="action-btn del"
                               onclick="return confirm('Delete \'<?= htmlspecialchars(addslashes($r['title'])) ?>\'?')">Del</a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="dash-col-right">

                <!-- ACTIVITY FEED -->
                <div class="dash-card">
                    <div class="dash-card-header">
                        <h2>Recent Activity</h2>
                    </div>
                    <div class="activity-feed">
                        <?php
                        $activity->data_seek(0);
                        $dot_colors = ['added'=>'#2ec4b6','edited'=>'#6c63ff','deleted'=>'#e63946'];
                        while ($act = $activity->fetch_assoc()):
                            $dot = $dot_colors[$act['action']] ?? '#888';
                        ?>
                        <div class="act-item">
                            <div class="act-dot" style="background:<?= $dot ?>"></div>
                            <div class="act-body">
                                <p>You <strong><?= $act['action'] ?></strong> <em><?= htmlspecialchars($act['recipe_title']) ?></em></p>
                                <span class="act-time"><?= date('M j, g:ia', strtotime($act['created_at'])) ?></span>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php if ($activity->num_rows === 0): ?>
                            <p class="no-activity">No activity yet.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- CATEGORY BREAKDOWN -->
                <div class="dash-card">
                    <div class="dash-card-header"><h2>By Category</h2></div>
                    <div class="cat-chart">
                        <?php foreach ($cat_data as $c):
                            $pct = round(($c['c'] / $max_cat) * 100);
                            $colors = ['Breakfast'=>'#f4a261','Lunch'=>'#2ec4b6','Dinner'=>'#6c63ff',
                                       'Snacks'=>'#e63946','Dessert'=>'#f72585','Uncategorized'=>'#aaa'];
                            $color = $colors[$c['category']] ?? '#6c63ff';
                        ?>
                        <div class="cat-row">
                            <span class="cat-name"><?= htmlspecialchars($c['category']) ?></span>
                            <div class="cat-bar-bg">
                                <div class="cat-bar-fill" style="width:<?= $pct ?>%;background:<?= $color ?>"></div>
                            </div>
                            <span class="cat-num"><?= $c['c'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- MONTHLY CHART -->
        <div class="dash-card" style="margin-top:20px">
            <div class="dash-card-header">
                <h2>My Recipes — Last 6 Months</h2>
            </div>
            <div class="month-chart">
                <?php foreach ($months_data as $m):
                    $bar_h = $max_month > 0 ? max(4, round(($m['count'] / $max_month) * 100)) : 4;
                ?>
                <div class="month-col">
                    <span class="month-count"><?= $m['count'] ?></span>
                    <div class="month-bar" style="height:<?= $bar_h ?>px"></div>
                    <span class="month-label"><?= $m['label'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- FAVORITES PREVIEW -->
        <?php if ($fav_recipes->num_rows > 0): ?>
        <div class="dash-card" style="margin-top:20px">
            <div class="dash-card-header">
                <h2>⭐ Saved Favorites</h2>
                <a href="favorites.php" class="card-link">View all →</a>
            </div>
            <div class="fav-grid">
                <?php while ($fr = $fav_recipes->fetch_assoc()): ?>
                <a href="view.php?id=<?= $fr['id'] ?>" class="fav-card">
                    <?php if (!empty($fr['image'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($fr['image']) ?>" alt="">
                    <?php else: ?>
                        <div class="fav-placeholder">🍽</div>
                    <?php endif; ?>
                    <div class="fav-title"><?= htmlspecialchars($fr['title']) ?></div>
                    <div class="fav-cat"><?= htmlspecialchars($fr['category']) ?></div>
                </a>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>

    </main>
</div>

<?php include 'footer.php'; ?>
