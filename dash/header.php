<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar bg-body-tertiary d-flex">
        <div class="container-fluid px-4">
            <!-- Brand Logo -->
            <a class="navbar-brand" href="#"><img src="images/logo-dark.svg" alt=""></a>

            <!-- Search Engine -->
            <form class="d-flex search" role="search">
                <input class="input search-input form-control me-2 p-2" type="search" placeholder="Search products, brands, and categories" aria-label="Search">
                <button class="btn hero-btn" type="submit">Search</button>
            </form>

            <!-- Dynamic Profile Section -->
            <div class="d-flex align-items-center profile">
                <i class="bi bi-person-circle profile-icon"></i>
                <?php if(isset($_SESSION['email'])): ?>
                    <div class="userLoggedIn d-flex align-items-center ms-2">
                        <div class="user-logged-in-name me-2"><?php echo htmlspecialchars($_SESSION['email']); ?></div>
                        <a href="../auth/logout.php" class="logout-link btn btn-sm btn-outline-secondary">LOG OUT</a>
                    </div>
                <?php else: ?>
                    <div class="profile d-flex align-items-center ms-2">
                        <a href="/sign/auth/login.php">LOG IN</a> 
                        /
                        <a href="/sign/auth/signupoption.php">REGISTER</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</body>
</html>
