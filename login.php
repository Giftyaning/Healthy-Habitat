<?php 
include("connection.php");
session_start();

if(isset($_POST["login"])) {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Debug
    error_log("Login attempt for: $email");

    $stmt = $conn->prepare("SELECT email, password FROM login WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify hash (or plain text as fallback - remove after testing)
        if(password_verify($password, $user['password']) || $password === $user['password']) {
            $_SESSION['email'] = $email;
            header("Location: index.php");
            exit();
        } else {
            echo '<script>alert("Invalid password"); window.location.href="login.php";</script>';
        }
    } else {
        echo '<script>alert("Email not found"); window.location.href="login.php";</script>';
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <title>Login Page</title>
</head>
<body>
    <div class="container login min-vh-100 d-flex align-items-center justify-content-center">
        <div class="card d-flex">
            <div class="card-header d-flex flex-column align-items-center w-100">
                <h1 class="form-title">Welcome</h1>
                <p class="subtitle d-flex justify-content-center align-items-center w-100 px-3">Log in to access your account and continue shopping securely</p>
            </div>

            <!-- Sign In Form -->
            <form action="login.php" method="post" class="mt-5 login-form d-flex flex-column" id="loginForm" >
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" placeholder="Enter Email:" name="email" class="form-control input" id="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" placeholder="Enter Password:" name="password"  class="form-control input" id="password" required>
                </div>
                               
                <input type="submit" value="Login" name="login" class="btn sign w-100 p-3">

                <div class="mt-3 text-center"><p class="mt-2">Don't have an account? <a href="#" class="text-decoration-none">Sign up</a></p>
                </div>
            </form>            
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>