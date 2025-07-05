<?php
// admin/manage_services.php

include '../includes/db.php';
include 'header.php';

$error = '';
$success = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_services.php");
    exit;
}

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $icon_url = trim($_POST['icon_url']);

    if ($title && $description && $icon_url) {
        $stmt = $pdo->prepare("INSERT INTO services (title, description, icon_url) VALUES (?, ?, ?)");
        $stmt->execute([$title, $description, $icon_url]);
        $success = "Service added successfully.";
    } else {
        $error = "All fields are required.";
    }
}

// Handle Edit (Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = (int)$_POST['service_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $icon_url = trim($_POST['icon_url']);

    if ($id && $title && $description && $icon_url) {
        $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, icon_url = ? WHERE id = ?");
        $stmt->execute([$title, $description, $icon_url, $id]);
        $success = "Service updated successfully.";
    } else {
        $error = "All fields are required for update.";
    }
}

// Fetch Services
$stmt = $pdo->query("SELECT * FROM services ORDER BY id DESC");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Services</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      min-height: 100vh;
      background: #f0f5ff;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding: 2rem 3rem;
    }
    h2 {
      color: #0d6efd;
      font-weight: 700;
      margin-bottom: 2rem;
      text-align: center;
    }
    .container-fluid {
      max-width: 1400px;
      margin: 0 auto;
    }
    .row-gap {
      gap: 2rem;
    }
    form {
      background: #ffffff;
      padding: 2rem 2.5rem;
      border-radius: 1rem;
      box-shadow: 0 8px 30px rgb(13 110 253 / 0.12);
      height: 100%;
    }
    .form-label {
      font-weight: 600;
      color: #1e3a8a;
    }
    .btn-primary {
      background: linear-gradient(135deg, #3b82f6, #1e40af);
      border: none;
      font-weight: 700;
      padding: 0.75rem 1.25rem;
      border-radius: 0.6rem;
      width: 100%;
      transition: background 0.3s ease;
    }
    .btn-primary:hover, .btn-primary:focus {
      background: linear-gradient(135deg, #1e40af, #1e3a8a);
      outline: none;
    }
    table {
      background: #ffffff;
      border-radius: 1rem;
      box-shadow: 0 8px 30px rgb(13 110 253 / 0.1);
      overflow: hidden;
      width: 100%;
    }
    thead {
      background-color: #0d6efd;
      color: #fff;
      font-weight: 600;
    }
    tbody tr:hover {
      background-color: #dbe9ff;
      transition: background-color 0.3s ease;
    }
    td, th {
      vertical-align: middle !important;
      padding: 1rem 1.5rem;
    }
    .alert {
      max-width: 100%;
      border-radius: 0.6rem;
      font-weight: 600;
    }
    .note {
      font-size: 0.95rem;
      color: #1e40af;
      background-color: #dbe9ff;
      border-left: 5px solid #0d6efd;
      padding: 0.75rem 1rem;
      border-radius: 0.3rem;
      font-style: italic;
      text-align: center;
      margin-top: 1.5rem;
      user-select: none;
    }
    lord-icon {
      display: inline-block;
      vertical-align: middle;
      width: 55px;
      height: 55px;
    }
    @media (min-width: 992px) {
      .content-wrapper {
        display: flex;
        gap: 3rem;
      }
      form {
        flex: 1 1 350px;
      }
      .table-wrapper {
        flex: 2 1 700px;
        overflow-x: auto;
      }
    }
    @media (max-width: 991.98px) {
      .content-wrapper {
        display: block;
      }
      .table-wrapper {
        margin-top: 2rem;
      }
    }
  </style>
</head>
<body>

<div class="container-fluid">
  <h2>Manage Services</h2>

  <?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($error) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php elseif ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($success) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="content-wrapper">
    <!-- Add Service Form -->
    <form method="POST" novalidate>
      <input type="hidden" name="action" value="add" />
      <div class="mb-4">
        <label for="title" class="form-label">Service Title</label>
        <input type="text" name="title" id="title" class="form-control" required placeholder="Enter service title" />
      </div>
      <div class="mb-4">
        <label for="icon_url" class="form-label">Icon URL (Lordicon)</label>
        <input type="url" name="icon_url" id="icon_url" class="form-control" required placeholder="https://cdn.lordicon.com/xxxxxx.json" />
      </div>
      <div class="mb-4">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" class="form-control" rows="5" required placeholder="Brief description of the service"></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Add Service</button>
      <div class="note">
        Note: Don’t change icons if you don’t know where you get the URL for icons.
      </div>
    </form>

    <!-- Services Table -->
    <div class="table-wrapper">
      <table class="table table-hover table-bordered align-middle">
        <thead>
          <tr>
            <th scope="col" style="width: 70px;">Icon Preview</th>
            <th scope="col" style="min-width: 180px;">Title</th>
            <th scope="col" style="min-width: 350px;">Description</th>
            <th scope="col" style="width: 150px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($services): ?>
            <?php foreach ($services as $service): ?>
              <tr>
                <td>
                  <lord-icon
                    src="<?= htmlspecialchars($service['icon_url']) ?>"
                    trigger="hover"
                    aria-label="<?= htmlspecialchars($service['title']) ?> icon"
                  ></lord-icon>
                </td>
                <td><?= htmlspecialchars($service['title']) ?></td>
                <td><?= htmlspecialchars($service['description']) ?></td>
                <td>
                  <button
                    class="btn btn-sm btn-info me-2"
                    data-bs-toggle="modal"
                    data-bs-target="#editServiceModal"
                    data-id="<?= $service['id'] ?>"
                    data-title="<?= htmlspecialchars($service['title'], ENT_QUOTES) ?>"
                    data-icon="<?= htmlspecialchars($service['icon_url'], ENT_QUOTES) ?>"
                    data-description="<?= htmlspecialchars($service['description'], ENT_QUOTES) ?>"
                    aria-label="Edit <?= htmlspecialchars($service['title']) ?> service"
                  >Edit</button>

                  <a href="?delete=<?= $service['id'] ?>"
                    class="btn btn-sm btn-danger"
                    onclick="return confirm('Are you sure you want to delete this service?');"
                    aria-label="Delete <?= htmlspecialchars($service['title']) ?> service"
                  >Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="text-center text-muted">No services found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Edit Service Modal -->
<div class="modal fade" id="editServiceModal" tabindex="-1" aria-labelledby="editServiceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" id="editServiceForm" novalidate>
        <input type="hidden" name="action" value="edit" />
        <input type="hidden" name="service_id" id="editServiceId" />
        <div class="modal-header">
          <h5 class="modal-title" id="editServiceModalLabel">Edit Service</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="editTitle" class="form-label">Service Title</label>
            <input type="text" name="title" id="editTitle" class="form-control" required />
          </div>
          <div class="mb-3">
            <label for="editIconUrl" class="form-label">Icon URL (Lordicon)</label>
            <input type="url" name="icon_url" id="editIconUrl" class="form-control" required />
          </div>
          <div class="mb-3">
            <label for="editDescription" class="form-label">Description</label>
            <textarea name="description" id="editDescription" class="form-control" rows="4" required></textarea>
          </div>
          <div class="note">
            Note: Don’t change icons if you don’t know where you get the URL for icons.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.lordicon.com/lordicon.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const editModal = document.getElementById('editServiceModal');
  editModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget; // Button that triggered the modal
    const id = button.getAttribute('data-id');
    const title = button.getAttribute('data-title');
    const icon = button.getAttribute('data-icon');
    const description = button.getAttribute('data-description');

    // Update modal fields
    document.getElementById('editServiceId').value = id;
    document.getElementById('editTitle').value = title;
    document.getElementById('editIconUrl').value = icon;
    document.getElementById('editDescription').value = description;
  });
</script>
</body>
</html>
