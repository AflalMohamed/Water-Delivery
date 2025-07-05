<?php
include '../includes/auth.php';
include '../includes/db.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // Optional: Delete image file too if you want
    $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: admin_testimonials.php");
exit;
