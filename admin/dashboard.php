<?php
include '../includes/auth.php';
include '../includes/db.php';

// Fetch counts
$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orderCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$serviceCount = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
$testimonialCount = $pdo->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();

// Fetch WhatsApp number
$stmt = $pdo->prepare("SELECT `value` FROM settings WHERE `key` = 'whatsapp_number'");
$stmt->execute();
$whatsappNumber = $stmt->fetchColumn() ?: 'Not Set';

include 'header.php';
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

  /* Reset & Base */
  * {
    box-sizing: border-box;
  }

  body {
    margin: 0;
    font-family: 'Inter', sans-serif;
    background-color: #e7f0ff;
    color: #1e293b;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

  .dashboard-wrapper {
    display: flex;
    min-height: 100vh;
    background-color: #e7f0ff;
  }

  /* Sidebar */
  .sidebar {
    width: 260px;
    background: linear-gradient(180deg, #2563eb 0%, #1e40af 100%);
    color: #dbeafe;
    display: flex;
    flex-direction: column;
    padding: 2.5rem 1.5rem;
    box-shadow: 2px 0 8px rgb(0 0 0 / 0.1);
  }

  .sidebar h2 {
    font-weight: 700;
    font-size: 1.9rem;
    margin-bottom: 3rem;
    letter-spacing: 0.1em;
    text-align: center;
    text-transform: uppercase;
    user-select: none;
  }

  .sidebar nav a {
    color: #dbeafe;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 1rem;
    font-weight: 600;
    padding: 0.9rem 1.2rem;
    margin-bottom: 1.25rem;
    border-radius: 8px;
    transition: background-color 0.3s ease, color 0.3s ease;
    font-size: 1rem;
  }

  .sidebar nav a:hover,
  .sidebar nav a.active {
    background-color: #1e40af;
    color: #f3f4f6;
    box-shadow: 0 0 10px rgb(37 99 235 / 0.6);
  }

  .sidebar nav a .icon {
    font-size: 1.4rem;
    user-select: none;
  }

  /* Main Content */
  main.main-content {
    flex-grow: 1;
    padding: 3rem 4rem;
    background-color: #f9fafc;
    display: flex;
    flex-direction: column;
  }

  header.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 3rem;
  }

  header.dashboard-header h1 {
    font-weight: 700;
    font-size: 2.4rem;
    color: #1e293b;
    letter-spacing: 0.05em;
  }

  .btn-logout {
    background-color: #ef4444;
    border: none;
    padding: 0.65rem 1.8rem;
    color: white;
    font-weight: 700;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    box-shadow: 0 4px 12px rgb(239 68 68 / 0.3);
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
  }

  .btn-logout:hover {
    background-color: #b91c1c;
    box-shadow: 0 6px 18px rgb(185 28 28 / 0.5);
  }

  /* Dashboard Cards Grid */
  .dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2.5rem;
  }

  .card {
    background-color: white;
    border-radius: 14px;
    padding: 2rem 2.5rem;
    box-shadow: 0 6px 15px rgb(37 99 235 / 0.12);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 25px rgb(37 99 235 / 0.25);
  }

  .card h2 {
    font-weight: 700;
    font-size: 1.2rem;
    margin-bottom: 1rem;
    letter-spacing: 0.05em;
    color: #1e293b;
    text-transform: uppercase;
    user-select: none;
  }

  .card .number {
    font-size: 3rem;
    font-weight: 800;
    color: #2563eb;
    user-select: text;
  }

  .card a {
    margin-top: 1.5rem;
    font-weight: 700;
    font-size: 1rem;
    color: #2563eb;
    text-decoration: none;
    align-self: flex-start;
    transition: color 0.3s ease;
  }

  .card a:hover {
    text-decoration: underline;
    color: #1e40af;
  }

  /* Special WhatsApp Card */
  .card.whatsapp {
    background: #2563eb;
    color: #e0e7ff;
    box-shadow: 0 8px 22px rgb(37 99 235 / 0.4);
  }

  .card.whatsapp h2,
  .card.whatsapp a {
    color: #dbeafe;
  }

  .card.whatsapp a:hover {
    color: #f0f9ff;
  }

  .card.whatsapp .number {
    font-size: 2.4rem;
    user-select: text;
  }

  /* Responsive */
  @media (max-width: 720px) {
    main.main-content {
      padding: 2rem 2rem;
    }

    .dashboard-grid {
      grid-template-columns: 1fr;
      gap: 1.75rem;
    }

    .sidebar {
      width: 100%;
      flex-direction: row;
      padding: 1rem 1rem;
      overflow-x: auto;
    }

    .sidebar h2 {
      flex: 0 0 auto;
      margin: 0 2rem 0 0;
      font-size: 1.4rem;
      text-align: left;
    }

    .sidebar nav {
      flex: 1 1 auto;
      display: flex;
      gap: 1rem;
      overflow-x: auto;
      align-items: center;
    }

    .sidebar nav a {
      flex-shrink: 0;
      padding: 0.55rem 1rem;
      margin: 0;
      white-space: nowrap;
      font-size: 0.9rem;
    }
  }
