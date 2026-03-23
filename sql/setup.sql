-- ============================================================
-- RECIPE MANAGEMENT SYSTEM - DATABASE SETUP
-- Run this entire file in phpMyAdmin SQL tab
-- ============================================================

CREATE DATABASE IF NOT EXISTS recipe_db;
USE recipe_db;

-- -------------------------------------------------------
-- USERS TABLE
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    avatar_color VARCHAR(20) DEFAULT '#6c63ff',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------------
-- RECIPES TABLE
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    category VARCHAR(50) DEFAULT 'Uncategorized',
    ingredients TEXT NOT NULL,
    instructions TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    prep_time INT DEFAULT 0,
    cook_time INT DEFAULT 0,
    servings INT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- -------------------------------------------------------
-- FAVORITES TABLE
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    recipe_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_fav (user_id, recipe_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

-- -------------------------------------------------------
-- ACTIVITY LOG TABLE
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    recipe_title VARCHAR(200) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- -------------------------------------------------------
-- SAMPLE DATA (optional - for testing)
-- -------------------------------------------------------
INSERT INTO users (username, email, password, avatar_color) VALUES
('Anish', 'anish@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '#6c63ff');
-- Sample password is: password

INSERT INTO recipes (user_id, title, category, ingredients, instructions, prep_time, cook_time, servings) VALUES
(1, 'Dal Bhat', 'Lunch', 'Lentils, Rice, Tomato, Onion, Garlic, Cumin, Turmeric, Salt, Oil', 'Wash lentils. Boil with turmeric and salt. Fry onion garlic in oil. Add tomato and spices. Mix with lentils. Serve with rice.', 10, 30, 2),
(1, 'Momo', 'Snacks', 'Flour, Water, Minced Chicken, Onion, Garlic, Ginger, Coriander, Salt, Pepper', 'Make dough. Mix filling ingredients. Wrap in dough circles. Steam for 15 minutes. Serve hot with achar.', 30, 15, 4),
(1, 'Chicken Curry', 'Dinner', 'Chicken, Onion, Tomato, Ginger, Garlic, Garam Masala, Turmeric, Chili, Oil, Salt', 'Fry onion until golden. Add ginger garlic paste. Add tomatoes and spices. Add chicken pieces. Cook covered for 25 minutes.', 15, 30, 3),
(1, 'Sel Roti', 'Breakfast', 'Rice flour, Sugar, Banana, Ghee, Oil for frying', 'Soak rice and grind. Add banana and sugar. Mix to batter. Fry in ring shapes in hot oil. Drain and serve.', 20, 20, 6),
(1, 'Aloo Tama', 'Dinner', 'Potato, Bamboo shoots, Black-eyed peas, Turmeric, Chili, Oil, Salt', 'Boil bamboo shoots. Fry potato. Add bamboo and peas. Add spices and water. Simmer until thick.', 10, 25, 3);

INSERT INTO activity_log (user_id, action, recipe_title) VALUES
(1, 'added', 'Dal Bhat'),
(1, 'added', 'Momo'),
(1, 'added', 'Chicken Curry'),
(1, 'added', 'Sel Roti'),
(1, 'added', 'Aloo Tama');
