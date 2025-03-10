<?php
// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', '/workspaces/test-webpage/admin/error_log.txt');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Database connection
    $conn = new mysqli('localhost', 'username', 'password', 'database');
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        header("Location: login.html?error=server_error");
        exit();
    }
    
    $sql = "SELECT * FROM admin_users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        header("Location: login.html?error=server_error");
        exit();
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            session_start();
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            $_SESSION['login_time'] = time(); // Track login time
            header("Location: dashboard.php");
            $stmt->close();
            $conn->close();
            exit();
        } else {
            error_log("Password verification failed for user: $username");
        }
    } else {
        error_log("No user found with username: $username");
    }
    
    $stmt->close();
    $conn->close();
    header("Location: login.html?error=invalid_credentials");
    exit();
} else {
    // Method not allowed
    header("HTTP/1.1 405 Method Not Allowed");
    exit();
}
?>
