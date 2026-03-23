<?php
// ============================================================
// config.php — Database connection
// ============================================================
$host = "127.0.0.1";
$user = "root";
$pass = "";
$port = 3307;       // Change to 3306 if needed
$db = "food_db";

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Helper: log activity
function logActivity($conn, $user_id, $action, $recipe_title) {
    $action = $conn->real_escape_string($action);
    $recipe_title = $conn->real_escape_string($recipe_title);
    $conn->query("INSERT INTO activity_log (user_id, action, recipe_title) VALUES ($user_id, '$action', '$recipe_title')");
}
?>
