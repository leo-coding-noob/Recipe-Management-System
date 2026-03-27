<?php
session_start();
include 'config.php';

// 🚫 Block non-admin users
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit;
}

// 🗑 Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM recipes WHERE id = $id");
    header("Location: admin.php?deleted=1");
    exit;
}

// 📊 Get all recipes
$recipes = $conn->query("SELECT r.*, u.username 
                         FROM recipes r 
                         JOIN users u ON r.user_id = u.id 
                         ORDER BY r.created_at DESC");

$page_title = "Admin Panel — RecipeNest";
$extra_css = ['admin'];
include 'header.php';
?>

<div class="admin-page">
    <h1> Admin Panel</h1>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert-success">✅ Recipe deleted successfully</div>
    <?php endif; ?>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $recipes->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td>
                    <?php if(!empty($row['image'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="" class="recipe-thumb">
                    <?php endif; ?>
                    <?= htmlspecialchars($row['title']) ?>
                </td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
                <td>
                    <a href="view.php?id=<?= $row['id'] ?>" class="btn btn-view">View</a>
                    <a href="admin.php?delete=<?= $row['id'] ?>" 
                       class="btn btn-delete"
                       onclick="return confirm('Delete this recipe?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>