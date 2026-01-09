<?php

// Migration: Create users table
// Generated: 2026_01_09_134327

return "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password TINYTEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
