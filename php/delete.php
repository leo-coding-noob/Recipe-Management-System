<?php
// ============================================================
// delete.php — Protected: only author can delete
// ============================================================
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
include 'config.php';

$uid = $_SESSION['user_id'];
$id  = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) { header("Location: dashboard.php"); exit; }

$result = $conn->query("SELECT * FROM recipes WHERE id=$id AND user_id=$uid");
if ($result->num_rows === 0) {
    header("Location: index.php");
    exit;
}
$recipe = $result->fetch_assoc();

// Delete image file
if (!empty($recipe['image']) && file_exists("../uploads/" . $recipe['image'])) {
    @unlink("../uploads/" . $recipe['image']);
}

$title = $conn->real_escape_string($recipe['title']);
$conn->query("DELETE FROM recipes WHERE id=$id AND user_id=$uid");
logActivity($conn, $uid, 'deleted', $title);

header("Location: dashboard.php?deleted=1");
exit;
?>
