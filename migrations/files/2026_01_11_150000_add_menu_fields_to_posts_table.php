<?php

// Migration: Add menu fields to posts table
// Generated: 2026_01_11_150000

return "ALTER TABLE posts 
    ADD COLUMN is_in_menu BOOLEAN DEFAULT FALSE AFTER content,
    ADD COLUMN menu_order INT DEFAULT 0 AFTER is_in_menu;";
