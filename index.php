<?php 
include 'includes/db.php'; 
include 'includes/header.php'; 


// Fetch whatsapp
$stmt = $pdo->prepare("SELECT `value` FROM settings WHERE `key` = 'whatsapp_number'");
$stmt->execute();
$whatsappNumber = $stmt->fetchColumn() ?: '';

// Fetch products 
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC"); 
$products = $stmt->fetchAll(PDO::FETCH_ASSOC); 

// Fetch services 
$stmt = $pdo->query("SELECT * FROM services ORDER BY id DESC"); 
$services = $stmt->fetchAll(PDO::FETCH_ASSOC); 

// Fetch testimonials
$stmt = $pdo->query("SELECT * FROM testimonials WHERE status = 'active' ORDER BY id DESC");
$testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Helper function: safe escape 
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Helper function: Check image file exists and return path or placeholder
function productImage($filename) {
    $path = 'products/' . $filename;
    if ($filename && file_exists($path)) {
        return $path;
    }
    return 'https://via.placeholder.com/160?text=No+Image';
}

// Helper function: Check icon URL exists or return placeholder
function serviceIcon($url) {
    if ($url) {
        return e($url);
    }
    return 'https://cdn-icons-png.flaticon.com/512/565/565547.png'; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>PureWater Delivery</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #222;
            text-align: center; /* Center all text */
        }

        .hero {
            background: linear-gradient(90deg, #00c6ff 0%, #0072ff 100%);
            color: #fff;
            padding: 100px 0;
            text-align: center;
        }
        .hero h1 {
            font-weight: 800;
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .hero p {
            font-weight: 400;
            font-size: 1.25rem;
            margin-bottom: 2rem;
        }
        .hero .btn {
            background-color: #fff;
            color: #007bff;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 25px;
            transition: background-color 0.3s ease;
            border: none;
            display: inline-block;
            text-align: center;
        }
        .hero .btn:hover {
            background-color: #007bff;
            color: #fff;
        }

        section {
            padding: 60px 0;
        }

        h2 {
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 2rem;
            color: #007bff;
        }

        /* Services Grid */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(250px,1fr));
            gap: 2rem;
            max-width: 1100px;
            margin: 0 auto;
            text-align: center;
        }
        .services-grid .card {
            border: none;
            padding: 1.5rem;
            box-shadow: 0 3px 6px rgb(0 0 0 / 0.1);
            border-radius: 12px;
            background: #fff;
            text-align: center;
        }
        .services-grid .card h4 {
            margin-top: 1rem;
            font-weight: 700;
            font-size: 1.25rem;
            color: #333;
        }
        .services-grid .card p {
            margin-top: 0.5rem;
            color: #555;
            font-size: 1rem;
        }
        .icon-fallback {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin: 0 auto;
            display: block;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(220px,1fr));
            gap: 2rem;
            max-width: 1100px;
            margin: 0 auto;
            text-align: center;
        }
        .product-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgb(0 0 0 / 0.1);
            overflow: hidden;
            padding-bottom: 1rem;
            text-align: center;
        }
        .product-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-bottom: 1px solid #eee;
            display: block;
            margin: 0 auto;
        }
        .product-card h4 {
            font-weight: 700;
            margin: 0.8rem 0 0.3rem;
            font-size: 1.2rem;
            color: #007bff;
        }
        .product-card p {
            padding: 0 1rem;
            font-size: 1rem;
            color: #555;
            min-height: 60px;
        }
        .price {
            font-weight: 700;
            color: #222;
            margin: 0.5rem 0;
            font-size: 1.1rem;
        }
        .btn.order-btn {
            background-color: #007bff;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 30px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-bottom: 0.5rem;
            display: inline-block;
            text-align: center;
        }
        .btn.order-btn:hover {
            background-color: #0056b3;
        }

        /* Testimonials Grid */
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(280px,1fr));
            gap: 1.8rem;
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
        }
        .testimonial-card {
            background: #fff;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 3px 10px rgb(0 0 0 / 0.1);
            font-style: italic;
            color: #444;
        }
        .testimonial-card h5 {
            margin-top: 1rem;
            font-weight: 600;
            font-style: normal;
            text-align: center;
            color: #007bff;
        }

        /* WhatsApp Contact Button */
        #whatsapp-contact {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #25d366;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            z-index: 9999;
            cursor: pointer;
        }
        #whatsapp-contact svg {
            fill: white;
            width: 32px;
            height: 32px;
        }

        /* Center modal form inputs text */
        .modal-content input,
        .modal-content textarea {
            text-align: center;
        }
    </style>
</head>
<body>

<!-- HERO -->
<section class="hero">
    <div class="hero-content container">
        <h1>Stay Hydrated with Pure, Delivered Water</h1>
        <p>Convenient, safe, affordable water delivery for your home or business.</p>
        <a href="#products" class="btn">Explore Products</a>
    </div>
