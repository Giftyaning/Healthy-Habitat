<?php
session_start();
include("connection.php");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        // Check if the email exists in businesses
        $stmt = $conn->prepare("SELECT id, email, password_hash FROM businesses WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $business = $result->fetch_assoc();
            if (password_verify($password, $business['password_hash'])) {
                $_SESSION['business_email'] = $business['email'];
                $_SESSION['business_id'] = $business['id'];
                $_SESSION['account_type'] = 'business'; // Set account type
                header("Location: business.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            // Check if the email exists in users
            $stmt = $conn->prepare("SELECT id, email, password_hash FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password_hash'])) {
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['account_type'] = 'user'; // Set account type
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Invalid password.";
                }
            } else {
                $error = "Account not found.";
            }
        }
        $stmt->close();
    }
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
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <!-- Sign In Form -->
            <form action="login.php" method="post" class="mt-5 login-form d-flex flex-column" id="loginForm" >
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
                    <input type="password" placeholder="Enter Password:" name="password"  class="form-control input" id="password" required>
                </div>
                               
                <input type="submit" value="Login" name="login" class="btn sign w-100 p-3">

                <div class="mt-3 text-center"><p class="mt-2">Don't have an account? <a href="signupoption.php" class="text-decoration-none">Sign up</a></p>
                </div>
            </form>            
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
