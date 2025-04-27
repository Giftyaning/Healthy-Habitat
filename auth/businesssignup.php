<?php
    session_start();
    include("../database/connection.php");

    // Check if the form was submitted
    if (isset($_POST['signup'])) {
        // Get all the info from the form
        $company = $_POST['company'];
        $description = $_POST['description'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $postcode = $_POST['postcode'];
        $city = $_POST['city'];
        $county = $_POST['county'];
        $country = $_POST['country'];
        $product_category = $_POST['product_category'];
        $service_category = $_POST['service_category'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Make sure passwords match!
        if ($password !== $confirm_password) {
            $error = "Passwords do not match!";
        } else {
            // Hide the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Save the info in the database
            $sql = "INSERT INTO businesses (company, description, email, phone, address, postcode, city, county, country, product_category, service_category, password)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssssss", $company, $description, $email, $phone, $address, $postcode, $city, $county, $country, $product_category, $service_category, $hashed_password);

            if ($stmt->execute()) {
                
                header("Location: ../dash/business.php");
                exit();
            } else {
                $error = "Oops! Something went wrong: " . $stmt->error;
            }
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
    <title>Sign Up Page</title>
</head>
<body>
    <div class="container login min-vh-100 d-flex align-items-center justify-content-center">
        <div class="card d-flex">
            
        <!-- Sign Up Form -->
            <form action="" method="post" class="mt-4 login-form d-flex flex-column" id="signupForm">

                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>
                
                <div class="card-header d-flex flex-column align-items-center w-100">
                    <h1 class="form-title">Welcome</h1>
                    <p class="subtitle d-flex justify-content-center align-items-center w-100 px-3">Register your business account</p>
                </div>

                <input type="hidden" name="account_type" value="business">

                <!-- Company Information -->
                <div class="mb-3">
                    <label for="company" class="form-label">Company Name*</label>
                    <input type="text" placeholder="Enter Company Name" name="company" class="form-control input" id="company" required>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Business Description</label>
                    <textarea name="description" class="form-control input" id="description" placeholder="Brief description of your business"></textarea>
                </div>
                
                <!-- Contact Information -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email*</label>
                    <input type="email" placeholder="Enter Email" name="email" class="form-control input" id="email" required>
                </div>
                
                <div class="mb-3">
                    <label for="phone" class="form-label">Contact Number*</label>
                    <input type="tel" placeholder="Enter Phone Number" name="phone" class="form-control input" id="phone" required>
                </div>
                
                <!-- Location Information -->
                <div class="mb-3">
                    <label for="address" class="form-label">Address*</label>
                    <input type="text" placeholder="Enter Street Address" name="address" class="form-control input" id="address" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="postcode" class="form-label">Postcode*</label>
                        <input type="text" placeholder="Enter Postcode" name="postcode" class="form-control input" id="postcode" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="city" class="form-label">City*</label>
                        <input type="text" placeholder="Enter City" name="city" class="form-control input" id="city" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="county" class="form-label">County*</label>
                        <select class="form-select input" name="county" id="county" required>
                            <option value="" selected disabled>Select County</option>
                            <option value="bedfordshire">Bedfordshire</option>
                            <option value="berkshire">Berkshire</option>
                            <option value="bristol">Bristol</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="country" class="form-label">Country*</label>
                        <select class="form-select input" name="country" id="country" required>
                            <option value="" selected disabled>Select Country</option>
                            <option value="uk">United Kingdom</option>
                            <option value="us">United States</option>
                            <option value="canada">Canada</option>
                        </select>
                    </div>
                </div>
                
                <!-- Business Categories -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="product-category" class="form-label">Product Category*</label>
                        <select class="form-select input" name="product_category" id="product-category" required>
                            <option value="" selected disabled>Select Product Category</option>
                            <option value="beauty">Beauty & Wellness</option>
                            <option value="food">Organic Food</option>
                            <option value="home">Eco Home</option>
                            <option value="clothing">Sustainable Clothing</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="service-category" class="form-label">Service Category*</label>
                        <select class="form-select input" name="service_category" id="service-category" required>
                            <option value="" selected disabled>Select Service Category</option>
                            <option value="retail">Retail</option>
                            <option value="wholesale">Wholesale</option>
                            <option value="manufacturer">Manufacturer</option>
                            <option value="consultancy">Consultancy</option>
                        </select>
                    </div>
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
                               
                <input type="submit" value="Create Account" name="signup" class="btn sign w-100 p-3 mt-2">

                <div class="mt-3 text-center">
                    <p class="mt-2">Already have an account? <a href="login.php" class="text-decoration-none">Log in</a></p>
                </div>
            </form>             
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Client-side validation example
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                e.preventDefault();
            }
            
            
        });
    </script>
</body>
</html>