<?php
session_start();
include("database/connection.php");

// CSRF token for security
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

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
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    foreach ($products as &$product) {
        $product['user_voted'] = (int)$product['user_voted'];
    }
    return $products;
}

$newProducts = getProducts($conn, 4);
$popularProducts = getProducts($conn, 4);
$springDeals = getProducts($conn, 4);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <title>Natural Habitat Network</title>
    <style>
        .vote-btn {
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
        .vote-btn:hover {
            background-color: #ddd;
        }
        .vote-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .vote-btn.voted {
            background-color: #00a251;
            color: white;
            border: 1px solid #00a251;
        }
    </style>
</head>
<body>
    <!-- Navbar/Header -->
    <nav class="navbar bg-body-tertiary d-flex">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="#"><img src="images/logo-dark.svg" alt=""></a>
            <form class="d-flex search" role="search" method="GET" action="/healthy-habitat-network/auth/search.php">
                <input class="input search-input form-control me-2 p-2" type="search" name="q" placeholder="Search products, brands, and categories" aria-label="Search">
                <button class="btn hero-btn" type="submit">Search</button>
            </form>
            <div class="d-flex align-items-center profile">
                <i class="bi bi-person-circle profile-icon"></i>
                <?php if(isset($_SESSION['email'])): ?>
                <div class="userLoggedIn d-flex align-items-center ms-2" id="user-logged-in-section">
                    <div class="user-logged-in-name me-2"><?= htmlspecialchars($_SESSION['email']); ?></div>
                    <a href="#" id="logout-btn" class="logout-link btn btn-sm btn-outline-secondary">LOG OUT</a>
                </div>
                <?php else: ?>
                <div class="profile d-flex align-items-center ms-2" id="login-register-section">
                    <a href="/healthy-habitat-network/auth/login.php">LOG IN</a>
                    /
                    <a href="/healthy-habitat-network/auth/signupoption.php">REGISTER</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
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
                                <img src="/healthy-habitat-network/uploads/<?= htmlspecialchars($product['image']) ?>"
                                    class="product-img" alt="<?= htmlspecialchars($product['name']) ?>">
                            </div>
                            <div class="product-body d-flex p-2">
                                <div class="product-info">
                                    <p class="product-price text-dark">£<?= number_format($product['price'], 2) ?></p>
                                    <button class="btn hero-btn">Buy now</button>
                                </div>
                                <div class="shop d-flex justify-content-center align-items-center">
                                    <button type="button"
                                        class="vote-btn btn-edit text-dark p-1<?= $product['user_voted'] ? ' voted' : '' ?>"
                                        data-id="<?= $product['id'] ?>"
                                        <?= ($user_id && !$product['user_voted']) ? '' : 'disabled' ?>>
                                        Rate
                                    </button>
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
                                <img src="/healthy-habitat-network/uploads/<?= htmlspecialchars($product['image']) ?>"
                                    class="product-img" alt="<?= htmlspecialchars($product['name']) ?>">
                            </div>
                            <div class="product-body d-flex p-2">
                                <div class="product-info">
                                    <p class="product-price text-dark">£<?= number_format($product['price'], 2) ?></p>
                                    <button class="btn hero-btn">Buy now</button>
                                </div>
                                <div class="shop d-flex justify-content-center align-items-center">
                                    <button type="button"
                                        class="vote-btn btn-edit text-dark p-1<?= $product['user_voted'] ? ' voted' : '' ?>"
                                        data-id="<?= $product['id'] ?>"
                                        <?= ($user_id && !$product['user_voted']) ? '' : 'disabled' ?>>
                                        Rate
                                    </button>
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
                                <img src="/healthy-habitat-network/uploads/<?= htmlspecialchars($product['image']) ?>"
                                    class="product-img" alt="<?= htmlspecialchars($product['name']) ?>">
                            </div>
                            <div class="product-body d-flex p-2">
                                <div class="product-info">
                                    <p class="product-price text-dark">£<?= number_format($product['price'], 2) ?></p>
                                    <button class="btn hero-btn">Buy now</button>
                                </div>
                                <div class="shop d-flex justify-content-center align-items-center">
                                    <button type="button"
                                        class="vote-btn btn-edit text-dark p-1<?= $product['user_voted'] ? ' voted' : '' ?>"
                                        data-id="<?= $product['id'] ?>"
                                        <?= ($user_id && !$product['user_voted']) ? '' : 'disabled' ?>>
                                        Rate
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Voting system
            document.querySelectorAll('.vote-btn').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.preventDefault();

                    if (btn.hasAttribute('disabled')) {
                        alert('You must be logged in to vote!');
                        return;
                    }

                    const productId = btn.dataset.id;

                    try {
                        const response = await fetch('/healthy-habitat-network/auth/vote.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `product_id=${encodeURIComponent(productId)}&csrf_token=<?= $csrf_token ?>`
                        });

                        let data;
                        try {
                            data = await response.json();
                        } catch (jsonError) {
                            const text = await response.text();
                            console.error('Non-JSON response:', text);
                            alert('Server error. Please contact support.');
                            return;
                        }

                        if (data.success) {
                            btn.classList.add('voted');
                            btn.setAttribute('disabled', 'disabled');
                        } else {
                            alert(data.message || 'You can only vote once per product!');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    }
                });
            });

            // AJAX logout
            var logoutBtn = document.getElementById('logout-btn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    fetch('/healthy-habitat-network/auth/logout.php', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (response.ok) {
                                var loggedInSection = document.getElementById('user-logged-in-section');
                                if (loggedInSection) loggedInSection.remove();
                                var profileDiv = document.querySelector('.profile');
                                if (profileDiv && !document.getElementById('login-register-section')) {
                                    var loginDiv = document.createElement('div');
                                    loginDiv.className = 'profile d-flex align-items-center ms-2';
                                    loginDiv.id = 'login-register-section';
                                    loginDiv.innerHTML = `
                                    <a href="/healthy-habitat-network/auth/login.php">LOG IN</a> 
                                    /
                                    <a href="/healthy-habitat-network/auth/signupoption.php">REGISTER</a>
                                `;
                                    profileDiv.appendChild(loginDiv);
                                }
                            }
                        });
                });
            }
        });
    </script>
</body>
</html>
