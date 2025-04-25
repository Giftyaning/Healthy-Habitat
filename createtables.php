<?php

$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . "/healthy/connection.php";

// SQL to create users table
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    gender ENUM('male', 'female', 'other'),
    age_group VARCHAR(50),
    interest VARCHAR(255),
    postcode VARCHAR(20),
    city VARCHAR(100),
    county VARCHAR(100),
    country VARCHAR(100),
    password_hash VARCHAR(255) NOT NULL
)";

// SQL to create businesses table
$sql_businesses = "CREATE TABLE IF NOT EXISTS businesses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    description TEXT,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    address VARCHAR(255),
    postcode VARCHAR(20),
    city VARCHAR(100),
    county VARCHAR(100),
    country VARCHAR(100),
    product_category VARCHAR(255),
    service_category VARCHAR(255),
    password_hash VARCHAR(255) NOT NULL
)";

// Function to execute SQL and handle errors
function executeSQL($conn, $sql, $tableName) {
    if ($conn->query($sql) === TRUE) {
        echo "Table '$tableName' created successfully<br>";
    } else {
        echo "Error creating table '$tableName': " . $conn->error . "<br>";
    }
}

// Execute the SQL statements
executeSQL($conn, $sql_users, 'users');
executeSQL($conn, $sql_businesses, 'businesses');

echo "All tables creation attempts completed. Check for any errors above.";

$conn->close();
?>
