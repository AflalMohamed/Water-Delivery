<?php
include '../includes/auth.php';
include '../includes/db.php';

// Fetch current about data from `settings` table
$stmt = $pdo->prepare("SELECT `key`, `value` FROM settings WHERE `key` IN ('about_title', 'about_subtitle', 'about_description', 'founder_name', 'founder_role', 'founder_bio', 'founder_photo')");
$stmt->execute();
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update text settings
    $keys = ['about_title', 'about_subtitle', 'about_description', 'founder_name', 'founder_role', 'founder_bio'];
    foreach ($keys as $key) {
        $value = trim($_POST[$key] ?? '');
        $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?");
        $stmt->execute([$key, $value, $value]);
    }

    // Handle founder photo upload
    if (isset($_FILES['founder_photo']) && $_FILES['founder_photo']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['founder_photo']['tmp_name'];
        $fileName = basename($_FILES['founder_photo']['name']);
        $targetDir = '../uploads/';
        $targetFile = $targetDir . $fileName;

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (in_array($fileExt, $allowedExtensions)) {
            if (move_uploaded_file($tmpName, $targetFile)) {
                $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES ('founder_photo', ?) ON DUPLICATE KEY UPDATE `value` = ?");
                $stmt->execute([$fileName, $fileName]);
                $settings['founder_photo'] = $fileName;
            } else {
                $error = "Failed to move uploaded file.";
            }
        } else {
            $error = "Invalid file type. Allowed types: jpg, jpeg, png, gif.";
        }
    }

    if (!isset($error)) {
        header("Location: about_settings.php?success=1");
        exit;
    }
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit About Us - Admin</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f5f9fc;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    main.main-content {
      max-width: 800px;
      margin: 50px auto;
      background: #ffffff;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    h1 {
      color: #0077b6;
      font-weight: 700;
      margin-bottom: 30px;
      text-align: center;
    }

    label {
      font-weight: 600;
      margin-bottom: 6px;
      display: block;
      color: #023e8a;
    }

    input[type="text"],
    input[type="email"],
    textarea,
    input[type="file"] {
      border: 1.5px solid #90caf9;
      border-radius: 6px;
      padding: 10px 12px;
      width: 100%;
      font-size: 1rem;
      transition: border-color 0.3s;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    textarea:focus {
      border-color: #0077b6;
      outline: none;
      box-shadow: 0 0 8px rgba(0, 119, 182, 0.3);
    }

    textarea {
      resize: vertical;
    }

    h3 {
      color: #0077b6;
      margin-top: 40px;
      margin-bottom: 25px;
      font-weight: 700;
    }

    .founder-photo {
      width: 130px;
      height: 130px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 15px;
      border: 2px solid #0077b6;
      box-shadow: 0 2px 10px rgba(0, 119, 182, 0.2);
      display: block;
    }

    .btn-save {
      background-color: #0077b6;
      border: none;
      color: #fff;
      padding: 12px 30px;
      font-size: 1.1rem;
      font-weight: 600;
      border-radius: 8px;
      cursor: pointer;
      width: 100%;
      transition: background-color 0.3s ease;
      box-shadow: 0 4px 15px rgba(0, 119, 182, 0.4);
    }

    .btn-save:hover {
      background-color: #005f87;
      box-shadow: 0 6px 20px rgba(0, 95, 135, 0.6);
    }

    /* Success and error messages */
    .alert-success {
      background-color: #d1e7dd;
      border: 1px solid #badbcc;
      color: #0f5132;
      padding: 12px 20px;
      border-radius: 8px;
      margin-bottom: 25px;
      font-weight: 600;
    }

    .alert-error {
      background-color: #f8d7da;
      border: 1px solid #f5c2c7;
      color: #842029;
      padding: 12px 20px;
      border-radius: 8px;
      margin-bottom: 25px;
      font-weight: 600;
    }
  </style>
</head>
<body>

<main class="main-content">

  <h1>Edit About Us</h1>

  <?php if (isset($_GET['success'])): ?>
    <div class="alert-success">✅ About Us updated successfully!</div>
  <?php elseif (!empty($error)): ?>
    <div class="alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" novalidate>
    <div class="mb-4">
      <label for="about_title">Title</label>
      <input type="text" id="about_title" name="about_title" required value="<?= htmlspecialchars($settings['about_title'] ?? '') ?>">
    </div>

    <div class="mb-4">
      <label for="about_subtitle">Subtitle</label>
      <input type="text" id="about_subtitle" name="about_subtitle" required value="<?= htmlspecialchars($settings['about_subtitle'] ?? '') ?>">
    </div>

    <div class="mb-4">
      <label for="about_description">Description</label>
      <textarea id="about_description" name="about_description" rows="6"><?= htmlspecialchars($settings['about_description'] ?? '') ?></textarea>
    </div>

    <h3>Founder Info</h3>

    <div class="mb-4">
      <label for="founder_name">Name</label>
      <input type="text" id="founder_name" name="founder_name" required value="<?= htmlspecialchars($settings['founder_name'] ?? '') ?>">
    </div>

    <div class="mb-4">
      <label for="founder_role">Role</label>
      <input type="text" id="founder_role" name="founder_role" value="<?= htmlspecialchars($settings['founder_role'] ?? '') ?>">
    </div>

    <div class="mb-4">
      <label for="founder_bio">Bio</label>
      <textarea id="founder_bio" name="founder_bio" rows="4"><?= htmlspecialchars($settings['founder_bio'] ?? '') ?></textarea>
    </div>

    <div class="mb-4">
      <label>Founder Photo</label><br>
      <?php if (!empty($settings['founder_photo'])): ?>
        <img src="../uploads/<?= htmlspecialchars($settings['founder_photo']) ?>" alt="Founder Photo" class="founder-photo">
      <?php else: ?>
        <p style="color: #6c757d;">No founder photo set.</p>
      <?php endif; ?>
      <input type="file" name="founder_photo" accept="image/*" class="form-control mt-2">
    </div>

    <button type="submit" class="btn-save">Save Changes</button>
  </form>
</main>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php include '../includes/footer.php'; ?>
