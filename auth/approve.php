<?php
session_start();
include("../database/connection.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $stmt = $conn->prepare("UPDATE products SET status = 'approved' WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
}

// Redirect to the correct admin dashboard
header("Location: ../dash/admin.php");
exit();
?>
