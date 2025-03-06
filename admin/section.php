<?php
session_start();
$conn = new mysqli("localhost", "root", "", "enrollment_db");

// Check if user is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Handle section enrollment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_section'])) {
    $section_name = $_POST['section_name'];
    $grade_level = $_POST['grade_level'];
    $teacher_name = $_POST['teacher_name'];
    $track = $_POST['track'];
    $strand = $_POST['strand'];
    $section_code = bin2hex(random_bytes(4)); // Generate random section code

    $sql = "INSERT INTO sections (section_name, grade_level, teacher_name, track, strand, section_code) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $section_name, $grade_level, $teacher_name, $track, $strand, $section_code);

    if ($stmt->execute()) {
        // Get the last inserted section ID
        $section_id = $stmt->insert_id;
        
        // Logic to assign students to the new section based on grade level and strand
        $students = $conn->query("SELECT id FROM students WHERE grade_level = '$grade_level' AND strand = '$strand' AND section_id IS NULL");
        
        // Assign each student to the new section and update section_code
        while ($student = $students->fetch_assoc()) {
            $student_id = $student['id'];
            $conn->query("UPDATE students SET section_id = '$section_id', section_code = '$section_code' WHERE id = '$student_id'");
        }
        
        // Redirect to prevent form resubmission
        header("Location: section.php?success=Section added successfully");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch sections
$sections = $conn->query("SELECT * FROM sections ORDER BY section_id DESC");

// Handle section deletion
if (isset($_GET['delete_section'])) {
    $section_id = $_GET['delete_section'];
    $conn->query("DELETE FROM sections WHERE section_id = $section_id");
    header("Location: section.php");
}

// Fetch students for the dropdown
$students = $conn->query("SELECT * FROM students");

// Handle student removal
if (isset($_GET['drop_student']) && is_numeric($_GET['drop_student'])) {
    $student_id = $_GET['drop_student'];
    
    // Use Prepared Statements to prevent SQL Injection
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        header("Location: section.php?success=Student dropped successfully");
        exit();
    } else {
        echo "Error deleting student: " . $stmt->error;
    }

    $stmt->close();
}

// Export data to Excel
if (isset($_GET['export'])) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=sections.xls");

    $output = fopen("php://output", "w");
    fputcsv($output, ["Section ID", "Section Name", "Grade Level", "Teacher Name", "Track", "Strand"]);

    $query = $conn->query("SELECT * FROM sections");
    while ($row = $query->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sections</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="LOGO STA. CRUZ.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <h1><i class="fas fa-users-cog"></i> Section Management</h1>

    <?php if(isset($_GET['success'])): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i> <?php echo $_GET['success']; ?>
        </div>
    <?php endif; ?>

    <!-- Add Section Form -->
    <form method="POST" class="add-section-form">
        <h2><i class="fas fa-plus-circle"></i> Add New Section</h2>
        <div class="form-grid">
            <input type="text" name="section_name" placeholder="Section Name" required>
            <input type="text" name="grade_level" placeholder="Grade Level" required>
            <input type="text" name="teacher_name" placeholder="Teacher Name" required>
            <select name="track" id="track" onchange="updateStrands()" required>
                <option value="">Select Track</option>
                <option value="Academic">Academic</option>
                <option value="TVL">TVL</option>
            </select>
            <select name="strand" id="strand" required>
                <option value="">Select Strand</option>
            </select>
        </div>
        <button type="submit" name="add_section"><i class="fas fa-plus"></i> Add Section</button>
    </form>

    <!-- Display Sections -->
    <h2><i class="fas fa-list"></i> Existing Sections</h2>
    <div class="table-container">
        <table>
            <tr>
                <th>ID</th>
                <th>Section Name</th>
                <th>Grade Level</th>
                <th>Teacher</th>
                <th>Track</th>
                <th>Strand</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $sections->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['section_id']; ?></td>
                    <td><?php echo $row['section_name']; ?></td>
                    <td><?php echo $row['grade_level']; ?></td>
                    <td><?php echo $row['teacher_name']; ?></td>
                    <td><?php echo $row['track']; ?></td>
                    <td><?php echo $row['strand']; ?></td>
                    <td>
                        <a href="section.php?delete_section=<?php echo $row['section_id']; ?>" 
                           onclick="return confirm('Are you sure?')" 
                           class="remove-btn">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- Export Options -->
    <div class="export-section">
        <h2><i class="fas fa-download"></i> Export Options</h2>
        <div class="button-group">
            <a href="section.php?export=true" class="btn">
                <i class="fas fa-file-excel"></i> Download Section List
            </a>
            <a href="export_section.php" class="btn">
                <i class="fas fa-file-export"></i> Download Teacher/Student List
            </a>
        </div>
    </div>

    <script src="../script.js"></script>
</body>
</html>
