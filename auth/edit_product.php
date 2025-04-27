<?php
session_start();
include("../database/connection.php");

if (!isset($_SESSION['business_id'])) {
    header("Location: login.php");
    exit();
}
$business_id = $_SESSION['business_id'];
$product_id = intval($_GET['id'] ?? 0);

$error = '';
$success = '';

if (!$product_id) {
    header("Location: ../dash/business.php");
    exit();
}

// Fetch product
$stmt = $conn->prepare("SELECT * FROM products WHERE id=? AND business_id=?");
$stmt->bind_param("ii", $product_id, $business_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: ../dash/business.php");
    exit();
}

// Handle update
if (isset($_POST['update_product'])) {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? 0;
    $category = $_POST['category'] ?? '';
    $product_category = $_POST['product_category'] ?? '';
    $city = $_POST['city'] ?? '';

    if ($name && $description && $price && $category && $product_category && $city) {
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, category=?, product_category=?, city=? WHERE id=? AND business_id=?");
        $stmt->bind_param("ssdsssii", $name, $description, $price, $category, $product_category, $city, $product_id, $business_id);
        $stmt->execute();
        $success = "Product updated successfully!";
        
         header("Location: ../dash/business.php"); exit();
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Product</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Product or Service Name*</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description*</label>
            <textarea name="description" class="form-control" required><?= htmlspecialchars($product['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Price (£)*</label>
            <input type="number" step="0.01" min="0" name="price" class="form-control" value="<?= htmlspecialchars($product['price']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Pricing Category*</label>
            <select name="category" class="form-select" required>
                <option value="budget" <?= $product['category'] == 'budget' ? 'selected' : '' ?>>Budget</option>
                <option value="midrange" <?= $product['category'] == 'midrange' ? 'selected' : '' ?>>Midrange</option>
                <option value="premium" <?= $product['category'] == 'premium' ? 'selected' : '' ?>>Premium</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Product Category*</label>
            <select name="product_category" class="form-select" required>
                <option value="Healthy Eating" <?= $product['product_category'] == 'beauty' ? 'selected' : '' ?>>Healthy Eating Programs</option>
                <option value="Fitness and Wellness" <?= $product['product_category'] == 'food' ? 'selected' : '' ?>>Fitness and Wellness</option>
                <option value="Sustainable Living" <?= $product['product_category'] == 'clothing' ? 'selected' : '' ?>>Sustainable Living</option>
                <option value="Mindfulness and Mental Health" <?= $product['product_category'] == 'clothing' ? 'selected' : '' ?>>Mindfulness and Mental Health</option>
                <option value="Reusable Health Products" <?= $product['product_category'] == 'clothing' ? 'selected' : '' ?>>Reusable Health Products</option>
                <option value="Eco-Friencdly Fitness Gear" <?= $product['product_category'] == 'clothing' ? 'selected' : '' ?>>Eco-Friendly Fitness Gear</option>
                <option value="Organic Care" <?= $product['product_category'] == 'clothing' ? 'selected' : '' ?>>Organic Personal Care Products</option>
                <option value="Home Wellness" <?= $product['product_category'] == 'clothing' ? 'selected' : '' ?>>Home Wellness</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">City*</label>
            <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($product['city']) ?>" required>
        </div>
        <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
        <a href="../dash/business.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
