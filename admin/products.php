<?php 
include 'header.php'; 
include '../includes/auth.php'; 
include '../includes/db.php'; 
 
// Handle Create & Delete 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) { 
    $name = trim($_POST['product_name']); 
    $desc = trim($_POST['product_desc']); 
    $price = floatval($_POST['product_price']); 
    $imagePath = ''; 
 
    // Check if image is uploaded 
    if (!empty($_FILES['product_image']['name'])) { 
        $uploadDir = "../uploads/"; 
 
        // Make sure upload directory exists and is writable 
        if (!is_dir($uploadDir)) { 
            mkdir($uploadDir, 0755, true); 
        } 
 
        $filename = uniqid() . "_" . basename($_FILES['product_image']['name']); 
        $targetFile = $uploadDir . $filename; 
 
        // Check for upload errors 
        if ($_FILES['product_image']['error'] === UPLOAD_ERR_OK) { 
            // Validate file type (optional, but recommended) 
            $fileType = mime_content_type($_FILES['product_image']['tmp_name']); 
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']; 
 
            if (in_array($fileType, $allowedTypes)) { 
                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFile)) { 
                    $imagePath = $targetFile; 
                } else { 
                    // File move failed 
                    $uploadError = "Failed to move uploaded file."; 
                } 
            } else { 
                $uploadError = "Invalid image type. Allowed types: JPEG, PNG, GIF, WEBP."; 
            } 
        } else { 
            $uploadError = "Upload error code: " . $_FILES['product_image']['error']; 
        } 
    } 
 
    if (!isset($uploadError)) { 
        // Insert product into DB 
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_path) VALUES (?, ?, ?, ?)"); 
        $stmt->execute([$name, $desc, $price, $imagePath]); 
        header("Location: products.php?success=Product added successfully"); 
        exit(); 
    } else { 
        // Redirect with error message (encode to be safe) 
        header("Location: products.php?error=" . urlencode($uploadError)); 
        exit(); 
    } 
} 
 
if (isset($_GET['delete_product'])) { 
    $id = intval($_GET['delete_product']); 
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?"); 
    $stmt->execute([$id]); 
    header("Location: products.php?success=Product deleted successfully"); 
    exit(); 
} 
 
// Fetch products 
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC"); 
$products = $stmt->fetchAll(PDO::FETCH_ASSOC); 
?> 
 
<div class="container my-5">

    <a href="dashboard.php" class="btn btn-secondary mb-3">
        ← Back
    </a>

    <h1 class="mb-4 text-primary">💧 Manage Products</h1> 
 
    <!-- Success Alert --> 
    <?php if (isset($_GET['success'])): ?> 
        <div class="alert alert-success alert-dismissible fade show" role="alert"> 
            <?= htmlspecialchars($_GET['success']) ?> 
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> 
        </div> 
    <?php endif; ?> 
 
    <!-- Error Alert --> 
    <?php if (isset($_GET['error'])): ?> 
        <div class="alert alert-danger alert-dismissible fade show" role="alert"> 
            <?= htmlspecialchars($_GET['error']) ?> 
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> 
        </div> 
    <?php endif; ?> 
 
    <!-- Add Product Card --> 
    <div class="card mb-5 shadow-sm"> 
        <div class="card-header bg-primary text-white"> 
            <h5 class="mb-0">➕ Add New Product</h5> 
        </div> 
        <div class="card-body"> 
            <form method="post" enctype="multipart/form-data" class="row g-3 align-items-end"> 
                <div class="col-md-3"> 
                    <label for="product_name" class="form-label">Product Name <span class="text-danger">*</span></label> 
                    <input type="text" id="product_name" name="product_name" placeholder="Enter product name" class="form-control" required> 
                </div> 
                <div class="col-md-4"> 
                    <label for="product_desc" class="form-label">Description</label> 
                    <textarea id="product_desc" name="product_desc" placeholder="Product description" class="form-control" rows="2"></textarea> 
                </div> 
                <div class="col-md-2"> 
                    <label for="product_price" class="form-label">Price (Rs) <span class="text-danger">*</span></label> 
                    <input type="number" step="0.01" id="product_price" name="product_price" placeholder="0.00" class="form-control" required> 
                </div> 
                <div class="col-md-2"> 
                    <label for="product_image" class="form-label">Product Image</label> 
                    <input type="file" id="product_image" name="product_image" class="form-control" accept="image/*"> 
                </div> 
                <div class="col-md-1 d-grid"> 
                    <button type="submit" name="add_product" class="btn btn-success">Add</button> 
                </div> 
            </form> 
        </div> 
    </div> 
 
    <!-- Products List Table --> 
    <div class="card shadow-sm"> 
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center"> 
            <h5 class="mb-0">📦 Products List</h5> 
            <button onclick="window.print()" class="btn btn-light btn-sm">🖨️ Print</button> 
        </div> 
        <div class="card-body p-0"> 
            <table class="table table-hover table-bordered mb-0 align-middle"> 
                <thead class="table-secondary text-center"> 
                    <tr> 
                        <th style="width: 5%;">ID</th> 
                        <th style="width: 20%;">Name</th> 
                        <th>Description</th> 
                        <th style="width: 10%;">Price (Rs)</th> 
                        <th style="width: 15%;">Image</th> 
                        <th style="width: 15%;">Actions</th> 
                    </tr> 
                </thead> 
                <tbody> 
                    <?php if ($products): ?> 
                        <?php foreach ($products as $p): ?> 
                        <tr> 
                            <td class="text-center"><?= $p['id'] ?></td> 
                            <td><?= htmlspecialchars($p['name']) ?></td> 
                            <td><?= htmlspecialchars($p['description']) ?></td> 
                            <td class="text-end"><?= number_format($p['price'], 2) ?></td> 
                            <td class="text-center"> 
                                <?php if ($p['image_path'] && file_exists($p['image_path'])): ?> 
                                    <img src="<?= $p['image_path'] ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="img-thumbnail" style="max-height: 80px;"> 
                                <?php else: ?> 
                                    <span class="text-muted">No image</span> 
                                <?php endif; ?> 
                            </td> 
                            <td class="text-center"> 
                                <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-primary me-1" title="Edit Product"> 
                                    ✏️ Edit 
                                </a> 
                                <a href="?delete_product=<?= $p['id'] ?>" onclick="return confirm('Are you sure you want to delete this product?')" class="btn btn-sm btn-danger" title="Delete Product"> 
                                    ❌ Delete 
                                </a> 
                            </td> 
                        </tr> 
                        <?php endforeach; ?> 
                    <?php else: ?> 
                        <tr><td colspan="6" class="text-center text-muted">No products found.</td></tr> 
                    <?php endif; ?> 
                </tbody> 
            </table> 
        </div> 
    </div> 
 
</div> 
 
<style> 
  .img-thumbnail { 
    max-width: 100%; 
    height: auto; 
    border-radius: 0.5rem; 
  } 
</style> 
 
<?php include '../includes/footer.php'; ?> 
