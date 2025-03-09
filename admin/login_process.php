<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM admin_users WHERE username = ?";
    $stmt = $conn->prepare($sql);
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
            exit();
        }
    }
    
    header("Location: login.html?error=invalid_credentials");
    exit();
} else {
    // Method not allowed
    header("HTTP/1.1 405 Method Not Allowed");
    exit();
}
?>
