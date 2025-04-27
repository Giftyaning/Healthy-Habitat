<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include("../database/connection.php");

// Check if business is logged in
if (!isset($_SESSION['business_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
$business_id = $_SESSION['business_id'];

$error = '';

// Handle Add Product
if (isset($_POST['add_product'])) {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? 0;
    $category = $_POST['category'] ?? '';
    $status = 'pending'; // default status
    $product_category = $_POST['product_category'] ?? '';
    $city = $_POST['city'] ?? '';
    $image = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_name = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $image_name;
        }
    }

    // Validation 
    if ($name && $description && $price && $category && $product_category && $city && $image) {
        $stmt = $conn->prepare("INSERT INTO products (business_id, name, description, price, category, status, product_category, city, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdsssss", $business_id, $name, $description, $price, $category, $status, $product_category, $city, $image);
        $stmt->execute();
        header("Location: business.php");
        exit();
    } else {
        $error = "Please fill in all required fields.";
    }
}

// Fetch Products for this Business
$stmt = $conn->prepare("SELECT * FROM products WHERE business_id = ?");
$stmt->bind_param("i", $business_id);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch Ratings for Analytics (assuming product_votes table has 'rating' column)
$ratingsData = [];
$stmt = $conn->prepare("SELECT p.id, p.name, AVG(v.rating) as avg_rating, COUNT(v.id) as num_votes FROM products p LEFT JOIN product_votes v ON p.id = v.product_id WHERE p.business_id = ? GROUP BY p.id");
$stmt->bind_param("i", $business_id);
$stmt->execute();
$ratingsResult = $stmt->get_result();
while ($row = $ratingsResult->fetch_assoc()) {
    $ratingsData[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Business Dashboard</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header class="dashboard-header">
        <img src="../images/logo-dark.svg" alt="Logo" class="logo">
        <div class="profile-section">
            <i class="bi bi-person-circle profile-icon"></i>
        </div>
    </header>

    <nav class="dashboard-nav mt-4 d-flex justify-content-center align-items-center">
        <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="listings-tab" data-bs-toggle="tab" data-bs-target="#listings" type="button" role="tab">
                    Products / Services
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ratings-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab">
                    Ratings
                </button>
            </li>
        </ul>
    </nav>
<main class="dashboard-content">
        <div class="tab-content" id="dashboardTabsContent">
            <div class="tab-pane fade show active" id="listings" role="tabpanel">
                <div class="products-top mb-5">
                    <h2 class="mb-4">Products / Services</h2>
                     <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#productModal">
                        <i class="bi bi-plus-lg"></i> Add Products / Services
                    </button>
                </div>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <div class="table-responsive data-table">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Pricing Category</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Product Category</th>
                                <th>City</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                                    <td>£<?php echo htmlspecialchars($product['price']); ?></td>
                                    <td><?php echo htmlspecialchars($product['status']); ?></td>
                                    <td><?php echo htmlspecialchars($product['product_category']); ?></td>
                                    <td><?php echo htmlspecialchars($product['city']); ?></td>
                                    <td>
                                        <!-- Edit/Delete buttons here if needed -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($products)): ?>
                                <tr><td colspan="8">No products/services added yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="analytics" role="tabpanel">
                <h2 class="mb-4 mt-4">Product Ratings</h2>
                <div class="chart-container">
                    <canvas id="analyticsChart"></canvas>
                </div>
            </div>
        </div>
    </main>
  <!-- Add Product Modal -->
    <div id="productModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog d-flex w-100">
            <div class="modal-content w-100">
                <form method="post" enctype="multipart/form-data" class="mt-5 login-form d-flex flex-column" id="productForm">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product or Service Name*</label>
                        <input type="text" name="name" class="form-control input" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description*</label>
                        <textarea name="description" class="form-control input" id="description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price (£)*</label>
                        <input type="number" step="0.01" min="0" name="price" class="form-control input" id="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Pricing Category*</label>
                        <select name="category" id="category" class="form-select input" required>
                            <option value="" disabled selected>Select Pricing Category</option>
                            <option value="budget">Budget</option>
                            <option value="midrange">Midrange</option>
                            <option value="premium">Premium</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="product_category" class="form-label">Product Category*</label>
                        <select name="product_category" id="product_category" class="form-select input" required>
                            <option value="" disabled selected>Select Product Category</option>
                            <option value="beauty">Beauty</option>
                            <option value="food">Food</option>
                            <option value="clothing">Clothing</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="city" class="form-label">City*</label>
                        <input type="text" name="city" class="form-control input" id="city" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Product Image*</label>
                        <input type="file" name="image" class="form-control input" id="image" accept="image/*" required>
                    </div>
                    <button type="submit" name="add_product" class="submit">Add Product</button>
                </form>
            </div>
        </div>
    </div>

    <footer class="dashboard-footer">
        <p>&copy; 2024 My Business. All rights reserved.</p>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const analyticsCtx = document.getElementById('analyticsChart').getContext('2d');
        const analyticsChart = new Chart(analyticsCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($ratingsData, 'name')); ?>,
                datasets: [{
                    label: 'Average Rating',
                    data: <?php echo json_encode(array_map(function($r) { return round($r['avg_rating'] ?? 0, 2); }, $ratingsData)); ?>,
                    backgroundColor: [
                        '#3498DB', '#E67E22', '#2ECC71', '#9B59B6', '#F1C40F', '#E74C3C', '#1ABC9C', '#34495E'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true },
                    title: {
                        display: true,
                        text: 'Average Product Ratings'
                    }
                }
            }
        });
    </script>
</body>
</html>
