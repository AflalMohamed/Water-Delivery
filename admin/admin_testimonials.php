<?php
include 'header.php';
include '../includes/db.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin_testimonials.php");
    exit;
}

// Fetch all testimonials
$stmt = $pdo->query("SELECT id, name, content, photo, status, created_at FROM testimonials ORDER BY created_at DESC");
$testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Escape helper
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Testimonials</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h1>Manage Testimonials</h1>

    <a href="add_testimonial.php" class="btn btn-primary mb-3">Add New Testimonial</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Content</th>
                <th>Photo</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($testimonials): ?>
            <?php foreach ($testimonials as $t): ?>
                <tr>
                    <td><?= (int)$t['id'] ?></td>
                    <td><?= e($t['name']) ?></td>
                    <td><?= e($t['content']) ?></td>
                    <td>
                        <?php if (!empty($t['photo'])): ?>
                            <img src="../uploads/<?= e($t['photo']) ?>" alt="Photo" width="80">
                        <?php else: ?>
                            No photo
                        <?php endif; ?>
                    </td>
                    <td><?= e($t['status']) ?></td>
                    <td><?= e($t['created_at']) ?></td>
                    <td>
                        <a href="admin_edit_testimonial.php?id=<?= (int)$t['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="admin_testimonials.php?delete=<?= (int)$t['id'] ?>" 
                           onclick="return confirm('Are you sure you want to delete this testimonial?')"
                           class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7" class="text-center">No testimonials found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php include 'footer.php'; ?>
