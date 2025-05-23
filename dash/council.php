<?php
session_start();
include("../database/connection.php");

// Check if council is logged in
if (!isset($_SESSION['council_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$council_id = $_SESSION['council_id'];
$council_email = $_SESSION['council_email'];
$error = "";

// Handle Add Area Form Submission
if (isset($_POST['add_area'])) {
    $city = $_POST['city'];
    $county = $_POST['county'];
    $postcode = $_POST['postcode'];
    $country = $_POST['country'];

    $sql = "INSERT INTO areas (council_id, city, county, postcode, country) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $council_id, $city, $county, $postcode, $country);
    if (!$stmt->execute()) {
        $error = "Failed to add area: " . $conn->error;
    } else {
        header("Location: council.php");
        exit();
    }
}

// Handle Remove Area
if (isset($_POST['remove_area_id'])) {
    $area_id = $_POST['remove_area_id'];
    $stmt = $conn->prepare("DELETE FROM areas WHERE id = ? AND council_id = ?");
    $stmt->bind_param('ii', $area_id, $council_id);
    $stmt->execute();
    header("Location: council.php");
    exit();
}

// Fetch areas managed by the council
$areas = [];
$stmt = $conn->prepare("SELECT * FROM areas WHERE council_id = ?");
$stmt->bind_param("i", $council_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $areas[] = $row;
}
$stmt->close();

$areaCities = array_column($areas, 'city');

// Fetch votes per product for all products in the council's cities
$productNames = [];
$voteCounts = [];
if (count($areaCities) > 0) {
    // Prepare city list
    $cityList = implode("','", array_map([$conn, 'real_escape_string'], $areaCities));
    $sql = "
        SELECT p.name, COUNT(v.id) AS vote_count
        FROM products p
        LEFT JOIN product_votes v ON p.id = v.product_id
        WHERE p.city IN ('$cityList')
        GROUP BY p.id
        ORDER BY vote_count DESC, p.name ASC
    ";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $productNames[] = $row['name'];
        $voteCounts[] = (int)$row['vote_count'];
    }
}
$labelsJson = json_encode($productNames);
$dataJson = json_encode($voteCounts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Council Dashboard</title>
</head>
<body>
    <!-- Dashboard Header -->
    <header class="dashboard-header d-flex justify-content-between align-items-center px-4 py-2">
        <img src="../images/logo-dark.svg" alt="Logo" class="logo">
        <div class="profile-section d-flex gap-3">
            <i class="bi bi-person-circle profile-icon"></i>
            <div class="profile-info d-flex flex-column">
                <span><?php echo htmlspecialchars($council_email); ?></span>
                <a href="../auth/logout.php" class="btn btn-sm btn-outline-secondary mt-1">Logout</a>      
            </div>
        </div>
    </header>
    
    <!-- Dashboard Nav -->
    <nav class="dashboard-nav mt-4 d-flex justify-content-center align-items-center">
        <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab">Product Analytics</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="add-area-tab" data-bs-toggle="tab" data-bs-target="#add-area" type="button" role="tab">Add Area</button>
            </li>
        </ul>
    </nav>
    <!-- Dashboard Content -->
    <main class="dashboard-content">
        <div class="tab-content" id="dashboardTabsContent">
            
            <!-- Analytics Tab -->
            <div class="tab-pane fade show active" id="analytics" role="tabpanel">
                <h2 class="mb-4 mt-4">Votes Per Product (All Areas)</h2>
                <div class="mb-4">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Votes</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (count($productNames) > 0): ?>
                            <?php foreach ($productNames as $i => $name): ?>
                                <tr>
                                    <td><?= htmlspecialchars($name) ?></td>
                                    <td><?= $voteCounts[$i] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="2">No products found for your areas.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="chart-container">
                    <canvas id="analyticsChart"></canvas>
                </div>
            </div>

            <!-- Add Area Tab -->
            <div class="tab-pane fade" id="add-area" role="tabpanel">
                <h2 class="mb-4">Add New Area</h2>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form class="add-area-form d-flex flex-column justify-content-center align-items-center" method="POST" action="council.php">
                    <div class="mb-3 w-100">
                        <label for="city" class="form-label">City</label>
                        <select class="form-select" id="city" name="city" required>
                            <option value="" selected disabled>Select City</option>
                            <option value="London">London</option>
                            <option value="Manchester">Manchester</option>
                            <option value="Birmingham">Birmingham</option>
                            <option value="Leeds">Leeds</option>
                        </select>
                    </div>
                    <div class="mb-3 w-100">
                        <label for="county" class="form-label">County</label>
                        <select class="form-select" id="county" name="county" required>
                            <option value="" selected disabled>Select County</option>
                            <option value="Greater London">Greater London</option>
                            <option value="Greater Manchester">Greater Manchester</option>
                            <option value="West Midlands">West Midlands</option>
                            <option value="West Yorkshire">West Yorkshire</option>
                        </select>
                    </div>
                    <div class="mb-3 w-100">
                        <label for="postcode" class="form-label">Postcode</label>
                        <input type="text" class="form-control" id="postcode" name="postcode" placeholder="e.g. SW1A 1AA" required>
                    </div>
                    <div class="mb-3 w-100">
                        <label for="country" class="form-label">Country</label>
                        <select class="form-select" id="country" name="country" required>
                            <option value="United Kingdom" selected>United Kingdom</option>
                            <option value="United States">United States</option>
                            <option value="Canada">Canada</option>
                        </select>
                    </div>
                    <button type="submit" class="btn hero-btn" name="add_area">Add Area</button>
                </form>

                <!-- Areas Table -->
                <div class="areas-table mt-4">
                    <h3 class="mb-3">Added Areas</h3>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>City</th>
                                    <th>County</th>
                                    <th>Country</th>
                                    <th>Postcode</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (count($areas) > 0): ?>
                                <?php foreach ($areas as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['city']); ?></td>
                                        <td><?php echo htmlspecialchars($row['county']); ?></td>
                                        <td><?php echo htmlspecialchars($row['country']); ?></td>
                                        <td><?php echo htmlspecialchars($row['postcode']); ?></td>
                                        <td>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="remove_area_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5">No areas added yet.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('analyticsChart').getContext('2d');
        const analyticsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= $labelsJson ?>,
                datasets: [{
                    label: 'Votes',
                    data: <?= $dataJson ?>,
                    backgroundColor: 'rgba(52, 152, 219, 0.7)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Votes per Product (All Areas)'
                    }
                }
            }
        });
    </script>
</body>
</html>
