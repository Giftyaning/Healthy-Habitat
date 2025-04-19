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
            <div class="card-header d-flex flex-column align-items-center w-100">
                <h1 class="form-title">Welcome</h1>
                <p class="subtitle d-flex justify-content-center align-items-center w-100 px-3">Log in to access your account and continue shopping securely</p>
            </div>

            <!-- Sign Up Form -->
            <form action="signup.php" method="post" class="mt-4 login-form d-flex flex-column" id="signupForm">
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
                            <option value="other">Other</option>
                            <option value="prefer-not-to-say">Prefer not to say</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="ageGroup" class="form-label">Age Group*</label>
                        <select class="form-select input" name="ageGroup" id="ageGroup" required>
                            <option value="" selected disabled>Select Age Group</option>
                            <option value="18-24">18-24</option>
                            <option value="25-34">25-34</option>
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
                        <label for="county" class="form-label">County*</label>
                        <select class="form-select input" name="county" id="county" required>
                            <option value="" selected disabled>Select County</option>
                            <option value="bedfordshire">Bedfordshire</option>
                            <option value="berkshire">Berkshire</option>
                            <option value="bristol">Bristol</option>
                            <!-- Add more counties as needed -->
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="country" class="form-label">Country*</label>
                    <select class="form-select input" name="country" id="country" required>
                        <option value="" selected disabled>Select Country</option>
                        <option value="uk">United Kingdom</option>
                        <option value="us">United States</option>
                        <option value="canada">Canada</option>
                        <!-- Add more countries as needed -->
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