</style>

<div class="dashboard-wrapper" role="main" aria-label="Admin Dashboard">


  <aside class="sidebar" aria-label="Main Navigation">
    <h2>Water Delivery</h2>
    <nav>
      <a href="products.php" class="<?= basename($_SERVER['PHP_SELF']) === 'products.php' ? 'active' : '' ?>"><span class="icon" aria-hidden="true">📦</span> Products</a>
      <a href="order.php" class="<?= basename($_SERVER['PHP_SELF']) === 'order.php' ? 'active' : '' ?>"><span class="icon" aria-hidden="true">🛒</span> Orders</a>
      <a href="manage_services.php" class="<?= basename($_SERVER['PHP_SELF']) === 'manage_services.php' ? 'active' : '' ?>"><span class="icon" aria-hidden="true">🔧</span> Services</a>
      <a href="admin_testimonials.php" class="<?= basename($_SERVER['PHP_SELF']) === 'admin_testimonials.php' ? 'active' : '' ?>"><span class="icon" aria-hidden="true">💬</span> Testimonials</a>
      <a href="whatsapp.php" class="<?= basename($_SERVER['PHP_SELF']) === 'whatsapp.php' ? 'active' : '' ?>"><span class="icon" aria-hidden="true">📞</span> Contact Us</a>
      <a href="about_settings.php" class="<?= basename($_SERVER['PHP_SELF']) === 'about_settings.php' ? 'active' : '' ?>"><span class="icon" aria-hidden="true">⚙️</span> About Us</a>
    </nav>
  </aside>

  <main class="main-content">
   <header class="dashboard-header">
  <h1>Dashboard Overview</h1>
  <div style="display: flex; gap: 1rem; align-items: center;">
    <!-- Manage User Button -->
    <a href="update_admin.php" 
       class="btn-manage-user" 
       aria-label="Manage Users"
       title="Manage Users"
       style="display: inline-flex; align-items: center; gap: 0.5rem; background-color: #2563eb; color: white; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 1rem; box-shadow: 0 4px 12px rgb(37 99 235 / 0.4); transition: background-color 0.3s ease;">
      <span aria-hidden="true" style="font-size: 1.2rem;">👤</span> Manage Users
    </a>

    <!-- Logout Button -->
    <form method="POST" action="logout.php" style="margin:0;">
      <button type="submit" class="btn-logout" aria-label="Logout">Logout</button>
    </form>
  </div>
</header>


    <section class="dashboard-grid" aria-label="Statistics Overview">
      <article class="card" tabindex="0" aria-label="Number of Products">
        <h2>Products</h2>
        <p class="number" aria-live="polite"><?= $productCount ?></p>
        <a href="products.php" aria-label="Manage Products">Manage</a>
      </article>

      <article class="card" tabindex="0" aria-label="Number of Orders">
        <h2>Orders</h2>
        <p class="number" aria-live="polite"><?= $orderCount ?></p>
        <a href="order.php" aria-label="Manage Orders">Manage</a>
      </article>

      <article class="card" tabindex="0" aria-label="Number of Services">
        <h2>Services</h2>
        <p class="number" aria-live="polite"><?= $serviceCount ?></p>
        <a href="manage_services.php" aria-label="Manage Services">Manage</a>
      </article>

      <article class="card" tabindex="0" aria-label="Number of Testimonials">
        <h2>Testimonials</h2>
        <p class="number" aria-live="polite"><?= $testimonialCount ?></p>
        <a href="admin_testimonials.php" aria-label="Manage Testimonials">Manage</a>
      </article>

      <article class="card whatsapp" tabindex="0" aria-label="WhatsApp Contact Number">
        <h2>Contact Us</h2>
        <p class="number" aria-live="polite"><?= htmlspecialchars($whatsappNumber) ?></p>
        <a href="whatsapp.php" aria-label="Manage WhatsApp Number">Manage</a>
      </article>

      <article class="card" tabindex="0" aria-label="About Us Settings">
        <h2>About Us</h2>
        <p class="number" aria-live="polite"></p>
        <a href="about_settings.php" aria-label="Open About Us Control">Manage</a>
      </article>
    </section>
  </main>
</div>

<?php include '../includes/footer.php'; ?>
