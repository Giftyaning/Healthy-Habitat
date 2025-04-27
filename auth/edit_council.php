<?php
session_start();
include("../database/connection.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch council data
    $sql = "SELECT * FROM councils WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $council = $result->fetch_assoc();

    if (!$council) {
        echo "Council not found.";
        exit();
    }

    // Handle update form submission
    if (isset($_POST['update_council'])) {
        $name = $_POST['name'];
        $county = $_POST['county'];
        $country = $_POST['country'];
        $email = $_POST['email'];

        $sql = "UPDATE councils SET name = ?, county = ?, country = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $county, $country, $email, $id);
        $stmt->execute();

        header("Location: ../dash/admin.php"); 
        exit();
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
    <title>Edit Council</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Council</h2>
        <form method="POST">
            <div class="mb-4">
                <label class="form-label">Council Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($council['name']); ?>" required>
            </div>
            <div class="mb-4">
                <label class="form-label">County</label>
                <input type="text" name="county" class="form-control" value="<?php echo htmlspecialchars($council['county']); ?>" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Country</label>
                <input type="text" name="country" class="form-control" value="<?php echo htmlspecialchars($council['country']); ?>" required>
            </div>
            <div class="mb-5">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($council['email']); ?>" required>
            </div>
            <button type="submit" name="update_council" class="btn hero-btn">Update Council</button>
            <a href="../dash/admin.php" class="btn btn-edit">Cancel</a>
        </form>
    </div>
</body>
</html>
