-- ===============================
-- Create Database
-- ===============================
CREATE DATABASE IF NOT EXISTS classifieds1
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE classifieds1;

-- ===============================
-- Users Table
-- Stores registered users
-- ===============================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ===============================
-- Categories Table
-- Stores ad categories (Cars, Electronics, etc.)
-- ===============================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- ===============================
-- Ads Table
-- Stores classified advertisements
-- ===============================
CREATE TABLE ads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    main_image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Relations
    CONSTRAINT fk_ads_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_ads_category
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===============================
-- Contact Messages Table
-- Messages sent from Contact Us form
-- ===============================
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ===============================
-- Seller Messages Table
-- Messages sent to sellers about ads
-- ===============================
CREATE TABLE seller_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad_id INT NOT NULL,
    sender_name VARCHAR(100) NOT NULL,
    sender_email VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_message_ad
        FOREIGN KEY (ad_id) REFERENCES ads(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===============================
-- Insert Default Categories
-- ===============================
INSERT INTO categories (name, image) VALUES
('Cars', 'cars.jpg'),
('Real Estate', 'real_estate.jpg'),
('Electronics', 'electronics.jpg'),
('Clothing', 'clothing.jpg');
