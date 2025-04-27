<?php
session_start();
include("../database/connection.php");

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$admin_email = $_SESSION['admin_email'];

// Handle council form submission
if (isset($_POST['add_council'])) {
    $name = $_POST['name'];
    $county = $_POST['county'];
    $country = $_POST['country'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO councils (name, county, country, email, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $county, $country, $email, $password);
    $stmt->execute();

    header("Location: ../dash/admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
    <title>Admin Dashboard</title>
</head>
<body>
    <!-- Dashboard Header -->
    <header class="dashboard-header">
        <img src="../images/logo-dark.svg" alt="Logo" class="logo" />
        <div class="profile-section d-flex gap-3">
            <i class="bi bi-person-circle profile-icon"></i>
            <div class="profile-info d-flex flex-column">
                <span><?php echo htmlspecialchars($admin_email); ?></span>
                <a href="../auth/logout.php" class="btn btn-sm">Logout</a>
            </div>
        </div>
    </header>

    <!-- Dashboard Nav -->
    <nav class="dashboard-nav mt-4 d-flex justify-content-center align-items-center">
        <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="listings-tab" data-bs-toggle="tab" data-bs-target="#listings" type="button">Listings</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button">Pending</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="companies-tab" data-bs-toggle="tab" data-bs-target="#companies" type="button">Companies</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="councils-tab" data-bs-toggle="tab" data-bs-target="#councils" type="button">Councils</button>
            </li>
        </ul>
    </nav>

    <!-- Dashboard Content -->
    <main class="dashboard-content">
        <div class="tab-content" id="dashboardTabsContent">

            <!-- Listings Tab -->
            <div class="tab-pane fade show active" id="listings" role="tabpanel">
                <h2 class="mb-4">Approved Products</h2>
                <div class="table-responsive data-table">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Business</th>
                                <th>Price</th>
                                <th>Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT products.*, businesses.company AS business_name 
                                    FROM products 
                                    JOIN businesses ON products.business_id = businesses.id 
                                    WHERE products.status = 'approved'";
                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['business_name']) . "</td>";
                                    echo "<td>£" . htmlspecialchars($row['price']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No approved products.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pending Tab -->
            <div class="tab-pane fade" id="pending" role="tabpanel">
                <h2 class="mb-4">Pending Product Approvals</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Business</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT products.*, businesses.company AS business_name 
                                    FROM products 
                                    JOIN businesses ON products.business_id = businesses.id 
                                    WHERE products.status = 'pending'";
                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['business_name']) . "</td>";
                                    echo "<td>£" . htmlspecialchars($row['price']) . "</td>";
                                    echo "<td>";
                                    // Updated links to auth folder
                                    echo "<a href='../auth/approve.php?id=" . $row['id'] . "' class='btn btn-sm btn-edit me-1'>Approve</a>";
                                    echo "<a href='../auth/decline.php?id=" . $row['id'] . "' class='btn btn-sm btn-delete'>Decline</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No pending products.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Companies Tab -->
            <div class="tab-pane fade" id="companies" role="tabpanel">
                <h2 class="mb-4">Registered Companies</h2>
                <div class="table-responsive data-table">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Company Name</th>
                                <th>Email Address</th>
                                <th>Contact Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM businesses";
                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['company']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>No registered businesses found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Councils Tab -->
            <div class="tab-pane fade" id="councils" role="tabpanel">
                <h2 class="mb-4">Councils</h2>
                <button class="btn btn-add mb-3" data-bs-toggle="modal" data-bs-target="#addCouncilModal">
                    <i class="bi bi-plus-lg"></i> Add Council
                </button>
                <div class="table-responsive data-table">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Council Name</th>
                                <th>County</th>
                                <th>Country</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM councils";
                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['county']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['country']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>No councils found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add Council Modal -->
            <div class="modal fade" id="addCouncilModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Council</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Council Name</label>
                                    <input type="text" name="name" class="form-control" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">County</label>
                                    <input type="text" name="county" class="form-control" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Country</label>
                                    <input type="text" name="country" class="form-control" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required />
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="add_council" class="btn">Add Council</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
