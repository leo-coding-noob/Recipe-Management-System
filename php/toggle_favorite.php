<?php
// ============================================================
// toggle_favorite.php — AJAX endpoint
// ============================================================
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success'=>false,'message'=>'Login required','redirect'=>'login.php']);
    exit;
}
include 'config.php';

$uid       = $_SESSION['user_id'];
$recipe_id = intval($_POST['recipe_id'] ?? 0);
if (!$recipe_id) { echo json_encode(['success'=>false,'message'=>'Invalid recipe']); exit; }

// Check if already favorited
$check = $conn->query("SELECT id FROM favorites WHERE user_id=$uid AND recipe_id=$recipe_id");
if ($check->num_rows > 0) {
    $conn->query("DELETE FROM favorites WHERE user_id=$uid AND recipe_id=$recipe_id");
    echo json_encode(['success'=>true,'action'=>'removed','message'=>'Removed from favorites']);
} else {
    $conn->query("INSERT INTO favorites (user_id, recipe_id) VALUES ($uid, $recipe_id)");
    echo json_encode(['success'=>true,'action'=>'added','message'=>'Added to favorites!']);
}
?>
