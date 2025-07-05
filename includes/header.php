<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Water Delivery Service</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Google Font for a clean, modern feel -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f4f8fc;
      color: #333;
    }
    .navbar {
      background-color: #fff;
      box-shadow: 0 4px 12px rgba(0, 123, 255, 0.1);
    }
    .navbar-brand {
      font-weight: 700;
      color: #007bff;
    }
    .navbar-brand:hover {
      color: #0056b3;
    }
    .nav-link {
      font-weight: 500;
      margin-right: 1rem;
      transition: color 0.3s;
    }
    .nav-link:hover {
      color: #007bff;
    }
    .btn-primary {
      background-color: #007bff;
      border: none;
    }
    .btn-primary:hover {
      background-color: #0056b3;
    }
    footer {
      margin-top: 4rem;
      padding: 2rem 0;
      background-color: #f0f4fa;
      text-align: center;
      color: #555;
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
      <a class="navbar-brand" href="index.php">PureWater</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
          <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
          <li class="nav-item"><a class="nav-link" href="order.php">Order</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
          <li class="nav-item"><a class="btn btn-primary ms-lg-3" href="admin/login.php">Admin Login</a></li>
        </ul>
      </div>
    </div>
  </nav>

  

 
  <!-- Bootstrap 5 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
