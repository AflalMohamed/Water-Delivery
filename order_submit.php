<?php
include 'includes/db.php';

$message = '';
$message_type = ''; // 'success' or 'error'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $customer_name = filter_input(INPUT_POST, 'customer_name', FILTER_SANITIZE_STRING);
    $customer_phone = filter_input(INPUT_POST, 'customer_phone', FILTER_SANITIZE_STRING);
    $delivery_address = filter_input(INPUT_POST, 'delivery_address', FILTER_SANITIZE_STRING);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    if (!$quantity || $quantity < 1) {
        $quantity = 1;
    }

    if (!$product_id || !$customer_name || !$customer_phone || !$delivery_address) {
        $message = 'Please fill all required fields.';
        $message_type = 'error';
    } else {
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            $message = 'Invalid product selected.';
            $message_type = 'error';
        } else {
            $total_price = $product['price'] * $quantity;

            $stmt = $pdo->prepare("INSERT INTO orders (product_id, customer_name, customer_phone, delivery_address, quantity, total_price, order_date) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            if ($stmt->execute([
                $product_id,
                $customer_name,
                $customer_phone,
                $delivery_address,
                $quantity,
                $total_price
            ])) {
                $message = 'Order placed successfully!';
                $message_type = 'success';
            } else {
                $message = 'Failed to place order. Please try again.';
                $message_type = 'error';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Place Order</title>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 2em;
    }
    form {
        max-width: 400px;
        margin-top: 1em;
    }
    label {
        display: block;
        margin-bottom: 0.3em;
        font-weight: bold;
    }
    input, select, textarea {
        width: 100%;
        padding: 0.4em;
        margin-bottom: 1em;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    button {
        background-color: #007bff;
        color: white;
        padding: 0.6em 1.2em;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1em;
    }
    button:hover {
        background-color: #0056b3;
    }

    /* Popup overlay */
    .popup-overlay {
        display: none; /* Hidden by default */
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: rgba(0,0,0,0.5);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.3s ease forwards;
    }
    .popup-content {
        background: white;
        padding: 2em 3em;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        max-width: 400px;
        text-align: center;
        position: relative;
        animation: popupPop 0.5s ease forwards;
        font-size: 1.1em;
        font-weight: 600;
    }
    .popup-content.success {
        border-left: 6px solid #28a745;
        color: #155724;
    }
    .popup-content.error {
        border-left: 6px solid #dc3545;
        color: #721c24;
    }
    .popup-close {
        position: absolute;
        top: 8px;
        right: 12px;
        font-size: 1.5em;
        font-weight: bold;
        color: #999;
        cursor: pointer;
        user-select: none;
    }
    .popup-close:hover {
        color: #333;
    }

    @keyframes popupPop {
        0% {
            opacity: 0;
            transform: scale(0.7);
        }
        60% {
            opacity: 1;
            transform: scale(1.1);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }
    @keyframes fadeIn {
        from {opacity: 0;}
        to {opacity: 1;}
    }
</style>
</head>
<body>

  

<?php if ($message): ?>
<div id="popup" class="popup-overlay" style="display:flex;">
    <div class="popup-content <?= $message_type ?>">
        <span class="popup-close" onclick="closePopup()">&times;</span>
        <?= htmlspecialchars($message) ?>
    </div>
</div>

<script>
function closePopup() {
    document.getElementById('popup').style.display = 'none';
}

// Optional: auto close after 5 seconds
setTimeout(() => {
    closePopup();
}, 5000);
</script>
<?php endif; ?>

</body>
</html>
