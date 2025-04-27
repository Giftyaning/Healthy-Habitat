<?php
session_start();
include("../database/connection.php");

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check users table
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    $userType = '';
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $userType = 'user';
    } else {
        // Check businesses table
        $sql = "SELECT * FROM businesses WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $userType = 'business';
        } else {
            // Check admins table
            $sql = "SELECT * FROM admins WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                $userType = 'admin';
            } else {
                // Check councils table
                $sql = "SELECT * FROM councils WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows === 1) {
                    $row = $result->fetch_assoc();
                    $userType = 'council';
                }
            }
        }
    }

    if ($userType) {
        if (password_verify($password, $row['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['user_type'] = $userType;

            // Also set specific session variable for councils and admins
            if ($userType === 'business') {
                $_SESSION['business_id'] = $row['id'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['user_type'] = $userType;
                header("Location: ../dash/business.php");
                exit();
            } elseif ($userType === 'admin') {
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['admin_email'] = $row['email'];
                header("Location: ../dash/admin.php");
            } elseif ($userType === 'council') {
                $_SESSION['council_id'] = $row['id'];
                $_SESSION['council_email'] = $row['email'];
                header("Location: ../dash/council.php");
            } else {
                header("Location: ../index.php");
            }
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "Account not found!";
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <title>Login Page</title>
</head>
<body>
    <div class="container login min-vh-100 d-flex align-items-center justify-content-center">
        <div class="card d-flex">
            
        <!-- Sign In Form -->
            <form action="login.php" method="post" class="mt-5 login-form d-flex flex-column" id="loginForm">

                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>

                <div class="card-header d-flex flex-column align-items-center w-100">
                    <h1 class="form-title">Welcome</h1>
                    <p class="subtitle d-flex justify-content-center align-items-center w-100 px-3">Log in to access your account and continue shopping securely</p>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" placeholder="Enter Email:" name="email" class="form-control input" id="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" placeholder="Enter Password:" name="password" class="form-control input" id="password" required>
                </div>
                               
                <input type="submit" value="Login" name="login" class="btn sign w-100 p-3">

                <div class="mt-3 text-center">
                    <p class="mt-2">Don't have an account? <a href="signupoption.php" class="text-decoration-none">Sign up</a></p>
                </div>
            </form>            
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Basic client-side validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                alert('Please fill in both email and password fields');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>