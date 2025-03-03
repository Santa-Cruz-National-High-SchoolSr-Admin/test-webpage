<?php
session_start();
if (!isset($_SESSION['superadmin'])) {
    header("Location: superadmin_login.php"); // Redirect if not logged in
    exit();
}

$conn = new mysqli("localhost", "root", "", "enrollment_db");

// Handle New Admin Creation (Only Superadmins can do this)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_admin'])) {
    $new_admin = $_POST['username'];
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO admin (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $new_admin, $new_password);

    if ($stmt->execute()) {
        echo "New admin created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Superadmin Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['superadmin']; ?>!</h1>

    <h2>Create New Admin</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="New Admin Username" required>
        <input type="password" name="password" placeholder="New Admin Password" required>
        <button type="submit" name="create_admin">Create Admin</button>
    </form>

    <h2>List of Admins</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Username</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM admin");
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['id']}</td><td>{$row['username']}</td></tr>";
        }
        ?>
    </table>

    <a href="logout.php">Logout</a>
</body>
</html>
