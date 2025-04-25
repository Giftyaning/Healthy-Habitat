<?php
session_start();
include("connection.php");

// Check if the business is logged in
if (!isset($_SESSION['business_email'])) {
    header("Location: login.php");
    exit();
}

$businessEmail = $_SESSION['business_email'];

// Fetch business_id from session
if (isset($_SESSION['business_id'])) {
    $business_id = $_SESSION['business_id'];
} else {
    echo "Error: business_id is not set in the session.";
    exit();
}

// Handle actions based on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add product
    if (isset($_POST['add_product'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $size = trim($_POST['size']);
        $health_benefits = trim($_POST['health_benefits']);
        $pricing_category = $_POST['pricing_category'];
        $price = floatval($_POST['price']);
        $certificate = null;

        // Handle certificate upload if provided
        if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileTmpName = $_FILES['certificate']['tmp_name'];
            $fileName = $_FILES['certificate']['name'];
            $fileSize = $_FILES['certificate']['size'];
            $fileType = $_FILES['certificate']['type'];

            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
            if (in_array($fileType, $allowedTypes) && $fileSize <= 2000000) {
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $certificate = uniqid() . '.' . $fileExt;
                move_uploaded_file($fileTmpName, $uploadDir . $certificate);
            } else {
                echo "Error: Invalid file type or size. PDF, JPG, and PNG files are allowed, max 2MB.";
                $certificate = null;
            }
        }

        // Insert into database using MySQLi prepared statement
        $stmt = $conn->prepare("INSERT INTO products (business_id, name, description, size, health_benefits, pricing_category, price, certificate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssds", $business_id, $name, $description, $size, $health_benefits, $pricing_category, $price, $certificate);
        $stmt->execute();
        $stmt->close();

        // Refresh to show the new product
        header("Location: business.php");
        exit();
    }
    // Edit Submission
    if (isset($_POST['edit_product'])) {
        $edit_product_id = $_POST['edit_product_id'];
        $edit_name = $_POST['edit_name'];
        $edit_description = $_POST['edit_description'];
        $edit_size = $_POST['edit_size'];
        $edit_health_benefits = $_POST['edit_health_benefits'];
        $edit_pricing_category = $_POST['edit_pricing_category'];
        $edit_price = $_POST['edit_price'];

        // Update product in the database
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, size=?, health_benefits=?, pricing_category=?, price=? WHERE id=? AND business_id=?");
        $stmt->bind_param("sssssdsi", $edit_name, $edit_description, $edit_size, $edit_health_benefits, $edit_pricing_category, $edit_price, $edit_product_id, $business_id);
        $stmt->execute();
        $stmt->close();
        header("Location: business.php");
        exit();
    }
    // Delete product
    if (isset($_POST['delete_product_id'])) {
        $delete_id = intval($_POST['delete_product_id']);
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND business_id = ?");
        $stmt->bind_param("ii", $delete_id, $business_id);
        $stmt->execute();
        $stmt->close();
        header("Location: business.php");
        exit();
    }
}

// Fetch products for this business
$stmt = $conn->prepare("SELECT * FROM products WHERE business_id = ?");
$stmt->bind_param("i", $business_id);
$stmt->execute();
$result = $stmt->get_result();
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
$stmt->close();

$hasProducts = count($products) > 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Business Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
        }
    </style>
</head>

