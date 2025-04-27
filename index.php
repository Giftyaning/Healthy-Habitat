<?php
session_start();
include("database/connection.php");

// Get logged-in user ID
$user_id = $_SESSION['user_id'] ?? null;

// Fetch approved products and votes
function getProducts($conn, $limit = 4) {
    global $user_id;
    $sql = "SELECT p.*, 
                   COUNT(v.id) as votes,
                   SUM(CASE WHEN v.user_id = ? THEN 1 ELSE 0 END) as user_voted
            FROM products p
            LEFT JOIN product_votes v ON p.id = v.product_id
            WHERE p.status = 'approved'
            GROUP BY p.id
            ORDER BY RAND()
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$newProducts = getProducts($conn, 4);
$popularProducts = getProducts($conn, 4);
$springDeals = getProducts($conn, 4);

// For Pie Chart: Get all products and their vote counts
$chartData = [];
$stmt = $conn->prepare("SELECT p.id, p.name, COUNT(v.id) as votes
                        FROM products p
                        LEFT JOIN product_votes v ON p.id = v.product_id
                        WHERE p.status = 'approved'
                        GROUP BY p.id
                        ORDER BY p.name");
$stmt->execute();
$result = $stmt->get_result();
while($row = $result->fetch_assoc()) {
    $chartData[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <title>Natural Habitat Network</title>
    <style>
        .vote-btn.voted .bi-hand-thumbs-up {
            color: #00a251 !important;
        }
        .vote-btn .bi-hand-thumbs-up {
            transition: color 0.2s;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <header class="header"> 
        <?php include("dash/header.php") ?>
    </header>

    <section class="hero">
        <div class="hero-image-container">
            <img src="images/hero-img.svg" class="hero-image" alt="Healthy Habitat">
        </div>
    </section>

    <section class="hero-products">
        <div class="container">

            <!-- New Products -->
            <div class="products">
                <h1 class="product-title">New Products</h1>
                <div class="row">
                    <?php foreach ($newProducts as $product): ?>
                    <div class="col-md-3 col-6">
                        <div class="product-card">
                            <div class="product-card-title text-dark"><?= htmlspecialchars($product['name']) ?></div>
                            <div class="product-img-container d-flex align-items-center justify-content-center">
                                <img src="uploads/<?= htmlspecialchars($product['image']) ?>" class="product-img" alt="<?= htmlspecialchars($product['name']) ?>">
                            </div>
                            <div class="product-body d-flex p-2">
                                <div class="product-info">
                                    <p class="product-price text-dark">£<?= number_format($product['price'], 2) ?></p>
                                    <button class="btn hero-btn">Buy now</button>
                                </div>
                                <div class="shop d-flex flex-column">
                                    <div class="shop-opt d-flex">
                                        <a href="#" class="vote-btn<?= $product['user_voted'] ? ' voted' : '' ?>" data-id="<?= $product['id'] ?>" <?= $user_id ? '' : 'data-disabled="1"' ?>>
                                            <i class="bi bi-hand-thumbs-up shop-icon"></i>
                                            <span class="vote-count"><?= $product['votes'] ?></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Popular Purchases -->
            <div class="products">
                <h1 class="product-title">Popular Purchases</h1>
                <div class="row">
                    <?php foreach ($popularProducts as $product): ?>
                    <div class="col-md-3 col-6">
                        <div class="product-card">
                            <div class="product-card-title text-dark"><?= htmlspecialchars($product['name']) ?></div>
                            <div class="product-img-container d-flex align-items-center justify-content-center">
                                <img src="uploads/<?= htmlspecialchars($product['image']) ?>" class="product-img" alt="<?= htmlspecialchars($product['name']) ?>">
                            </div>
                            <div class="product-body d-flex p-2">
                                <div class="product-info">
                                    <p class="product-price text-dark">£<?= number_format($product['price'], 2) ?></p>
                                    <button class="btn hero-btn">Buy now</button>
                                </div>
                                <div class="shop d-flex flex-column">
                                    <div class="shop-opt d-flex">
                                        <a href="#" class="vote-btn<?= $product['user_voted'] ? ' voted' : '' ?>" data-id="<?= $product['id'] ?>" <?= $user_id ? '' : 'data-disabled="1"' ?>>
                                            <i class="bi bi-hand-thumbs-up shop-icon"></i>
                                            <span class="vote-count"><?= $product['votes'] ?></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Spring Deals -->
            <div class="products">
                <h1 class="product-title">Spring Deals</h1>
                <div class="row">
                    <?php foreach ($springDeals as $product): ?>
                    <div class="col-md-3 col-6">
                        <div class="product-card">
                            <div class="product-card-title text-dark"><?= htmlspecialchars($product['name']) ?></div>
                            <div class="product-img-container d-flex align-items-center justify-content-center">
                                <img src="uploads/<?= htmlspecialchars($product['image']) ?>" class="product-img" alt="<?= htmlspecialchars($product['name']) ?>">
                            </div>
                            <div class="product-body d-flex p-2">
                                <div class="product-info">
                                    <p class="product-price text-dark">£<?= number_format($product['price'], 2) ?></p>
                                    <button class="btn hero-btn">Buy now</button>
                                </div>
                                <div class="shop d-flex flex-column">
                                    <div class="shop-opt d-flex">
                                        <a href="#" class="vote-btn<?= $product['user_voted'] ? ' voted' : '' ?>" data-id="<?= $product['id'] ?>"<?= $user_id ? '' : 'data-disabled="1"' ?>>
                                            <i class="bi bi-hand-thumbs-up shop-icon"></i>
                                            <span class="vote-count"><?= $product['votes'] ?></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <footer class="footer"></footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Voting system
        document.querySelectorAll('.vote-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                if (btn.dataset.disabled === "1") {
                    alert('You must be logged in to vote!');
                    return;
                }
                if (btn.classList.contains('voted')) {
                    alert('You already voted for this product!');
                    return;
                }
                const productId = btn.dataset.id;
                const voteCount = btn.querySelector('.vote-count');
                try {
                    const response = await fetch('vote.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `product_id=${productId}`
                    });
                    const data = await response.json();
                    if(data.success) {
                        voteCount.textContent = parseInt(voteCount.textContent) + 1;
                        btn.classList.add('voted');
                        updatePieChart(productId);
                    } else {
                        alert(data.message || 'You can only vote once per product!');
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            });
        });

        // Update Pie Chart Live
        function updatePieChart(productId) {
            
            const idx = chartProductIds.indexOf(productId);
            if (idx !== -1) {
                ratingsPieChart.data.datasets[0].data[idx]++;
                ratingsPieChart.update();
            }
        }
    </script>
</body>
</html>
