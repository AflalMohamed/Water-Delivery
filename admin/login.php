<?php
session_start();
require_once '../includes/db.php'; // Adjust if needed

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email LIMIT 1");
  $stmt->execute(['email' => $username]);
  $admin = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($admin && password_verify($password, $admin['password'])) {
    $_SESSION['admin'] = true;
    header('Location: dashboard.php');
    exit;
  } else {
    $error = "Invalid email or password.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login | PureWater Delivery</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Material+Icons+Outlined" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #007bff 0%, #00c6ff 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .login-card {
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 123, 255, 0.2);
      padding: 40px 30px;
      max-width: 400px;
      width: 100%;
    }

    .login-card h2 {
      margin: 0 0 25px;
      font-size: 1.8rem;
      font-weight: 800;
      color: #007bff;
      text-align: center;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    label {
      font-weight: 600;
      margin-bottom: 6px;
      color: #333;
      font-size: 0.95rem;
    }

    .input-wrapper {
      position: relative;
      margin-bottom: 20px;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 12px 44px 12px 15px;
      border: 2px solid #007bff;
      border-radius: 8px;
      font-size: 1rem;
      transition: border-color 0.3s;
    }

    input:focus {
      outline: none;
      border-color: #004085;
    }
    
input[type="email"],
input[type="password"],
input[type="text"] {
  width: 100%;
  padding: 12px 44px 12px 15px;
  border: 2px solid #007bff;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

input:focus {
  outline: none;
  border-color: #004085;
}

    .toggle-password {
      position: absolute;
      top: 50%;
      right: 15px;
      transform: translateY(-50%);
      cursor: pointer;
      color: #007bff;
      font-size: 1.2rem;
      user-select: none;
    }

    .toggle-password:hover {
      color: #004085;
    }

    button {
      background: #007bff;
      color: #fff;
      border: none;
      padding: 14px 0;
      font-size: 1.1rem;
      font-weight: 600;
      border-radius: 50px;
      cursor: pointer;
      transition: background 0.3s, box-shadow 0.3s;
      box-shadow: 0 6px 15px rgba(0, 123, 255, 0.3);
    }

    button:hover {
      background: #004085;
      box-shadow: 0 8px 25px rgba(0, 64, 133, 0.4);
    }

    .error-message {
      margin-top: 15px;
      color: #d9534f;
      font-weight: 600;
      text-align: center;
    }

    footer {
      position: absolute;
      bottom: 15px;
      width: 100%;
      text-align: center;
      font-size: 0.85rem;
      color: #f0f8ff;
      font-weight: 500;
    }

    @media (max-height: 600px) {
      footer {
        position: static;
        margin-top: 20px;
      }
    }
  </style>
</head>
<body>
  <main class="login-card" aria-label="Admin Login">
    <h2>Admin Login</h2>
    <form method="POST" novalidate>
      <label for="username">Email</label>
      <div class="input-wrapper">
        <input
          type="email"
          id="username"
          name="username"
          placeholder="admin@example.com"
          required
          autocomplete="username"
        >
      </div>

      <label for="password">Password</label>
      <div class="input-wrapper">
        <input
          type="password"
          id="password"
          name="password"
          placeholder="••••••••"
          required
          autocomplete="current-password"
        >
        <span
          class="material-icons-outlined toggle-password"
          onclick="togglePassword()"
          title="Show/Hide Password"
          aria-label="Toggle password visibility"
        >visibility</span>
      </div>

      <button type="submit">Login</button>
    </form>

    <?php if (!empty($error)): ?>
      <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
  </main>

  <footer>&copy; <?php echo date('Y'); ?> PureWater Delivery</footer>

  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const toggleIcon = document.querySelector('.toggle-password');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.textContent = 'visibility_off';
      } else {
        passwordInput.type = 'password';
        toggleIcon.textContent = 'visibility';
      }
    }
  </script>
</body>
</html>
