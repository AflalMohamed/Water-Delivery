<?php
include '../includes/auth.php';
include '../includes/db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: admin_testimonials.php');
    exit;
}

$id = (int)$_GET['id'];

// Fetch existing testimonial
$stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
$stmt->execute([$id]);
$testimonial = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$testimonial) {
    header('Location: admin_testimonials.php');
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status = $_POST['status'] ?? 'active'; // default to active

    if ($name === '') {
        $errors[] = "Name is required.";
    }
    if ($content === '') {
        $errors[] = "Content is required.";
    }

    // Handle image upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['photo']['type'], $allowedTypes)) {
            $errors[] = "Only JPG, PNG, and GIF images are allowed.";
        } else {
            $uploadDir = '../uploads/';
            $filename = uniqid() . '_' . basename($_FILES['photo']['name']);
            $targetFile = $uploadDir . $filename;
            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                $errors[] = "Failed to upload image.";
            } else {
                // Delete old photo if exists
                if (!empty($testimonial['photo']) && file_exists($uploadDir . $testimonial['photo'])) {
                    unlink($uploadDir . $testimonial['photo']);
                }
                $testimonial['photo'] = $filename;
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE testimonials SET name = ?, content = ?, status = ?, photo = ? WHERE id = ?");
        $stmt->execute([
            $name,
            $content,
            $status,
            $testimonial['photo'] ?? null,
            $id
        ]);
        $success = "Testimonial updated successfully.";
        // Reload updated testimonial
        $stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
        $stmt->execute([$id]);
        $testimonial = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

include '../includes/header.php';
?>

<div class="container py-5">
  <h1>Edit Testimonial</h1>

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <ul>
        <?php foreach ($errors as $error): ?>
          <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form action="" method="POST" enctype="multipart/form-data" class="mt-4">
    <div class="mb-3">
      <label for="name" class="form-label">Name</label>
      <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($testimonial['name'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
      <label for="content" class="form-label">Content</label>
      <textarea id="content" name="content" class="form-control" rows="5" required><?= htmlspecialchars($testimonial['content'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label for="status" class="form-label">Status</label>
      <select id="status" name="status" class="form-select" required>
        <option value="active" <?= ($testimonial['status'] === 'active') ? 'selected' : '' ?>>Active</option>
        <option value="inactive" <?= ($testimonial['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Current Photo</label><br>
      <?php if (!empty($testimonial['photo']) && file_exists('../uploads/' . $testimonial['photo'])): ?>
        <img src="../uploads/<?= htmlspecialchars($testimonial['photo']) ?>" alt="Current Photo" width="150">
      <?php else: ?>
        <p>No photo uploaded.</p>
      <?php endif; ?>
    </div>

    <div class="mb-3">
      <label for="photo" class="form-label">Change Photo (optional)</label>
      <input type="file" id="photo" name="photo" class="form-control" accept="image/*">
    </div>

    <button type="submit" class="btn btn-primary">Update Testimonial</button>
    <a href="admin_testimonials.php" class="btn btn-secondary">Back to List</a>
  </form>
</div>

<?php include '../includes/footer.php'; ?>
