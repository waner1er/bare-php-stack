<?php

// Migration: Add category to posts
// Generated: 2026_01_11_160100

return "ALTER TABLE posts 
    ADD COLUMN category_id INT NULL,
    ADD CONSTRAINT fk_posts_category 
        FOREIGN KEY (category_id) 
        REFERENCES categories(id) 
        ON DELETE SET NULL;";
