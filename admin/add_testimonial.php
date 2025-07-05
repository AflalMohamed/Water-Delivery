<?php
include '../includes/auth.php';
include '../includes/db.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $content = trim($_POST['content'] ?? '');

    // Validate required fields
    if (!$name) {
        $errors[] = "Name is required.";
    }
    if (!$content) {
        $errors[] = "Content is required.";
    }

    // Handle image upload
    $photoName = null;
    if (!empty($_FILES['photo']['name'])) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['photo']['type'];
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileSize = $_FILES['photo']['size'];

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Only JPG, PNG, and GIF images are allowed.";
        } elseif ($fileSize > 10 * 1024 * 1024) { // 10MB limit
            $errors[] = "Image size should be less than 10MB.";
        } else {
            // Generate unique file name
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $photoName = uniqid('testimonial_', true) . '.' . $ext;
            $destPath = __DIR__ . '/../uploads/' . $photoName;

            if (!move_uploaded_file($fileTmpPath, $destPath)) {
                $errors[] = "Failed to upload the image.";
            }
        }
    }

    // If no errors, insert into DB
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO testimonials (name, content, photo, status, created_at) VALUES (?, ?, ?, 'active', NOW())");
        $stmt->execute([$name, $content, $photoName]);
        $success = true;
    }
}

include '../includes/header.php';
?>

<div class="container py-5">
    <h1>Add New Testimonial</h1>

    <?php if ($success): ?>
        <div class="alert alert-success">Testimonial added successfully! <a href="admin_testimonials.php">Back to list</a></div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="mt-4" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Name *</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Content *</label>
            <textarea name="content" id="content" rows="4" class="form-control" required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label for="photo" class="form-label">Photo (JPG, PNG, GIF, max 10MB)</label>
            <input type="file" name="photo" id="photo" class="form-control" accept=".jpg,.jpeg,.png,.gif">
        </div>

        <button type="submit" class="btn btn-primary">Add Testimonial</button>
        <a href="admin_testimonials.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
