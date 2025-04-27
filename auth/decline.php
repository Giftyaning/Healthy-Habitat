<?php
session_start();
include("../database/connection.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $stmt = $conn->prepare("UPDATE products SET status = 'declined' WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
}

header("Location: admin.php");
exit();
?>
