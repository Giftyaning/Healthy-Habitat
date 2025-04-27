<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
session_start();

// Adjust the path below as needed!
include("../database/connection.php");

// CSRF check
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

$product_id = $_POST['product_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if (!$product_id || !$user_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

// Check if the user has already voted for this product
$stmt = $conn->prepare("SELECT id FROM product_votes WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'You have already voted for this product.']);
    exit;
}
$stmt->close();

// Save the vote (set rating to NULL)
$stmt = $conn->prepare("INSERT INTO product_votes (user_id, product_id, rating, vote_date) VALUES (?, ?, NULL, NOW())");
$stmt->bind_param("ii", $user_id, $product_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Vote saved successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save vote.', 'error' => $stmt->error]);
}
$stmt->close();
?>
