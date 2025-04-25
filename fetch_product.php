<?php
include("connection.php");

if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Fetch product data
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    header('Content-Type: application/json');
    echo json_encode($product);
}
?>
