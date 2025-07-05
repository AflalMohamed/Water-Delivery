<?php
include '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (isset($_POST['id'])) {
    // Single delete
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
    $success = $stmt->execute([$id]);
    echo json_encode(['success' => $success]);
    exit;
}

if (isset($_POST['ids'])) {
    // Bulk delete
    $ids = explode(',', $_POST['ids']);
    $ids = array_map('intval', $ids);

    if (count($ids) === 0) {
        echo json_encode(['success' => false, 'message' => 'No IDs provided']);
        exit;
    }

    // Prepare placeholders for IN clause
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id IN ($placeholders)");
    $success = $stmt->execute($ids);
    echo json_encode(['success' => $success]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'No valid parameters provided']);
?>
