<?php
session_start();
$conn = new mysqli("localhost", "root", "", "enrollment_db");

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<p style='color:red;'>Passwords do not match!</p>";
    } else {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if username already exists
        $check_stmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            echo "<p style='color:red;'>Username already exists. Choose another one.</p>";
        } else {
            // Insert new admin into the database
            $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {
                echo "<p style='color:green;'>Account created successfully! <a href='login.php'>Login here</a></p>";
            } else {
                echo "<p style='color:red;'>Error creating account.</p>";
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="login.css">
    <link rel="icon" href="LOGO STA. CRUZ.png" type="image/x-icon">
</head>
<body>
    <form method="POST">
        <h2>Admin Registration</h2>
        <input type="text" name="username" placeholder="Enter Username" required><br><br>
        <input type="password" name="password" placeholder="Enter Password" required><br><br>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required><br><br>
        <input type="submit" value="Register">
        <p>Already have an account? <a href="login.php">Login here</a></p>

    </form>
</body>
</html>
