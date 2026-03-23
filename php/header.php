<?php
// ============================================================
// header.php — Shared HTML head + nav
// ============================================================
if (session_status() === PHP_SESSION_NONE) session_start();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'RecipeNest' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,600;1,9..144,300&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/base.css">
    <?php if (!empty($extra_css)): foreach ($extra_css as $f): ?>
        <link rel="stylesheet" href="../css/<?= $f ?>.css">
    <?php endforeach; endif; ?>
</head>
<body class="<?= $body_class ?? '' ?>">

<nav class="main-nav">
    <a href="../php/index.php" class="nav-logo">
        <span class="logo-icon">🍽</span>
        <span class="logo-text">RecipeNest</span>
    </a>

    <div class="nav-links">
        <a href="../php/index.php" class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>">Browse</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="../php/dashboard.php" class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
            <a href="../php/add.php" class="nav-btn">+ Add Recipe</a>
            <div class="nav-avatar" onclick="toggleDropdown()" title="<?= htmlspecialchars($_SESSION['username']) ?>">
                <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
                <div class="nav-dropdown" id="navDropdown">
                    <span class="dropdown-user">Signed in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
                    <a href="../php/dashboard.php">My Dashboard</a>
                    <a href="../php/logout.php" class="dropdown-logout">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a href="../php/login.php" class="nav-link <?= $current_page === 'login.php' ? 'active' : '' ?>">Login</a>
            <a href="../php/signup.php" class="nav-btn">Sign Up</a>
        <?php endif; ?>
    </div>

    <button class="mobile-menu-btn" onclick="toggleMobileMenu()" aria-label="Menu">
        <span></span><span></span><span></span>
    </button>
</nav>

<div class="mobile-menu" id="mobileMenu">
    <a href="../php/index.php">Browse Recipes</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="../php/dashboard.php">Dashboard</a>
        <a href="../php/add.php">Add Recipe</a>
        <a href="../php/logout.php">Logout</a>
    <?php else: ?>
        <a href="../php/login.php">Login</a>
        <a href="../php/signup.php">Sign Up</a>
    <?php endif; ?>
</div>
