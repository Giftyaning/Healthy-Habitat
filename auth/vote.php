<?php
session_start();
include("database/connection.php");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$user_id = $_SESSION['user_id'] ?? null;

if (!$product_id || !$user_id) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to vote.']);
    exit;
}

// Check if this user already voted for this product
$stmt = $conn->prepare("SELECT id FROM product_votes WHERE product_id = ? AND user_id = ?");
$stmt->bind_param("ii", $product_id, $user_id);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You already voted for this product']);
    exit;
}

// Insert vote (rating = 1 for thumbs up)
$stmt = $conn->prepare("INSERT INTO product_votes (product_id, user_id, rating, vote_date) VALUES (?, ?, 1, NOW())");
$stmt->bind_param("ii", $product_id, $user_id);
$stmt->execute();

echo json_encode(['success' => true, 'message' => 'Vote recorded']);
?>