<body>
    <header class="dashboard-header">
        <img src="images/logo-dark.svg" alt="Logo" class="logo">
        <div class="profile-section">
            <i class="bi bi-person-circle profile-icon"></i>
            <div class="userLoggedIn">
                <div class="user-logged-in-name"><?php echo htmlspecialchars($businessEmail); ?></div>
                <a href="logout.php" class="logout-link">LOG OUT</a>
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
                <button class="nav-link<?php echo !$hasProducts ? ' disabled text-muted' : ''; ?>" id="ratings-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab" <?php echo !$hasProducts ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
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
                    <button class="btn-add" id="addProductButton">Add Product / Services</button>
                </div>
                <div class="table-responsive data-table">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Pricing Category</th>
                                <th>Price</th>
                                <th>Certificate</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)) : ?>
                                <tr>
                                    <td colspan="6">Add products or services</td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($products as $product) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td><?php echo htmlspecialchars($product['description']); ?></td>
                                        <td><?php echo htmlspecialchars($product['pricing_category']); ?></td>
                                        <td>£<?php echo number_format($product['price'], 2); ?></td>
                                        <td>
                                            <?php if ($product['certificate']) : ?>
                                                <a href="../uploads/<?php echo htmlspecialchars($product['certificate']); ?>" target="_blank">View</a>
                                            <?php else : ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button
                                                class="btn btn-sm btn-primary edit-btn"
                                                data-id="<?php echo $product['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                                data-description="<?php echo htmlspecialchars($product['description']); ?>"
                                                data-size="<?php echo htmlspecialchars($product['size']); ?>"
                                                data-health_benefits="<?php echo htmlspecialchars($product['health_benefits']); ?>"
                                                data-pricing_category="<?php echo htmlspecialchars($product['pricing_category']); ?>"
                                                data-price="<?php echo htmlspecialchars($product['price']); ?>">
                                                Edit
                                            </button>
                                            <form method="post" action="" style="display:inline;">
                                                <input type="hidden" name="delete_product_id" value="<?php echo $product['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?');">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form method="post" enctype="multipart/form-data" class="mt-5 login-form d-flex flex-column" id="productForm">
                <div class="card-header d-flex flex-column align-items-center w-100">
                    <h1 class="form-title">Sell Faster!</h1>
                    <p class="subtitle d-flex justify-content-center align-items-center w-100 px-3">
                        Boost your sales by adding your products / services to our marketplace in minutes.
                    </p>
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Product or Service Name*</label>
                    <input type="text" placeholder="Product or Service name" name="name" class="form-control input" id="name" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description*</label>
                    <textarea name="description" class="form-control input" id="description" placeholder="Brief description of your product or service" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="size" class="form-label">Size (if applicable)</label>
                    <input type="text" placeholder="Size (e.g., 500ml, Large, 10-pack)" name="size" class="form-control input" id="size">
                </div>
                <div class="mb-3">
                    <label for="health_benefits" class="form-label">Health Benefits*</label>
                    <textarea name="health_benefits" class="form-control input" id="health_benefits" placeholder="List health benefits" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="pricing_category" class="form-label">Pricing Category*</label>
                    <select name="pricing_category" id="pricing_category" class="form-select input" required>
                        <option value="" disabled selected>Select Pricing Category</option>
                        <option value="budget">Budget</option>
                        <option value="midrange">Midrange</option>
                        <option value="premium">Premium</option>
                    </select>
                </div>
                <label for="price" class="form-label">Price (£)*</label>
                <input type="number" step="0.01" min="0" name="price" class="form-control input" id="price" required>
                <div class="mb-3">
                    <label for="certificate" class="form-label">Certification (optional)</label>
                    <input type="file" name="certificate" class="form-control input" id="certificate" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="form-text text-muted">Upload a certificate file (PDF, JPG, PNG).</small>
                </div>
                <input type="submit" value="Add Product / Service" name="add_product" class="btn sign w-100 p-3">
            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <span class="close edit-close">&times;</span>
            <form method="post" enctype="multipart/form-data" class="mt-5 login-form d-flex flex-column" id="editProductForm">
                <input type="hidden" name="edit_product_id" id="edit_product_id">
                <div class="mb-3">
                    <label for="edit_name" class="form-label">Product or Service Name*</label>
                    <input type="text" placeholder="Product or Service name" name="edit_name" class="form-control input" id="edit_name" required>
                </div>
                <div class="mb-3">
                    <label for="edit_description" class="form-label">Description*</label>
                    <textarea name="edit_description" class="form-control input" id="edit_description" placeholder="Brief description of your product or service" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="edit_size" class="form-label">Size (if applicable)</label>
                    <input type="text" placeholder="Size (e.g., 500ml, Large, 10-pack)" name="edit_size" class="form-control input" id="edit_size">
                </div>
                <div class="mb-3">
                    <label for="edit_health_benefits" class="form-label">Health Benefits*</label>
                    <textarea name="edit_health_benefits" class="form-control input" id="edit_health_benefits" placeholder="List health benefits" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="edit_pricing_category" class="form-label">Pricing Category*</label>
                    <select name="edit_pricing_category" id="edit_pricing_category" class="form-select input" required>
                        <option value="" disabled selected>Select Pricing Category</option>
                        <option value="budget">Budget</option>
                        <option value="midrange">Midrange</option>
                        <option value="premium">Premium</option>
                    </select>
                </div>
                <label for="edit_price" class="form-label">Price (£)*</label>
                <input type="number" step="0.01" min="0" name="edit_price" class="form-control input" id="edit_price" required>
                <input type="submit" value="Update Product / Service" name="edit_product" class="btn sign w-100 p-3">
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Get the modals
        var addProductModal = document.getElementById("productModal");
        var editProductModal = document.getElementById("editProductModal");

        // Get the buttons that open the modals
        var addProductButton = document.getElementById("addProductButton");

        // Get the <span> elements that close the modals
        var addProductClose = document.querySelector("#productModal .close");
        var editProductClose = document.querySelector("#editProductModal .close");

        // Function to open the add product modal
        addProductButton.onclick = function() {
            addProductModal.style.display = "block";
        }

        // Function to close the add product modal
        addProductClose.onclick = function() {
            addProductModal.style.display = "none";
        }

        // Function to close the edit product modal
        editProductClose.onclick = function() {
            editProductModal.style.display = "none";
        }

        // Function to close the modal if the user clicks outside of it
        window.onclick = function(event) {
            if (event.target == addProductModal) {
                addProductModal.style.display = "none";
            } else if (event.target == editProductModal) {
                editProductModal.style.display = "none";
            }
        }

        // To edit product logic
        document.querySelectorAll('.edit-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                var productId = this.getAttribute('data-id');
                var productName = this.getAttribute('data-name');
                var productDescription = this.getAttribute('data-description');
                var productSize = this.getAttribute('data-size');
                var productHealthBenefits = this.getAttribute('data-health_benefits');
                var productPricingCategory = this.getAttribute('data-pricing_category');
                var productPrice = this.getAttribute('data-price');

                // To Set product ID in the form
                document.getElementById('edit_product_id').value = productId;
                document.getElementById('edit_name').value = productName;
                document.getElementById('edit_description').value = productDescription;
                document.getElementById('edit_size').value = productSize;
                document.getElementById('edit_health_benefits').value = productHealthBenefits;
                document.getElementById('edit_pricing_category').value = productPricingCategory;
                document.getElementById('edit_price').value = productPrice;

                // To show the modal
                editProductModal.style.display = 'block';
            });
        });

        // Chart.js (placeholder)
        const ctx = document.getElementById('analyticsChart').getContext('2d');
        const analyticsChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Yoga Mats', 'Organic Fruits', 'Eco Cleaning', 'Sustainable Clothing', 'Mindfulness Books'],
                datasets: [{
                    data: [35, 25, 15, 15, 10],
                    backgroundColor: [
                        '#00A251',
                        '#2ECC71',
                        '#3498DB',
                        '#9B59B6',
                        '#F1C40F'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right'
                    },
                    title: {
                        display: true,
                        text: 'Product Category Distribution',
                        font: {
                            size: 16
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>
