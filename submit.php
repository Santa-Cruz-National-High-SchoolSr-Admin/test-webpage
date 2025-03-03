<?php
session_start();
$conn = new mysqli("localhost", "root", "", "enrollment_db");

// Check for connection errors
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION = $_POST; // Store form data in session

    // Check submission status
    if ($_POST['submission_status'] !== 'submitted') {
        $_SESSION['error'] = "Form submission failed. Please try again.";
        header("Location: online_registration.html");
        exit();
    }

    // Collect form data
    $grade = $_POST['grade'];
    $track = strtoupper($_POST['track']);
    $strand = strtoupper($_POST['strand']);
    $learning_modality = strtoupper($_POST['learning_modality']);
    $school_year = $_POST['school_year'];
    $lrn = $_POST['lrn'];
    $first_name = strtoupper($_POST['first_name']);
    $middle_name = strtoupper($_POST['middle_name']);
    $last_name = strtoupper($_POST['last_name']);
    $extension_name = strtoupper($_POST['extension_name']);
    $disability = strtoupper($_POST['disability']);
    $ip_community = strtoupper($_POST['ip_community']);
    $FourPs = strtoupper($_POST['FourPs']);
    $dob = $_POST['dob'];
    $age = $_POST['age'];
    $gender = strtoupper($_POST['gender']);
    $birthplace = strtoupper($_POST['birthplace']);
    $indigenous = strtoupper($_POST['indigenous']);
    $mother_tongue = strtoupper($_POST['mother_tongue']);
    $contact = $_POST['contact'];

    $province = $_POST['province'];
    $municipality = $_POST['municipality'];
    $barangay = $_POST['barangay'];
    $street = $_POST['street'];
    $house_number = $_POST['house_number'];
    $zip_code = $_POST['zip_code'];

    $father_last_name = $_POST['father_last_name'];
    $father_first_name = $_POST['father_first_name'];
    $father_middle_name = $_POST['father_middle_name'];
    $mother_last_name = $_POST['mother_last_name'];
    $mother_first_name = $_POST['mother_first_name'];
    $mother_middle_name = $_POST['mother_middle_name'];
    $guardian_last_name = $_POST['guardian_last_name'];
    $guardian_first_name = $_POST['guardian_first_name'];
    $guardian_middle_name = $_POST['guardian_middle_name'];
    $contact_guardian_parent = $_POST['contact_guardian_parent'];

    $is_returning_or_transfer = $_POST['is_returning_or_transfer'];
    $last_grade_level = $_POST['last_grade_level'];
    $last_school_year = $_POST['last_school_year'];
    $previous_school = $_POST['previous_school'];
    $previous_school_id = $_POST['previous_school_id'];

    // ✅ AUTOMATIC SECTION ASSIGNMENT
    $sections = ["A", "B", "C"]; // Three sections per strand
    $max_students_per_section = 30; // Maximum students per section

    // Check which section has the least students for the selected strand
    $sql_section = "SELECT section, COUNT(*) as student_count 
                    FROM students 
                    WHERE strand = ? 
                    GROUP BY section 
                    ORDER BY student_count ASC 
                    LIMIT 1";
    $stmt_section = $conn->prepare($sql_section);
    $stmt_section->bind_param("s", $strand);
    $stmt_section->execute();
    $result = $stmt_section->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['student_count'] < $max_students_per_section) {
            $section = $row['section']; // Assign the least populated section
        } else {
            // If all sections are full, randomly assign one
            $section = $strand . " " . $sections[array_rand($sections)];
        }
    } else {
        // If no students are enrolled yet, assign a random section
        $section = $strand . " " . $sections[array_rand($sections)];
    }

    $stmt_section->close();

    // ✅ INSERT INTO DATABASE
    $stmt = $conn->prepare("INSERT INTO students 
    (grade, track, strand, section, learning_modality, school_year, lrn, first_name, middle_name, last_name, extension_name, disability, ip_community, FourPs, dob, age, gender, birthplace, indigenous, mother_tongue, contact, 
    province, municipality, barangay, street, house_number, zip_code, 
    father_last_name, father_first_name, father_middle_name, 
    mother_last_name, mother_first_name, mother_middle_name, 
    guardian_last_name, guardian_first_name, guardian_middle_name, contact_guardian_parent, 
    is_returning_or_transfer, last_grade_level, last_school_year, previous_school, previous_school_id)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("SQL Preparation Error: " . $conn->error);
    }

    $stmt->bind_param("issssssssssssssssssssssssssssssssssssss", // Fixed: 40 placeholders, 40 parameters
        $grade, $track, $strand, $section, $learning_modality, $school_year, $lrn, $first_name, $middle_name, $last_name, $extension_name, $disability, $ip_community, $FourPs, $dob, $age, $gender, $birthplace, $indigenous, $mother_tongue, $contact, 
        $province, $municipality, $barangay, $street, $house_number, $zip_code, 
        $father_last_name, $father_first_name, $father_middle_name, 
        $mother_last_name, $mother_first_name, $mother_middle_name, 
        $guardian_last_name, $guardian_first_name, $guardian_middle_name, $contact_guardian_parent, 
        $is_returning_or_transfer, $last_grade_level, $last_school_year, $previous_school, $previous_school_id
    );

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registration successful! Assigned to section: $section";
        header("Location: dashboard.php"); // ✅ Redirect to dashboard
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
        header("Location: dashboard.php"); // ✅ Redirect to dashboard with error
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>