<?php
session_start();
include("connection.php");

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    //Data collection
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $gender = $_POST['gender'] ?? null;
    $ageGroup = $_POST['ageGroup'] ?? '';
    $interest = $_POST['interest'] ?? '';
    $postcode = trim($_POST['postcode'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $county = trim($_POST['county'] ?? '');
    $country = $_POST['country'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // To validate required fields
    $errors = [];
    if (empty($firstName)) $errors[] = "First name is required";
    if (empty($lastName)) $errors[] = "Last name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($ageGroup)) $errors[] = "Age group is required";
    if (empty($interest)) $errors[] = "Interest is required";
    if (empty($postcode)) $errors[] = "Postcode is required";
    if (empty($city)) $errors[] = "City is required";
    if (empty($county)) $errors[] = "County/State is required";
    if (empty($country)) $errors[] = "Country is required";
    if (empty($password) || strlen($password) < 8) $errors[] = "Password must be at least 8 characters";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match";

    // To check if email already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email is already registered";
        }
        $stmt->close();
    }

    if (!empty($errors)) {
        $error = "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>";
    } else {
        // Hashed password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO users 
            (first_name, last_name, email, gender, age_group, interest, postcode, city, county, country, password_hash)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "sssssssssss",
            $firstName, $lastName, $email, $gender, $ageGroup, $interest, $postcode, $city, $county, $country, $password_hash
        );

        if ($stmt->execute()) {

            $_SESSION['email'] = $email;

            // Page redirection
            header("Location: index.php");
            exit();
        } else {
            $error = "Registration failed: " . htmlspecialchars($stmt->error);
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
    <title>Sign Up Page</title>
</head>
<body>
    <div class="container login min-vh-100 d-flex align-items-center justify-content-center">
        <div class="card d-flex">
            <!-- Error Handling -->
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <!-- Sign Up Form -->
            <form action="" method="post" class="mt-4 login-form d-flex flex-column" id="signupForm">

                <div class="card-header d-flex flex-column align-items-center w-100">
                    <h1 class="form-title">Welcome</h1>
                    <p class="subtitle d-flex justify-content-center align-items-center w-100 px-3">Create your account and start shopping</p>
                </div>

                <!-- Personal Information -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="firstName" class="form-label">First Name*</label>
                        <input type="text" placeholder="Enter First Name" name="firstName" class="form-control input" id="firstName" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lastName" class="form-label">Last Name*</label>
                        <input type="text" placeholder="Enter Last Name" name="lastName" class="form-control input" id="lastName" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address*</label>
                    <input type="email" placeholder="Enter Email" name="email" class="form-control input" id="email" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select input" name="gender" id="gender">
                            <option value="" selected disabled>Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="prefer-not-to-say">Prefer not to say</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="ageGroup" class="form-label">Age Group*</label>
                        <select class="form-select input" name="ageGroup" id="ageGroup" required>
                            <option value="" selected disabled>Select Age Group</option>
                            <option value="18-24">18-24</option>
                            <option value="25-34" >25-34</option>
                            <option value="35-44">35-44</option>
                            <option value="45-54">45-54</option>
                            <option value="55-64">55-64</option>
                            <option value="65+">65+</option>
                        </select>
                    </div>
                </div>
                
                <!-- Interests and Location -->
                <div class="mb-3">
                    <label for="interest" class="form-label">Area of Interest*</label>
                    <select class="form-select input" name="interest" id="interest" required>
                        <option value="" selected disabled>Select Interest</option>
                        <option value="beauty">Beauty & Wellness</option>
                        <option value="nutrition">Nutrition</option>
                        <option value="fitness">Fitness</option>
                        <option value="mindfulness">Mindfulness</option>
                        <option value="sustainability">Sustainability</option>
                        <option value="all">All of the above</option>
                    </select>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="postcode" class="form-label">Postcode*</label>
                        <input type="text" placeholder="Enter Postcode" name="postcode" class="form-control input" id="postcode" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="city" class="form-label">City*</label>
                        <input type="text" placeholder="Enter City" name="city" class="form-control input" id="city" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="county" class="form-label">County/State*</label>
                        <input type="text" placeholder="Enter County/State" name="county" class="form-control input" id="county" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="country" class="form-label">Country*</label>
                    <select class="form-select input" name="country" id="country" required>
                        <option value="" selected disabled>Select Country</option>
                        <option value="uk">United Kingdom</option>
                        <option value="us">United States</option>
                        <option value="canada">Canada</option>
                    </select>
                </div>
                
                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password*</label>
                    <input type="password" placeholder="Create Password (min 8 characters)" name="password" class="form-control input" id="password" minlength="8" required>
                </div>
                
                <div class="mb-3">
                    <label for="confirm-password" class="form-label">Confirm Password*</label>
                    <input type="password" placeholder="Confirm Password" name="confirm_password" class="form-control input" id="confirm-password" minlength="8" required>
                </div>
                               
                <input type="submit" value="Sign Up" name="signup" class="btn sign w-100 p-3 mt-2">

                <div class="mt-3 text-center">
                    <p class="mt-2">Already have an account? <a href="login.php" class="text-decoration-none">Log in</a></p>
                </div>
            </form>             
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>