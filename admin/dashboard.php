<?php
session_start();

// Redirect to login page if the admin is NOT logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin/login.php");
    exit();
}

// Auto logout after 15 minutes of inactivity
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > 900)) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
} else {
    $_SESSION['login_time'] = time(); // Reset session time
}

// Connect to the database
$conn = new mysqli("localhost", "root", "", "enrollment_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch student data
$stmt = $conn->prepare("SELECT lrn, first_name, last_name, grade, track, strand, section FROM students ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../LOGO STA. CRUZ.png" type="image/x-icon">
    <title>Admin Dashboard</title>
    <style>
        /* General Page Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #eef2f3, #8e9eab);
            display: flex;
        }

        /* Sidebar with 3D Effect */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: linear-gradient(145deg, #2e2e2e, #3a3a3a);
            color: white;
            position: fixed;
            padding: 20px;
            border-radius: 10px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px;
            margin: 10px 0;
            text-align: center;
            border-radius: 8px;
            transition: all 0.3s ease-in-out;
        }

        .sidebar a:hover {
            background: #444;
            transform: translateY(-2px) scale(1.05);
        }

        /* Main Content */
        .content {
            margin-left: 280px;
            padding: 20px;
            width: calc(100% - 280px);
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: linear-gradient(145deg, #222, #333);
            color: white;
        }

        tr:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        /* Logout Button */
        .btn {
            padding: 8px 12px;
            background: linear-gradient(145deg, #28a745, #23963b);
            color: white;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #1e7a34;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Welcome, <?php echo $_SESSION['admin']; ?> 👤</h2>
        <a href="logout.php">Logout</a>
        <a href="section.php">Sections</a>
        <a href="superadmin_login.php">SuperIdol</a>
    </div>

    <div class="content">
        <h1>Admin Dashboard</h1>
        <input type="text" id="searchBox" placeholder="Search..." onkeyup="searchTable()">
        
        <h2>Student Records</h2>
        <table id="studentTable">
            <thead>
                <tr>
                    <th>LRN</th>
                    <th>Name</th>
                    <th>Grade</th>
                    <th>Track</th>
                    <th>Strand</th>
                    <th>Section</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['lrn']; ?></td>
                        <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
                        <td><?php echo $row['grade']; ?></td>
                        <td><?php echo $row['track']; ?></td>
                        <td><?php echo $row['strand']; ?></td>
                        <td><?php echo $row['section']; ?></td>
                        <td><a href="print.php?lrn=<?php echo $row['lrn']; ?>" class="btn">Print</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
