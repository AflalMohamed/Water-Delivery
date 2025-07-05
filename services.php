<?php
include 'includes/header.php';
include 'includes/db.php'; // DB connection

// Fetch services from DB
$stmt = $pdo->query("SELECT * FROM services ORDER BY id DESC");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
  /* Page background & font */
  body {
    background-color: #f8f9fa; /* light gray background */
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 2rem 1rem;
    color: #212529; /* dark text */
  }

  main[role="main"] {
    max-width: 960px;
    margin: 0 auto;
    background: #ffffff;
    border-radius: 12px;
    padding: 3rem 2.5rem;
    box-shadow: 0 10px 30px rgba(13, 110, 253, 0.15);
  }

  h1 {
    text-align: center;
    font-size: 2.75rem;
    color: #0d6efd; /* Bootstrap primary blue */
    margin-bottom: 3rem;
    font-weight: 700;
    letter-spacing: 0.05em;
  }

  .service-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
  }

  .service-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 6px 18px rgba(13, 110, 253, 0.15);
    padding: 2rem;
    display: flex;
    gap: 1.75rem;
    align-items: flex-start;
    transition: box-shadow 0.3s ease, transform 0.3s ease;
    cursor: default;
  }

  .service-card:hover,
  .service-card:focus {
    box-shadow: 0 10px 25px rgba(13, 110, 253, 0.35);
    transform: translateY(-6px);
  }

  .service-icon {
    width: 60px;
    height: 60px;
    flex-shrink: 0;
  }

  .service-content {
    flex-grow: 1;
  }

  .service-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0d6efd; /* Bootstrap primary blue */
    margin: 0 0 0.75rem 0;
  }

  .service-description {
    font-size: 1rem;
    color: #212529;
    line-height: 1.5;
    white-space: pre-line;
  }

  /* Responsive */
  @media (max-width: 600px) {
    main[role="main"] {
      padding: 2rem 1.5rem;
    }
    .service-card {
      flex-direction: column;
      align-items: center;
      text-align: center;
    }
    .service-icon {
      margin-bottom: 1rem;
    }
  }
</style>

<main role="main" aria-label="Services Offered">
  <h1>Our Water Delivery Services</h1>

  <section class="service-list">
    <?php if ($services): ?>
      <?php foreach ($services as $service): ?>
        <article class="service-card" tabindex="0" aria-label="<?= htmlspecialchars($service['title']) ?>">
          <div class="service-icon" aria-hidden="true">
            <lord-icon
              src="<?= htmlspecialchars($service['icon_url']) ?>"
              trigger="hover"
              colors="primary:#0d6efd,secondary:#a5d8ff"
              style="width:60px;height:60px;">
            </lord-icon>
          </div>
          <div class="service-content">
            <h2 class="service-title"><?= htmlspecialchars($service['title']) ?></h2>
            <p class="service-description"><?= nl2br(htmlspecialchars($service['description'])) ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="text-align:center; color:#0d6efd;">No services found.</p>
    <?php endif; ?>
  </section>
</main>

<script src="https://cdn.lordicon.com/lusqsztk.js"></script>
<?php include 'includes/footer.php'; ?>
