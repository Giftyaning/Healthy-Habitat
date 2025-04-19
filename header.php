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
            <form class="d-flex search " role="search">
                <input class="input search-input form-control me-2 p-2" type="search" placeholder="Search products, brands, and categories" aria-label="Search"/>
                <button class="btn hero-btn" type="submit">Search</button>
            </form>

            <!-- Login & Register Links -->
            <div class="d-flex align-items-center profile">
                <i class="bi bi-person-circle profile-icon"></i>
                    <a href="login.php">LOG IN</a>
                    /
                    <a href="registration.php">REGISTER</a>
            </div>

                <!-- User Logged In -->
            <div class="d-flex align-items-center profile d-none">
                <i class="bi bi-person-circle profile-icon"></i>
                <div class="userLoggedIn">
                    <div class="user-logged-in-name">Gifty Aning</div>
                    <a href="logout.php">LOG OUT</a> 
                </div>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>