CREATE DATABASE IF NOT EXISTS db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    money INT(10) NOT NULL,
    password VARCHAR(256) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (username, email, money, password) VALUES
('john_doe', 'john@example.com', 0),
('jane_smith', 'jane@example.com', 0);