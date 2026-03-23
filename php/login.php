<?php
// ============================================================
// login.php
// ============================================================
session_start();
if (isset($_SESSION['user_id'])) { header("Location: dashboard.php"); exit; }
include 'config.php';

$error = "";
if (isset($_POST['login'])) {
    $email    = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id']  = $row['id'];
            $_SESSION['username'] = $row['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Incorrect password. Please try again.";
        }
    } else {
        $error = "No account found with that email.";
    }
}

$page_title = "Login — RecipeNest";
$extra_css  = ['auth'];
include 'header.php';
?>

<div class="auth-page">
    <div class="auth-left">
        <div class="auth-brand">
            <span class="logo-icon" style="font-size:3rem">🍽</span>
            <h2>RecipeNest</h2>
            <p>Your personal recipe collection, beautifully organized.</p>
        </div>
        <div class="auth-features">
            <div class="af-item"><span>🔍</span> Browse recipes without an account</div>
            <div class="af-item"><span>✏️</span> Add and edit your own recipes</div>
            <div class="af-item"><span>⭐</span> Save favorites to your dashboard</div>
            <div class="af-item"><span>📊</span> Track your recipe collection</div>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-box">
            <h1 class="auth-title">Welcome back</h1>
            <p class="auth-sub">Login to manage your recipes</p>

            <?php if ($error): ?>
                <div class="auth-error">⚠ <?= $error ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="you@example.com"
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Your password">
                </div>
                <button type="submit" name="login" class="btn-auth">Login →</button>
            </form>

            <p class="auth-switch">Don't have an account? <a href="signup.php">Sign up free</a></p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
