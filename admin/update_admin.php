<?php
session_start();
require_once '../includes/db.php';

// Redirect if not logged in as admin
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit;
}

$currentEmail = '';
$error = '';

// Get current admin email
try {
    $stmt = $pdo->prepare("SELECT email FROM admins WHERE id = 1 LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($admin) {
        $currentEmail = $admin['email'];
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif (!empty($password) && strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $password_confirm) {
        $error = "Passwords do not match.";
    } else {
        try {
            if (!empty($password)) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admins SET email = :email, password = :password WHERE id = 1");
                $updated = $stmt->execute(['email' => $email, 'password' => $hashed]);
            } else {
                $stmt = $pdo->prepare("UPDATE admins SET email = :email WHERE id = 1");
                $updated = $stmt->execute(['email' => $email]);
            }

            if ($updated) {
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Failed to update admin details.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Update Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(135deg, #007BFF, #00C4FF);
      font-family: 'Inter', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
    }

    .form-container {
      background: #fff;
      padding: 2.5rem 2rem;
      border-radius: 12px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.15);
      color: #333;
    }

    h1 {
      text-align: center;
      margin-bottom: 1.5rem;
      font-size: 1.6rem;
      color: #007BFF;
    }

    label {
      display: block;
      margin-bottom: 0.35rem;
      font-weight: 600;
    }

    .input-group {
      position: relative;
      margin-bottom: 1.3rem;
    }

    .input-group input {
      width: 100%;
      padding: 0.75rem 2.5rem 0.75rem 0.75rem;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .input-group input:focus {
      outline: none;
      border-color: #007BFF;
      box-shadow: 0 0 0 3px rgba(0,123,255,0.2);
    }

    .toggle-visibility {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      font-size: 1.1rem;
      color: #555;
      transition: color 0.2s;
    }

    .toggle-visibility:hover {
      color: #007BFF;
    }

    button[type="submit"] {
      width: 100%;
      padding: 0.8rem;
      background: #007BFF;
      color: #fff;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 1rem;
      font-weight: 600;
      transition: background 0.2s, transform 0.1s;
    }

    button[type="submit"]:hover {
      background: #0056b3;
    }

    button[type="submit"]:active {
      transform: translateY(1px);
    }

    .error {
      background: #FFE2E2;
      border: 1px solid #FF6B6B;
      color: #B00020;
      padding: 0.75rem 1rem;
      border-radius: 6px;
      margin-bottom: 1rem;
      text-align: center;
      font-size: 0.95rem;
    }
  </style>
</head>
<body>

<div class="form-container">
  <h1>Update Admin</h1>

  <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <label for="email">Email</label>
    <div class="input-group">
      <input type="email" id="email" name="email" required value="<?= htmlspecialchars($currentEmail) ?>">
    </div>

    <label for="password">New Password</label>
    <div class="input-group">
      <input type="password" id="password" name="password">
      <button type="button" class="toggle-visibility" onclick="togglePassword('password', this)">👁️</button>
    </div>

    <label for="password_confirm">Confirm Password</label>
    <div class="input-group">
      <input type="password" id="password_confirm" name="password_confirm">
      <button type="button" class="toggle-visibility" onclick="togglePassword('password_confirm', this)">👁️</button>
    </div>

    <button type="submit">Update Details</button>
  </form>
</div>

<script>
  function togglePassword(fieldId, toggleBtn) {
    const input = document.getElementById(fieldId);
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    toggleBtn.textContent = isPassword ? '🙈' : '👁️';
  }
</script>

</body>
</html>
