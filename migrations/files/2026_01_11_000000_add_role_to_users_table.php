<?php

// Migration: Add role to users table
// Generated: 2026_01_11_000000

return "ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'user' NOT NULL AFTER password;";