</section>

<!-- SERVICES -->
<section id="services">
    <div class="container">
        <h2>Our Services</h2>
        <?php if ($services): ?>
            <div class="services-grid">
                <?php foreach ($services as $service): ?>
                    <div class="card d-flex flex-column align-items-center text-center">
                        <?php
                        $icon_url = $service['icon_url'] ?? '';
                        $isLordIcon = (bool)preg_match('/\.json$/i', $icon_url);
                        if ($isLordIcon): ?>
                            <lord-icon  
                                src="<?= e($icon_url) ?>"  
                                trigger="hover" 
                                colors="primary:#007bff,secondary:#00c6ff"
                                style="width:60px;height:60px; margin: 0 auto;">
                            </lord-icon>
                        <?php else: ?>
                            <img src="<?= serviceIcon($icon_url) ?>" alt="<?= e($service['title'] ?? 'Service Icon') ?>" class="icon-fallback" />
                        <?php endif; ?>
                        <h4 class="mt-3"><?= e($service['title'] ?? 'Untitled Service') ?></h4>
                        <p><?= e($service['description'] ?? '') ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center">No services available at the moment.</p>
        <?php endif; ?>
    </div>
</section>

<!-- PRODUCTS -->
<section id="products">
    <div class="container">
        <h2>Our Products</h2>
        <?php if ($products): ?>
            <?php $shownProducts = array_slice($products, 0, 3); ?>
            <div class="products-grid">
                <?php foreach ($shownProducts as $product): ?>
                    <div class="product-card">
                        <img src="<?= productImage($product['image_path'] ?? '') ?>" alt="<?= e($product['name'] ?? 'Product Image') ?>" />
                        <h4><?= e($product['name'] ?? 'Unnamed Product') ?></h4>
                        <p><?= e($product['description'] ?? '') ?></p>
                        <div class="price">QAR <?= number_format($product['price'] ?? 0, 2) ?></div>
                        <button 
                            class="btn order-btn" 
                            data-bs-toggle="modal" 
                            data-bs-target="#orderModal" 
                            data-product-name="<?= e($product['name']) ?>" 
                            data-product-id="<?= (int)($product['id'] ?? 0) ?>"
                        >Order</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <button 
                    class="btn btn-outline-primary" 
                    data-bs-toggle="modal" 
                    data-bs-target="#allProductsModal"
                >View All Products</button>
            </div>
        <?php else: ?>
            <p class="text-center">No products found.</p>
        <?php endif; ?>
    </div>
</section>

