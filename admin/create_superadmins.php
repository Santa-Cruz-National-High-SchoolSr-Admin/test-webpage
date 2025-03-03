<?php
$conn = new mysqli("localhost", "root", "", "enrollment_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$superadmins = [
    ['@_JayLechugas', '@_2025JaySY'],
    ['superadmin2', 'superpass2'],
    ['superadmin3', 'superpass3'],
    ['superadmin4', 'superpass4'],
    ['superadmin5', 'superpass5']
];

foreach ($superadmins as $superadmin) {
    $username = $superadmin[0];
    $password = password_hash($superadmin[1], PASSWORD_DEFAULT); // Securely hashed password

    $sql = "INSERT INTO accounts (username, password, role) VALUES (?, ?, 'superadmin')";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL Preparation Error: " . $conn->error);
    }

    $stmt->bind_param("ss", $username, $password);

    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
    }
}

echo "✅ 5 Superadmins created successfully! DELETE this file now for security.";

$stmt->close();
$conn->close();
?>
