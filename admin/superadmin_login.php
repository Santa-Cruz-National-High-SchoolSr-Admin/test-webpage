    <?php
    session_start();
    $conn = new mysqli("localhost", "root", "", "enrollment_db");

    // Check for database connection error
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Query the 'accounts' table instead of 'admin'
        $sql = "SELECT * FROM accounts WHERE username = ? AND role = 'superadmin' LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['superadmin'] = $row['username']; // Store superadmin session
                $_SESSION['role'] = $row['role'];
                header("Location: superadmin_dashboard.php"); // Redirect to dashboard
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    }
?>  

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyberpunk 8K Login</title>
    <link rel="stylesheet" href="superadmin_loginstyle.css">
</head>
<body>

    <div class="login-container">
        <h2>Welcome to Ultra Admin Login</h2>
        <form>
            <div class="input-group">
                <input type="text" required>
                <label>Username</label>
            </div>
            <div class="input-group">
                <input type="password" required>
                <label>Password</label>
            </div>
            <button type="submit" class="btn">Enter to Proceed</button>
        </form>
    </div>

</body>
</html>
