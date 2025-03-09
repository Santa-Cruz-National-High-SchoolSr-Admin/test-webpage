CREATE DATABASE IF NOT EXISTS admin_db;
USE admin_db;

CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert a default admin user (username: admin, password: admin123)
INSERT INTO admin_users (username, password) VALUES 
('admin', '$2y$10$8K1p/bkyzMZUgjrVg6kqBeSZg.RztH7QjcoWGcw6C1LBmCIwLWF4.');

-- Create enrollment_db and students table
CREATE DATABASE IF NOT EXISTS enrollment_db;
USE enrollment_db;

CREATE TABLE IF NOT EXISTS students (
    lrn VARCHAR(12) PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    grade INT NOT NULL,
    track VARCHAR(50) NOT NULL,
    strand VARCHAR(50) NOT NULL,
    section VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
