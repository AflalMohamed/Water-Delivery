<?php
// admin_contact_settings.php

include '../includes/db.php';

$message = '';

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $settings = [
    'whatsapp_number' => trim($_POST['whatsapp_number']),
    'phone_number'    => trim($_POST['phone_number']),
    'email_address'   => trim($_POST['email_address']),
    'company_address' => trim($_POST['company_address']),
  ];

  $stmt = $pdo->prepare("
    INSERT INTO settings (`key`, `value`) VALUES (:key, :value)
    ON DUPLICATE KEY UPDATE `value` = :value
  ");

  foreach ($settings as $key => $value) {
    if (!empty($value)) {
      $stmt->execute(['key' => $key, 'value' => $value]);
    }
  }

  $message = "✅ Settings updated successfully!";
}

// Fetch current values
$stmt = $pdo->query("SELECT `key`, `value` FROM settings");
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin - Contact Settings</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

    :root {
      --blue-dark: #054a91;
      --blue-primary: #0d6efd; /* Bootstrap default primary */
      --blue-light: #cce5ff;
      --white: #fff;
      --shadow: rgba(13, 110, 253, 0.3);
    }

    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #1e40af, #3b82f6);
      font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: var(--blue-dark);
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem;
      margin: 0;
    }

    .card {
      background: var(--white);
      border-radius: 1.25rem;
      box-shadow: 0 15px 30px var(--shadow);
      max-width: 480px;
      width: 100%;
      padding: 2.5rem 2rem;
      transition: box-shadow 0.3s ease;
    }

    .card:hover {
      box-shadow: 0 20px 40px rgba(13, 110, 253, 0.5);
    }

    .logo {
      font-size: 3.5rem;
      color: var(--blue-primary);
      text-align: center;
      margin-bottom: 0.5rem;
      user-select: none;
    }

    .card-title {
      font-weight: 700;
      font-size: 1.75rem;
      color: var(--blue-dark);
      text-align: center;
      margin-bottom: 0.25rem;
    }

    .card-subtitle {
      color: var(--blue-primary);
      text-align: center;
      margin-bottom: 2rem;
      font-weight: 600;
      letter-spacing: 0.03em;
    }

    .form-label {
      font-weight: 600;
      color: var(--blue-dark);
    }

    input.form-control,
    textarea.form-control {
      border-radius: 0.5rem;
      border: 1.5px solid var(--blue-light);
      padding: 0.5rem 1rem;
      font-size: 1rem;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    input.form-control:focus,
    textarea.form-control:focus {
      border-color: var(--blue-primary);
      box-shadow: 0 0 6px var(--blue-primary);
      outline: none;
    }

    .btn-water {
      background: linear-gradient(135deg, #3b82f6, #1e40af);
      color: var(--white);
      font-weight: 600;
      border-radius: 0.75rem;
      padding: 0.6rem 1rem;
      font-size: 1.1rem;
      border: none;
      width: 100%;
      transition: background 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
      user-select: none;
    }

    .btn-water:hover,
    .btn-water:focus {
      background: linear-gradient(135deg, #1e40af, #1e3a8a);
      box-shadow: 0 8px 24px rgba(30, 64, 175, 0.6);
      transform: translateY(-3px);
      outline: none;
    }

    .btn-outline-secondary {
      border-radius: 0.75rem;
      padding: 0.6rem 1rem;
      font-weight: 600;
      font-size: 1rem;
      width: 100%;
      margin-top: 0.5rem;
      color: var(--blue-primary);
      border-color: var(--blue-primary);
      transition: background-color 0.3s ease, color 0.3s ease, transform 0.3s ease;
      user-select: none;
    }

    .btn-outline-secondary:hover,
    .btn-outline-secondary:focus {
      background-color: var(--blue-primary);
      color: var(--white);
      transform: translateY(-2px);
      outline: none;
    }

    .alert-success {
      font-weight: 600;
      font-size: 1rem;
      border-radius: 0.5rem;
      padding: 1rem 1.25rem;
      margin-bottom: 1.5rem;
      text-align: center;
      box-shadow: 0 4px 20px rgba(13, 110, 253, 0.25);
    }

    /* Responsive */
    @media (max-width: 576px) {
      .card {
        padding: 2rem 1.5rem;
      }
    }
  </style>
</head>
<body>

  <div class="card" role="main" aria-label="Admin Contact Settings">
    <div class="logo" aria-hidden="true">💧</div>
    <h1 class="card-title">Contact Settings</h1>
    <p class="card-subtitle">Keep your contact info up to date</p>

    <?php if ($message): ?>
      <div class="alert alert-success" role="alert" aria-live="polite" aria-atomic="true">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <form method="post" novalidate>
      <div class="mb-4">
        <label for="whatsapp_number" class="form-label">WhatsApp Number</label>
        <input
          type="text"
          id="whatsapp_number"
          name="whatsapp_number"
          class="form-control"
          placeholder="+91 98765 43210"
          value="<?= htmlspecialchars($settings['whatsapp_number'] ?? '') ?>"
          autocomplete="tel"
        />
      </div>

      <div class="mb-4">
        <label for="phone_number" class="form-label">Phone Number</label>
        <input
          type="text"
          id="phone_number"
          name="phone_number"
          class="form-control"
          placeholder="+1 234 567 890"
          value="<?= htmlspecialchars($settings['phone_number'] ?? '') ?>"
          autocomplete="tel"
        />
      </div>

      <div class="mb-4">
        <label for="email_address" class="form-label">Email Address</label>
        <input
          type="email"
          id="email_address"
          name="email_address"
          class="form-control"
          placeholder="contact@waterservice.com"
          value="<?= htmlspecialchars($settings['email_address'] ?? '') ?>"
          autocomplete="email"
        />
      </div>

      <div class="mb-4">
        <label for="company_address" class="form-label">Company Address</label>
        <textarea
          id="company_address"
          name="company_address"
          rows="3"
          class="form-control"
          placeholder="123 Water Street, City"
        ><?= htmlspecialchars($settings['company_address'] ?? '') ?></textarea>
      </div>

      <button type="submit" class="btn btn-water" aria-label="Save Settings">💾 Save Settings</button>
      <a href="dashboard.php" class="btn btn-outline-secondary mt-3" aria-label="Back to Dashboard">← Back to Dashboard</a>
    </form>
  </div>

  <!-- Bootstrap Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
