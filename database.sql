-- Create database
CREATE DATABASE IF NOT EXISTS news_website;
USE news_website;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    bio TEXT,
    remember_token VARCHAR(100),
    token_expires DATETIME,
    created_at DATETIME NOT NULL,
    updated_at DATETIME
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at DATETIME NOT NULL,
    updated_at DATETIME
);

-- Articles table
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    user_id INT NOT NULL,
    category_id INT,
    image VARCHAR(255),
    views INT DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Comments table
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    user_id INT NOT NULL,
    article_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

-- Insert sample categories
INSERT INTO categories (name, slug, description, created_at, updated_at) VALUES
('Technology', 'technology', 'Latest technology news and updates', NOW(), NOW()),
('Business', 'business', 'Business news and market updates', NOW(), NOW()),
('Politics', 'politics', 'Political news and analysis', NOW(), NOW()),
('Health', 'health', 'Health news and wellness tips', NOW(), NOW()),
('Entertainment', 'entertainment', 'Entertainment news and celebrity updates', NOW(), NOW()),
('Sports', 'sports', 'Sports news and updates', NOW(), NOW()),
('Science', 'science', 'Science news and discoveries', NOW(), NOW()),
('Travel', 'travel', 'Travel news and destination guides', NOW(), NOW());

