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

// Fetch business email
$stmt = $conn->prepare("SELECT email FROM businesses WHERE id = ?");
$stmt->bind_param("i", $business_id);
$stmt->execute();
$stmt->bind_result($business_email);
$stmt->fetch();
$stmt->close();

$error = '';
$success = '';

// Handle Delete Product
if (isset($_POST['delete_product_id'])) {
    $delete_id = intval($_POST['delete_product_id']);
    $stmt = $conn->prepare("SELECT image FROM products WHERE id=? AND business_id=?");
    $stmt->bind_param("ii", $delete_id, $business_id);
    $stmt->execute();
    $stmt->bind_result($image);
    if ($stmt->fetch() && $image && file_exists("../uploads/$image")) {
        unlink("../uploads/$image");
    }
    $stmt->close();

    // Delete from database
    $stmt = $conn->prepare("DELETE FROM products WHERE id=? AND business_id=?");
    $stmt->bind_param("ii", $delete_id, $business_id);
    $stmt->execute();
    $success = "Product deleted successfully.";
}

// Handle Add Product
if (isset($_POST['add_product'])) {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? 0;
    $category = $_POST['category'] ?? '';
    $status = 'pending'; 
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
        $success = "Product added successfully.";
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

// Fetch Votes for Analytics
$stmt = $conn->prepare("
    SELECT p.name, COUNT(v.id) AS vote_count
    FROM products p
    LEFT JOIN product_votes v ON p.id = v.product_id
    WHERE p.business_id = ?
    GROUP BY p.id
    ORDER BY vote_count DESC, p.name ASC
");
$stmt->bind_param("i", $business_id);
$stmt->execute();
$results = $stmt->get_result();

$productNames = [];
$voteCounts = [];
while($row = $results->fetch_assoc()) {
    $productNames[] = $row['name'];
    $voteCounts[] = (int)$row['vote_count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Business Dashboard</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header class="dashboard-header d-flex justify-content-between align-items-center px-4 py-2">
        <img src="../images/logo-dark.svg" alt="Logo" class="logo">
        <div class="profile-section d-flex gap-3">
            <i class="bi bi-person-circle profile-icon"></i>
            <div class="profile-section d-flex flex-column align-items-center">
                <span class="me-3 fw-bold mb-2"><?= htmlspecialchars($business_email) ?></span>
                <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
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
                <button class="nav-link" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab">
                    Analytics
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
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
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
                                <th>Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $product): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['name']); ?></td>
                                    <td><?= htmlspecialchars($product['description']); ?></td>
                                    <td><?= htmlspecialchars($product['category']); ?></td>
                                    <td>£<?= htmlspecialchars($product['price']); ?></td>
                                    <td><?= htmlspecialchars($product['status']); ?></td>
                                    <td><?= htmlspecialchars($product['product_category']); ?></td>
                                    <td><?= htmlspecialchars($product['city']); ?></td>
                                    <td>
                                        <?php if ($product['image']): ?>
                                            <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" alt="Product Image" style="max-width:60px;max-height:60px;">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="../auth/edit_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-warning me-1">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form method="post" action="" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            <input type="hidden" name="delete_product_id" value="<?= $product['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($products)): ?>
                                <tr><td colspan="9">No products/services added yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="analytics" role="tabpanel">
                <h2 class="mb-4 mt-4">Product Votes</h2>
                <div class="mb-4">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Votes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productNames as $i => $name): ?>
                                <tr>
                                    <td><?= htmlspecialchars($name) ?></td>
                                    <td><?= $voteCounts[$i] ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($productNames)): ?>
                                <tr><td colspan="2">No products/services added yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="chart-container">
                    <canvas id="businessVotesChart"></canvas>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('businessVotesChart').getContext('2d');
        const businessVotesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($productNames) ?>,
                datasets: [{
                    label: 'Votes',
                    data: <?= json_encode($voteCounts) ?>,
                    backgroundColor: 'rgba(46, 204, 113, 0.7)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: { display: true, text: 'Votes per Product' }
                }
            }
        });
    </script>
</body>
</html>
