<?php 
    $host = "localhost";
    $username = "root";
    $password = "";
    $db_name = "healthy_network";

    // Create connection
    $conn = new mysqli($host, $username, $password, $db_name);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset
    if (!$conn->set_charset("utf8mb4")) {
        printf("Error loading character set utf8mb4: %s\n", $conn->error);
    }
    
    // Connection successful
    echo "Connected successfully";
?>