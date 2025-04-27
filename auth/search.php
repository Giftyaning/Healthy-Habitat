<?php
session_start();
include("../database/connection.php");

$searchTerm = trim($_GET['q'] ?? '');

$results = [];
if ($searchTerm !== '') {
    $stmt = $conn->prepare("SELECT * FROM products WHERE status='approved' AND (name LIKE ? OR category LIKE ? OR product_category LIKE ?)");
    $like = "%$searchTerm%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Search Results for "<?= htmlspecialchars($searchTerm) ?>"</h2>
        <?php if ($searchTerm === ''): ?>
            <div class="alert alert-warning mt-3">Please enter a search term.</div>
        <?php elseif (empty($results)): ?>
            <div class="alert alert-danger mt-3">Product not available.</div>
        <?php else: ?>
            <div class="row mt-4">
                <?php foreach ($results as $product): ?>
                <div class="col-md-3 col-6 mb-4">
                    <div class="card h-100">
                        <img src="/healthy-habitat-network/uploads/<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                            <p class="card-text"><strong>£<?= number_format($product['price'], 2) ?></strong></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <a href="../index.php" class="btn mt-4">Back to Home</a>
    </div>
</body>
</html>
