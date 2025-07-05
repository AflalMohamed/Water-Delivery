<?php 
include 'includes/header.php';
include 'includes/db.php';

// Get all settings
$stmt = $pdo->query("SELECT `key`, `value` FROM settings");
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">

      <div class="bg-white rounded-4 p-4 shadow-sm mb-4">
        <h2 class="text-primary mb-4">Contact Us</h2>

        <div class="mb-3">
          <strong class="text-primary d-inline-block" style="width:130px;">Email:</strong>
          <span><?= htmlspecialchars($settings['email_address'] ?? 'Not set') ?></span>
        </div>

        <div class="mb-3">
          <strong class="text-primary d-inline-block" style="width:130px;">Phone:</strong>
          <span><?= htmlspecialchars($settings['phone_number'] ?? 'Not set') ?></span>
        </div>

        <div class="mb-3">
          <strong class="text-primary d-inline-block" style="width:130px;">WhatsApp:</strong>
          <span><?= htmlspecialchars($settings['whatsapp_number'] ?? 'Not set') ?></span>
        </div>

        <div class="mb-3">
          <strong class="text-primary d-inline-block" style="width:130px;">Address:</strong>
          <span style="white-space: pre-line;"><?= htmlspecialchars($settings['company_address'] ?? 'Not set') ?></span>
        </div>
      </div>

      <a href="index.php" class="btn btn-primary d-block mx-auto" style="max-width: 300px;">
        ← Back to Dashboard
      </a>
    </div>
  </div>
</div>

<?php
  // Prepare WhatsApp link if number exists
  $whatsappNumber = $settings['whatsapp_number'] ?? '';
  $waNumberClean = preg_replace('/\D/', '', $whatsappNumber);
  $whatsappLink = "https://wa.me/{$waNumberClean}";
?>

<?php if ($whatsappNumber): ?>
  <a href="<?= $whatsappLink ?>" target="_blank" 
     class="position-fixed bottom-0 end-0 m-3 rounded-circle d-flex justify-content-center align-items-center"
     style="width:60px; height:60px; background-color:#25d366; box-shadow: 2px 2px 8px rgba(0,0,0,0.2); z-index: 1100;"
     title="Chat with us on WhatsApp">
    <img src="https://cdn-icons-png.flaticon.com/512/733/733585.png" alt="WhatsApp" style="width: 60%; height: auto;">
  </a>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
