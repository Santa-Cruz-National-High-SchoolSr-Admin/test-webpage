<?php
session_start();
$conn = new mysqli("localhost", "root", "", "enrollment_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password'])) {
            $_SESSION["admin"] = $admin['username'];
            $_SESSION["login_time"] = time();
            header("Location:dashboard.php");
            exit();
        } else {
            echo "<p style='color:red;'>Invalid password!</p>";
        }
    } else {
        echo "<p style='color:red;'>Admin not found!</p>";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../login.css">
    <link rel="stylesheet" href="login.css">
    <link rel="icon" href="../LOGO STA. CRUZ.png" type="image/x-icon">
</head>
<body>  
    <form method="POST">
        <h2>Admin Login</h2>
        <input type="text" name="username" placeholder="Enter Username" required><br><br>
        <input type="password" name="password" placeholder="Enter Password" required><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