<!-- TESTIMONIALS -->
<section id="testimonials">
    <div class="container">
        <h2>Customer Feedback</h2>
        <?php if ($testimonials): ?>
            <div class="testimonials-grid" style="display:grid; grid-template-columns: repeat(auto-fit,minmax(280px,1fr)); gap:1.8rem; max-width:900px; margin: 0 auto; text-align:center;">
                <?php foreach ($testimonials as $t): ?>
                    <?php 
                        $name = htmlspecialchars($t['name'] ?? 'Anonymous', ENT_QUOTES, 'UTF-8');
                        $content = htmlspecialchars($t['content'] ?? '', ENT_QUOTES, 'UTF-8');
                        $photo = $t['photo'] ?? '';
                        $photoPath = "uploads/testimonials/" . $photo;
                        $photoUrl = !empty($photo) ? "uploads/" . $photo : null;

                    ?>
                    <div class="testimonial-card" style="background:#fff; border-radius:10px; padding:1.5rem; box-shadow:0 3px 10px rgb(0 0 0 / 0.1); font-style:italic; color:#444;">
                        <?php if ($photoUrl): ?>
                            <img 
                                src="<?= $photoUrl ?>" 
                                alt="<?= $name ?>" 
                                onerror="this.onerror=null;this.src='assets/default-avatar.png';" 
                                style="width:60px; height:60px; border-radius:50%; object-fit:cover; margin-bottom:10px; display:block; margin-left:auto; margin-right:auto;"
                            />
                        <?php else: ?>
                            <div style="width:60px; height:60px; border-radius:50%; background:#007bff; color:#fff; display:flex; justify-content:center; align-items:center; font-weight:bold; font-size:1.2rem; margin:0 auto 10px;">
                                <?= strtoupper(substr($name, 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <p>"<?= $content ?>"</p>
                        <h5 style="margin-top:1rem; font-weight:600; font-style:normal; color:#007bff;">- <?= $name ?> -</h5>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center">No testimonials available.</p>
        <?php endif; ?>
    </div>
</section>





<!-- ORDER MODAL -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content text-center">
      <div class="modal-header">
        <h5 class="modal-title" id="orderModalLabel">Order Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="order_submit.php" method="POST" class="row g-3 p-3">
          <div class="col-md-6">
            <label for="customer_name" class="form-label">Name</label>
            <input type="text" name="customer_name" id="customer_name" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label for="customer_phone" class="form-label">Phone</label>
            <input type="text" name="customer_phone" id="customer_phone" class="form-control" required>
          </div>
          <div class="col-12">
            <label for="delivery_address" class="form-label">Address</label>
            <textarea name="delivery_address" id="delivery_address" class="form-control" required></textarea>
          </div>
          <div class="col-md-6">
            <label for="product_id" class="form-label">Bottle Name</label>
            <select name="product_id" id="product_id" class="form-select" required>
              <?php
              foreach ($products as $product) {
                  echo "<option value=\"" . htmlspecialchars($product['id']) . "\">" . htmlspecialchars($product['name']) . "</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-6">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" required>
          </div>
          <div class="col-12 d-flex justify-content-center gap-2 mt-3">
            <button type="submit" class="btn btn-primary">Place Order</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
      </form>
    </div>
  </div>
</div>


<!-- ALL PRODUCTS MODAL -->
<div class="modal fade" id="allProductsModal" tabindex="-1" aria-labelledby="allProductsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content text-center">
      <div class="modal-header">
        <h5 class="modal-title" id="allProductsModalLabel">All Products</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="products-grid">
          <?php foreach ($products as $product): ?>
            <div class="product-card">
                <img src="<?= productImage($product['image_path'] ?? '') ?>" alt="<?= e($product['name'] ?? 'Product Image') ?>" />
                <h4><?= e($product['name'] ?? 'Unnamed Product') ?></h4>
                <p><?= e($product['description'] ?? '') ?></p>
                <div class="price">QAR<?= number_format($product['price'] ?? 0, 2) ?></div>
                <button 
                    class="btn order-btn" 
                    data-bs-toggle="modal" 
                    data-bs-target="#orderModal" 
                    data-product-name="<?= e($product['name']) ?>" 
                    data-product-id="<?= (int)($product['id'] ?? 0) ?>"
                >Order</button>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>



<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Lordicon CDN -->
<script src="https://cdn.lordicon.com/ritcuqlt.js"></script>

<script>
    // When order modal opens, fill product data in hidden input
    var orderModal = document.getElementById('orderModal');
    orderModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      var productName = button.getAttribute('data-product-name');
      var productId = button.getAttribute('data-product-id');
      var modalTitle = orderModal.querySelector('.modal-title');
      var modalProductIdInput = orderModal.querySelector('#modalProductId');

      modalTitle.textContent = 'Order: ' + productName;
      modalProductIdInput.value = productId;
    });
</script>


<?php if (!empty($whatsappNumber)): ?>
  <a href="https://wa.me/<?= htmlspecialchars($whatsappNumber) ?>" 
     target="_blank" 
     id="whatsapp-contact"
     style="
       position: fixed;
       bottom: 20px;
       right: 20px;
       background-color: #25D366;
       border-radius: 50%;
       width: 60px;
       height: 60px;
       display: flex;
       align-items: center;
       justify-content: center;
       box-shadow: 0 2px 5px rgba(0,0,0,0.3);
       z-index: 1000;
       text-decoration: none;
     "
     aria-label="Contact us on WhatsApp">
    <svg xmlns="http://www.w3.org/2000/svg" 
         width="32" height="32" fill="#fff" viewBox="0 0 24 24">
      <path d="M12.04 2C6.56 2 2.05 6.51 2.05 12c0 2.09.57 4.06 1.57 5.75L2 22l4.39-1.54A9.97 9.97 0 0 0 12.04 22C17.52 22 22 17.49 22 12S17.52 2 12.04 2zm0 18c-1.84 0-3.55-.5-5-1.37l-.36-.21-2.61.92.88-2.54-.24-.38A7.92 7.92 0 0 1 4.05 12c0-4.39 3.6-8 8-8s8 3.61 8 8-3.61 8-8 8zm3.43-5.49c-.24-.12-1.42-.7-1.64-.78-.22-.08-.38-.12-.53.12-.15.25-.61.78-.74.94-.13.15-.27.17-.5.06a6.49 6.49 0 0 1-3.22-2.82c-.24-.42.24-.39.69-1.3.08-.17.04-.31-.02-.43-.07-.12-.53-1.28-.73-1.75-.19-.46-.38-.4-.53-.4l-.45-.01c-.15 0-.4.06-.6.28-.2.21-.79.77-.79 1.87 0 1.1.81 2.17.92 2.32.12.15 1.6 2.46 3.88 3.45 1.45.63 2.02.69 2.74.58.44-.07 1.42-.58 1.62-1.15.2-.56.2-1.04.14-1.15-.06-.12-.22-.19-.46-.31z"/>
    </svg>
  </a>
<?php endif; ?>

</body>
</html>
