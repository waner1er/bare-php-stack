<?php

// Migration: Add category_id to menu_items table
// Generated: 2026_01_12_190000

return "ALTER TABLE menuitems ADD COLUMN category_id INT DEFAULT NULL;";
