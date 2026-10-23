CREATE DATABASE IF NOT EXISTS studenthub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studenthub;
CREATE TABLE IF NOT EXISTS users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 role ENUM('student','admin') NOT NULL DEFAULT 'student',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 last_login DATETIME NULL
);
CREATE TABLE IF NOT EXISTS students (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE,
 course VARCHAR(80) NOT NULL,
 year VARCHAR(20) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS events (
 id INT AUTO_INCREMENT PRIMARY KEY,
 title VARCHAR(150) NOT NULL,
 description TEXT,
 event_date DATE NOT NULL,
 venue VARCHAR(150) NOT NULL,
 category VARCHAR(80) DEFAULT 'General',
 poster VARCHAR(255) NULL,
 status ENUM('Open','Closed') DEFAULT 'Open',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS registrations (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NULL,
 event_id INT NULL,
 registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
 FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL
);
CREATE TABLE IF NOT EXISTS audit_logs (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NULL,
 action VARCHAR(150) NOT NULL,
 details TEXT,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
INSERT IGNORE INTO students(name,email,course,year) VALUES
('Aarav Patel','aarav@example.com','B.Tech','2nd Year'),
('Diya Shah','diya@example.com','BCA','1st Year'),
('Vivaan Mehta','vivaan@example.com','B.Tech','3rd Year'),
('Anaya Shah','anaya@example.com','B.Tech','1st Year'),
('Ishaan Mehta','ishaan@example.com','BCA','2nd Year'),
('Kiara Patel','kiara@example.com','MCA','1st Year'),
('Reyansh Desai','reyansh@example.com','B.Tech','4th Year'),
('Aadhya Joshi','aadhya@example.com','BCA','3rd Year'),
('Arjun Trivedi','arjun@example.com','M.Tech','2nd Year'),
('Myra Shah','myra@example.com','B.Tech','2nd Year'),
('Advait Patel','advait@example.com','BCA','1st Year'),
('Sara Mehta','sara@example.com','MCA','2nd Year'),
('Krish Desai','krish@example.com','B.Tech','3rd Year'),
('Ira Joshi','ira@example.com','BCA','2nd Year'),
('Dhruv Shah','dhruv@example.com','B.Tech','1st Year'),
('Navya Patel','navya@example.com','M.Tech','1st Year'),
('Kabir Mehta','kabir@example.com','BCA','3rd Year'),
('Anvi Desai','anvi@example.com','B.Tech','4th Year'),
('Rudra Joshi','rudra@example.com','MCA','1st Year'),
('Pari Trivedi','pari@example.com','B.Tech','2nd Year');
INSERT IGNORE INTO events(title,description,event_date,venue,category) VALUES
('Web Development Workshop','Learn frontend development','2026-11-05','Lab 1','Workshop'),
('AI Innovation Seminar','Explore AI applications','2026-11-18','Auditorium','Seminar');
-- After creating your own account, promote it to admin using:
-- UPDATE users SET role='admin' WHERE email='your-email@example.com';


CREATE TABLE IF NOT EXISTS remember_tokens (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 selector CHAR(24) NOT NULL UNIQUE,
 token_hash CHAR(64) NOT NULL,
 expires_at DATETIME NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
