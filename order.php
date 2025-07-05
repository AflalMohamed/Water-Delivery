<?php include 'includes/header.php'; ?>

<style>
  /* Blue theme accents */
  h2 {
    color: #0d6efd; /* Bootstrap primary blue */
  }
  label {
    color: #0d6efd;
  }
  .form-control:focus, 
  .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
  }
  .btn-success {
    background-color: #0d6efd; /* primary blue */
    border-color: #0d6efd;
    transition: background-color 0.3s ease, border-color 0.3s ease;
  }
  .btn-success:hover, .btn-success:focus {
    background-color: #0b5ed7;
    border-color: #0b5ed7;
    box-shadow: 0 0 8px rgba(11, 94, 215, 0.7);
  }
</style>

<div class="container my-5" style="max-width: 700px;">
  <h2 class="mb-4 fw-bold text-center">Place an Order</h2>
  
  <form action="order_submit.php" method="POST" class="row g-4 needs-validation" novalidate>
    <div class="col-md-6">
      <label for="customer_name" class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
      <input type="text" name="customer_name" id="customer_name" class="form-control" placeholder="Your full name" required>
      <div class="invalid-feedback">Please enter your name.</div>
    </div>
    
    <div class="col-md-6">
      <label for="customer_phone" class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
      <input type="tel" name="customer_phone" id="customer_phone" class="form-control" placeholder="+1 234 567 8900" required pattern="^\+?[0-9\s\-]{7,15}$">
      <div class="invalid-feedback">Please enter a valid phone number.</div>
    </div>
    
    <div class="col-12">
      <label for="delivery_address" class="form-label fw-semibold">Delivery Address <span class="text-danger">*</span></label>
      <textarea name="delivery_address" id="delivery_address" class="form-control" rows="3" placeholder="Street, City, Zip, Country" required></textarea>
      <div class="invalid-feedback">Please enter the delivery address.</div>
    </div>
    
    <div class="col-md-4">
      <label for="product_id" class="form-label fw-semibold">Bottle Name <span class="text-danger">*</span></label>
      <select name="product_id" id="product_id" class="form-select" required>
        <option value="" disabled selected>Select bottle</option>
        <?php
        include 'includes/db.php';
        // Fetch products (bottle sizes)
        $stmt = $pdo->query("SELECT id, name FROM products ORDER BY name");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($products as $product) {
            echo "<option value=\"" . htmlspecialchars($product['id']) . "\">" . htmlspecialchars($product['name']) . "</option>";
        }
        ?>
      </select>
      <div class="invalid-feedback">Please select a bottle.</div>
    </div>
    
    <div class="col-md-4">
      <label for="quantity" class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
      <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" required>
      <div class="invalid-feedback">Please enter a valid quantity.</div>
    </div>
    
    <div class="col-md-4">
      <label for="delivery_date" class="form-label fw-semibold">Delivery Date <span class="text-danger">*</span></label>
      <input type="date" name="delivery_date" id="delivery_date" class="form-control" required>
      <div class="invalid-feedback">Please select a delivery date.</div>
    </div>
    
    <div class="col-12 d-grid">
      <button type="submit" class="btn btn-success btn-lg fw-semibold shadow-sm">Submit Order</button>
    </div>
  </form>
</div>

<script>
  // Bootstrap 5 form validation
  (() => {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })
  })()
</script>

<?php include 'includes/footer.php'; ?>
