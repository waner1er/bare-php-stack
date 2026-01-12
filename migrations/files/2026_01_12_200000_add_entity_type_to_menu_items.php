<?php

// Migration: Add entity_type to menu_items table
// Generated: 2026_01_12_200000

return "ALTER TABLE menuitems ADD COLUMN entity_type VARCHAR(50) DEFAULT 'Post' AFTER type;";
