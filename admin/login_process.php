<?php
header("Content-Type: text/html; charset=UTF-8");
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
            header("Location: dashboard.php");
            $stmt->close();
            exit();
        } else {
            $_SESSION['error'] = "Invalid password!";
            $stmt->close();
            header("Location: login.html");
            exit();
        }
    } else {
        $_SESSION['error'] = "Admin not found!";
        $stmt->close();
        header("Location: login.html");
        exit();
    }
}

$conn->close();
?>
