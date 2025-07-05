<?php
include '../includes/db.php';

$lastId = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE id > ?");
$stmt->execute([$lastId]);
$newOrders = $stmt->fetchColumn();

echo json_encode(['newOrders' => $newOrders]);
?>
