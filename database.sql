CREATE DATABASE IF NOT EXISTS ai_solution_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE ai_solution_db;

CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_login_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS enquiries (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL,
  phone VARCHAR(40) DEFAULT NULL,
  company VARCHAR(140) DEFAULT NULL,
  country VARCHAR(100) DEFAULT NULL,
  job_title VARCHAR(120) DEFAULT NULL,
  service VARCHAR(120) DEFAULT NULL,
  timeline VARCHAR(80) DEFAULT NULL,
  message TEXT NOT NULL,
  status ENUM('New', 'In Review', 'Contacted', 'Closed') NOT NULL DEFAULT 'New',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_enquiries_status (status),
  INDEX idx_enquiries_created_at (created_at),
  INDEX idx_enquiries_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO admins (username, password_hash)
VALUES ('admin', '$2y$10$e19WCAOX9ICdHuvw6DO9LOZZW8kICVAZOe5wuV88jIlBjLMLK/FPC')
ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash);
