<?php
// ============================================================
// signup.php
// ============================================================
session_start();
if (isset($_SESSION['user_id'])) { header("Location: dashboard.php"); exit; }
include 'config.php';

$error = "";
if (isset($_POST['signup'])) {
    $username = $conn->real_escape_string(trim($_POST['username']));
    $email    = $conn->real_escape_string(trim($_POST['email']));
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if (strlen($username) < 3) {
        $error = "Username must be at least 3 characters.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $check = $conn->query("SELECT id FROM users WHERE email='$email'");
        if ($check->num_rows > 0) {
            $error = "This email is already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $colors = ['#6c63ff','#2ec4b6','#f4a261','#e63946','#f72585'];
            $color  = $colors[array_rand($colors)];
            $sql    = "INSERT INTO users (username, email, password, avatar_color) VALUES ('$username','$email','$hashed','$color')";
            if ($conn->query($sql)) {
                $_SESSION['user_id']  = $conn->insert_id;
                $_SESSION['username'] = $username;
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}

$page_title = "Sign Up — RecipeNest";
$extra_css  = ['auth'];
include 'header.php';
?>

<div class="auth-page">
    <div class="auth-left">
        <div class="auth-brand">
            <span class="logo-icon" style="font-size:3rem">🍽</span>
            <h2>Join RecipeNest</h2>
            <p>Create your account and start building your recipe collection today.</p>
        </div>
        <div class="auth-features">
            <div class="af-item"><span>🆓</span> Completely free to join</div>
            <div class="af-item"><span>✏️</span> Add unlimited recipes</div>
            <div class="af-item"><span>⭐</span> Build your favorites list</div>
            <div class="af-item"><span>📊</span> Personal dashboard with stats</div>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-box">
            <h1 class="auth-title">Create account</h1>
            <p class="auth-sub">Free forever. No credit card needed.</p>

            <?php if ($error): ?>
                <div class="auth-error">⚠ <?= $error ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="Choose a username" minlength="3"
                           value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="you@example.com"
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="At least 6 characters" minlength="6">
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required placeholder="Repeat your password">
                </div>
                <button type="submit" name="signup" class="btn-auth">Create Account →</button>
            </form>

            <p class="auth-switch">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
