<?php
include '../includes/db.php';

// Fetch all orders with product info
$sql = "SELECT o.id, o.product_id, p.name AS product_name,
        o.customer_name, o.customer_phone, o.delivery_address, o.quantity, o.total_price, o.order_date, o.status
        FROM orders o
        LEFT JOIN products p ON o.product_id = p.id
        ORDER BY o.id DESC";

$stmt = $pdo->query($sql);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard - Orders</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding: 30px;
    }

    h1 {
      color: #0d6efd;
      font-weight: 700;
      margin-bottom: 30px;
      text-align: center;
      letter-spacing: 1.2px;
    }

    .actions-bar {
      margin-bottom: 25px;
      display: flex;
      justify-content: flex-start;
      gap: 15px;
      flex-wrap: wrap;
    }

    .btn-custom {
      font-weight: 600;
      padding: 10px 22px;
      border-radius: 6px;
      box-shadow: 0 4px 12px rgb(0 0 0 / 0.1);
      user-select: none;
      transition: box-shadow 0.3s ease;
    }
    .btn-custom:hover {
      box-shadow: 0 6px 16px rgb(0 0 0 / 0.15);
    }

    .back-btn {
      background-color: #6c757d;
      color: white;
      border: none;
    }
    .print-btn {
      background-color: #0d6efd;
      color: white;
      border: none;
    }
    .delete-selected-btn {
      background-color: #dc3545;
      color: white;
      border: none;
    }

    table {
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 20px rgb(0 0 0 / 0.1);
      width: 100%;
      table-layout: fixed;
      word-wrap: break-word;
    }

    thead th {
      background-color: #0d6efd;
      color: white;
      font-weight: 600;
      vertical-align: middle;
      text-align: center;
      padding: 15px 12px;
      user-select: none;
      white-space: nowrap;
    }

    tbody td {
      vertical-align: middle;
      padding: 12px 15px;
      font-size: 0.95rem;
      color: #212529;
      text-align: center;
      border-top: 1px solid #dee2e6;
      word-break: break-word;
    }

    tbody tr:hover {
      background-color: #e9f0ff;
      transition: background-color 0.2s ease;
    }

    td.delivery-address {
      text-align: left;
      max-width: 250px;
      white-space: normal !important;
      word-break: break-word;
      padding-left: 20px;
    }

    select.form-select {
      width: 140px;
      padding: 6px 10px;
      font-size: 0.9rem;
      cursor: pointer;
      border-radius: 6px;
      margin: auto;
    }

    button.delete-btn {
      background-color: #dc3545;
      color: white;
      border: none;
      padding: 6px 14px;
      font-weight: 600;
      border-radius: 6px;
      transition: background-color 0.25s ease;
      cursor: pointer;
    }
    button.delete-btn:hover {
      background-color: #bb2d3b;
    }

    @media (max-width: 992px) {
      table thead th, tbody td {
        font-size: 0.85rem;
        padding: 8px 8px;
      }
      .actions-bar {
        flex-direction: column;
        gap: 10px;
      }
      .btn-custom {
        width: 100%;
      }
      td.delivery-address {
        max-width: 100%;
        padding-left: 15px;
        text-align: left;
      }
    }

    @media print {
      body * {
        visibility: hidden;
      }
      #order-table, #order-table * {
        visibility: visible;
      }
      #order-table {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none;
        border: none;
        border-radius: 0;
      }
      .actions-bar {
        display: none;
      }
    }
  </style>
