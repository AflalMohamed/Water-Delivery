<?php
include 'includes/db.php';

$stmt = $pdo->prepare("SELECT `key`, `value` FROM settings WHERE `key` IN ('about_title', 'about_subtitle', 'about_description', 'founder_name', 'founder_role', 'founder_bio', 'founder_photo')");
$stmt->execute();
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

include 'includes/header.php';
?>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<style>
  :root {
    --blue-dark: #0d6efd;      /* Bootstrap primary blue */
    --blue-primary: #0d6efd;
    --blue-light: #dbe9ff;     /* light tint of primary blue */
    --gray-dark: #1f2937;
    --gray-medium: #4b5563;
    --gray-light: #9ca3af;
    --white: #fff;
  }

  *, *::before, *::after {
    box-sizing: border-box;
  }

  body {
    margin: 0;
    font-family: 'Inter', sans-serif;
    background: var(--white);
    color: var(--gray-dark);
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

  main {
    max-width: 900px;
    margin: 4rem auto 6rem;
    padding: 0 1.5rem;
  }

  h1 {
    font-size: 3rem;
    font-weight: 700;
    color: var(--blue-dark);
    margin-bottom: 0.5rem;
    letter-spacing: -0.02em;
  }

  .subtitle {
    font-size: 1.25rem;
    font-weight: 500;
    color: var(--blue-primary);
    margin-bottom: 3rem;
  }

  .about-description {
    font-size: 1.125rem;
    color: var(--gray-medium);
    margin-bottom: 4rem;
    white-space: pre-line;
  }

  .founder-card {
    display: flex;
    background: var(--blue-light);
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(13, 110, 253, 0.15);
    gap: 2.5rem;
    align-items: center;
    flex-wrap: wrap;
  }

  .founder-photo {
    flex: 0 0 180px;
    width: 180px;
    height: 180px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--blue-primary);
    box-shadow: 0 8px 25px rgba(13, 110, 253, 0.3);
  }

  .founder-info {
    flex: 1;
    min-width: 250px;
  }

  .founder-name {
    font-size: 2rem;
    font-weight: 700;
    color: var(--blue-dark);
    margin-bottom: 0.25rem;
  }

  .founder-role {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--blue-primary);
    margin-bottom: 1.25rem;
  }

  .founder-bio {
    font-size: 1rem;
    color: var(--gray-medium);
    line-height: 1.5;
  }

  .fallback {
    color: var(--gray-light);
    font-style: italic;
    font-size: 1rem;
    text-align: center;
  }

  footer {
    text-align: center;
    font-size: 0.9rem;
    color: var(--gray-medium);
    padding: 3rem 1rem 2rem;
    border-top: 1px solid var(--blue-light);
    font-weight: 500;
  }

  @media (max-width: 600px) {
    h1 {
      font-size: 2.4rem;
    }

    .founder-card {
      flex-direction: column;
      text-align: center;
      gap: 1.5rem;
      padding: 1.5rem 1rem;
    }

    .founder-photo {
      width: 140px;
      height: 140px;
      margin: 0 auto;
    }

    .founder-info {
      min-width: auto;
    }
  }
</style>

<main role="main" aria-label="About Us Page">
  <h1><?= htmlspecialchars($settings['about_title'] ?? 'About Us') ?></h1>

  <?php if (!empty($settings['about_subtitle'])): ?>
    <p class="subtitle"><?= htmlspecialchars($settings['about_subtitle']) ?></p>
  <?php endif; ?>

  <div class="about-description">
    <?= nl2br(htmlspecialchars($settings['about_description'] ?? 'No description available.')) ?>
  </div>

  <section class="founder-card" aria-label="Founder Information">
    <?php
      $founderPhoto = trim($settings['founder_photo'] ?? '');
      if (!empty($founderPhoto)) {
          $photoPath = 'uploads/' . basename($founderPhoto);
          if (file_exists($photoPath)) {
              echo '<img src="' . htmlspecialchars($photoPath) . '" alt="' . htmlspecialchars($settings['founder_name'] ?? 'Founder') . '" class="founder-photo">';
          } else {
              echo '<div class="fallback">Founder photo not found.</div>';
          }
      } else {
          echo '<div class="fallback">No founder photo set.</div>';
      }
    ?>

    <div class="founder-info">
      <h2 class="founder-name"><?= htmlspecialchars($settings['founder_name'] ?? '') ?></h2>
      <div class="founder-role"><?= htmlspecialchars($settings['founder_role'] ?? '') ?></div>
      <p class="founder-bio"><?= nl2br(htmlspecialchars($settings['founder_bio'] ?? '')) ?></p>
    </div>
  </section>
</main>

<footer role="contentinfo">
  &copy; <?= date('Y') ?> YourCompany. All rights reserved.
</footer>

<?php include 'includes/footer.php'; ?>
