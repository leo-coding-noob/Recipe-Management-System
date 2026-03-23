
<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <span class="logo-icon">🍽</span>
            <span class="logo-text">RecipeNest</span>
            <p>Cook, share, and discover recipes from around the world.</p>
        </div>
        <div class="footer-links-col">
            <h4>Explore</h4>
            <a href="../php/index.php">All Recipes</a>
            <a href="../php/index.php?category=Breakfast">Breakfast</a>
            <a href="../php/index.php?category=Dinner">Dinner</a>
            <a href="../php/index.php?category=Dessert">Dessert</a>
        </div>
        <div class="footer-links-col">
            <h4>Account</h4>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../php/dashboard.php">Dashboard</a>
                <a href="../php/add.php">Add Recipe</a>
                <a href="../php/logout.php">Logout</a>
            <?php else: ?>
                <a href="../php/login.php">Login</a>
                <a href="../php/signup.php">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 RecipeNest &mdash; BCA 4th Semester Project</p>
    </div>
</footer>

<script src="../js/script.js"></script>
</body>
</html>