</head>
<body>

  <h1>Order List</h1>

  <div class="actions-bar">
    <button class="btn btn-custom back-btn" onclick="window.history.back()">← Back</button>
    <button class="btn btn-custom print-btn" onclick="window.print()">🖨️ Print Orders</button>
    <button class="btn btn-custom delete-selected-btn" onclick="deleteSelectedOrders()">Delete Selected</button>
  </div>

  <div class="table-responsive">
    <table id="order-table" class="table align-middle text-center">
      <thead>
        <tr>
          <th><input type="checkbox" id="select-all" /></th>
          <th>ID</th>
          <th>Product Name</th>
          <th>Customer Name</th>
          <th>Customer Phone</th>
          <th>Delivery Address</th>
          <th>Quantity</th>
          <th>Total Price</th>
          <th>Order Date</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $order): ?>
          <tr id="order-row-<?= htmlspecialchars($order['id']) ?>">
            <td><input type="checkbox" class="order-checkbox" data-id="<?= (int)$order['id'] ?>" /></td>
            <td><?= htmlspecialchars($order['id']) ?></td>
            <td><?= htmlspecialchars($order['product_name'] ?? 'Unknown') ?></td>
            <td><?= htmlspecialchars($order['customer_name']) ?></td>
            <td><?= htmlspecialchars($order['customer_phone']) ?></td>
            <td class="delivery-address"><?= nl2br(htmlspecialchars($order['delivery_address'])) ?></td>
            <td><?= (int)$order['quantity'] ?></td>
            <td>$<?= number_format($order['total_price'], 2) ?></td>
            <td><?= date('Y-m-d H:i:s', strtotime($order['order_date'])) ?></td>
            <td>
              <select class="form-select" onchange="updateStatus(<?= (int)$order['id'] ?>, this.value)">
                <option value="Pending" <?= $order['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Processing" <?= $order['status'] === 'Processing' ? 'selected' : '' ?>>Processing</option>
                <option value="Delivered" <?= $order['status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
              </select>
            </td>
            <td>
              <button class="delete-btn" onclick="deleteOrder(<?= (int)$order['id'] ?>)">Delete</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

<script>
  document.getElementById('select-all').addEventListener('change', function () {
    const checked = this.checked;
    document.querySelectorAll('.order-checkbox').forEach(cb => {
      cb.checked = checked;
    });
  });

  function updateStatus(orderId, newStatus) {
    fetch('update_status.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `id=${encodeURIComponent(orderId)}&status=${encodeURIComponent(newStatus)}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        Swal.fire('Updated!', 'Order status updated.', 'success');
      } else {
        Swal.fire('Error', data.message || 'Failed to update status.', 'error');
      }
    })
    .catch(() => Swal.fire('Error', 'Network error.', 'error'));
  }

  function deleteOrder(orderId) {
    Swal.fire({
      title: 'Are you sure?',
      text: "This will permanently delete the order.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        fetch('delete_order.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
          body: `id=${encodeURIComponent(orderId)}`
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            Swal.fire('Deleted!', 'Order has been deleted.', 'success').then(() => {
              const row = document.getElementById('order-row-' + orderId);
              if (row) row.remove();
              document.getElementById('select-all').checked = false;
            });
          } else {
            Swal.fire('Error', data.message || 'Failed to delete order.', 'error');
          }
        })
        .catch(() => Swal.fire('Error', 'Network error.', 'error'));
      }
    });
  }

  function deleteSelectedOrders() {
    const selected = Array.from(document.querySelectorAll('.order-checkbox:checked')).map(cb => cb.dataset.id);
    if (selected.length === 0) {
      Swal.fire('No Orders Selected', 'Please select at least one order to delete.', 'info');
      return;
    }

    Swal.fire({
      title: 'Are you sure?',
      text: `This will permanently delete ${selected.length} order(s).`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      confirmButtonText: 'Yes, delete them!'
    }).then(result => {
      if (result.isConfirmed) {
        fetch('delete_order.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
          body: `ids=${encodeURIComponent(JSON.stringify(selected))}`
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            Swal.fire('Deleted!', `${selected.length} order(s) have been deleted.`, 'success').then(() => {
              selected.forEach(id => {
                const row = document.getElementById('order-row-' + id);
                if (row) row.remove();
              });
              document.getElementById('select-all').checked = false;
            });
          } else {
            Swal.fire('Error', data.message || 'Failed to delete selected orders.', 'error');
          }
        })
        .catch(() => Swal.fire('Error', 'Network error.', 'error'));
      }
    });
  }
</script>

</body>
</html>
