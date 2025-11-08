CREATE DATABASE IF NOT EXISTS student_events;
USE student_events;

-- Users table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    student_id VARCHAR(20),
    phone VARCHAR(15),
    role ENUM('student', 'admin') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Events table
CREATE TABLE events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    time TIME NOT NULL,
    venue VARCHAR(200) NOT NULL,
    organizer VARCHAR(100),
    capacity INT,
    category VARCHAR(50),
    status ENUM('active', 'cancelled') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id)
);

-- Registrations table
CREATE TABLE registrations (
    reg_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('confirmed', 'waiting', 'cancelled') DEFAULT 'confirmed',
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (event_id) REFERENCES events(event_id),
    UNIQUE KEY unique_registration (user_id, event_id)
);

-- Insert sample data
INSERT INTO users (name, email, password, student_id, phone, role) VALUES 
('Admin User', 'admin@university.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADM001', '1234567890', 'admin'),
('John Student', 'john@student.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'STU001', '1234567891', 'student');

INSERT INTO events (title, description, date, time, venue, organizer, capacity, category, created_by) VALUES 
('Web Development Workshop', 'Learn modern web technologies', '2025-11-20', '14:00:00', 'Computer Lab A', 'IT Department', 30, 'Workshop', 1),
('Hackathon 2025', '24-hour coding competition', '2025-11-25', '09:00:00', 'Main Auditorium', 'CS Club', 50, 'Competition', 1);