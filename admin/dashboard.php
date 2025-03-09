<?php
session_start();

// Redirect to login page if the admin is NOT logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.html");
    exit();
}

// Auto logout after 15 minutes of inactivity
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > 900)) {
    session_unset();
    session_destroy();
    header("Location: login.html");
    exit();
} else {
    $_SESSION['login_time'] = time(); // Reset session time
}

require_once 'config.php'; // Use existing database connection

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch student data
$stmt = $conn->prepare("SELECT lrn, first_name, last_name, grade, track, strand, section FROM students ORDER BY created_at DESC");
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="LOGO STA. CRUZ.png" type="image/x-icon">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        /* Base Variables */
        :root {
            --sidebar-width: 300px;
            --primary-gradient: linear-gradient(145deg, #2e2e2e, #3a3a3a);
            --content-gradient: linear-gradient(to right, #eef2f3, #8e9eab);
            --animation-speed: 0.3s;
            --shadow-color: rgba(0, 0, 0, 0.15);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { transform: translateX(-20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: var(--content-gradient);
            display: flex;
            min-height: 100vh;
            font-size: 16px;
            color: #333;
            line-height: 1.6;
        }

        /* 4K Resolution Scaling */
        @media (min-width: 2560px) {
            :root { --sidebar-width: 400px; }
            body { font-size: 22px; }
        }

        @media (min-width: 3840px) {
            :root { --sidebar-width: 500px; }
            body { font-size: 26px; }
        }

        /* Sidebar Enhancements */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--primary-gradient);
            color: white;
            position: fixed;
            padding: 2em;
            box-shadow: 5px 0 20px var(--shadow-color);
            animation: slideIn var(--animation-speed) ease-out;
            backdrop-filter: blur(10px);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 1.5em;
            font-size: 1.8em;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            letter-spacing: 1px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 1.2em;
            margin: 1em 0;
            text-align: center;
            border-radius: 12px;
            transition: all var(--animation-speed) ease-in-out;
            background: rgba(255, 255, 255, 0.1);
            font-size: 1.1em;
            letter-spacing: 0.5px;
            backdrop-filter: blur(5px);
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        /* Content Area */
        .content {
            margin-left: calc(var(--sidebar-width) + 30px);
            padding: 2.5em;
            width: calc(100% - var(--sidebar-width) - 60px);
            animation: fadeIn var(--animation-speed) ease-out;
        }

        .content h1 {
            font-size: 3em;
            margin-bottom: 1em;
            color: #2c3e50;
            font-weight: 600;
            letter-spacing: -1px;
        }

        /* Search Box */
        #searchBox {
            width: 100%;
            padding: 1.2em;
            margin-bottom: 2.5em;
            border: none;
            border-radius: 15px;
            font-size: 1.2em;
            box-shadow: 0 5px 15px var(--shadow-color);
            transition: all var(--animation-speed) ease;
        }

        #searchBox:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px var(--shadow-color);
            outline: none;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 25px var(--shadow-color);
            animation: fadeIn var(--animation-speed) ease-out;
        }

        th, td {
            padding: 1.4em;
            text-align: left;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }

        th {
            background: var(--primary-gradient);
            color: white;
            font-weight: 600;
            font-size: 1.1em;
            letter-spacing: 0.5px;
        }

        tr {
            transition: all var(--animation-speed) ease;
        }

        tr:hover {
            background: rgba(0, 0, 0, 0.02);
            transform: scale(1.005);
        }

        /* Button Styling */
        .btn {
            padding: 0.9em 1.4em;
            background: linear-gradient(145deg, #28a745, #23963b);
            color: white;
            border-radius: 10px;
            transition: all var(--animation-speed) ease;
            text-decoration: none;
            display: inline-block;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            letter-spacing: 0.5px;
        }

        .btn:hover {
            background: linear-gradient(145deg, #23963b, #1e7e34);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }

        /* Responsive Adjustments */
        @media (max-width: 1600px) {
            :root { --sidebar-width: 280px; }
            body { font-size: 15px; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?> 👤</h2>
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
    <script>
        function searchTable() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchBox");
            filter = input.value.toUpperCase();
            table = document.getElementById("studentTable");
            tr = table.getElementsByTagName("tr");

            for (i = 1; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0]; // Assuming LRN is in the first column
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }       
            }
        }
    </script>
</body>
</html>
