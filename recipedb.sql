DROP DATABASE recipe_sharing_app;
CREATE DATABASE recipe_sharing_app;

use recipe_sharing_app;

-- Table for Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    profile_img VARCHAR(255),
    date DATE DEFAULT NOW(),
    role ENUM('admin', 'chef') NOT NULL
);

-- Table for Recipes
CREATE TABLE recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    ingredients TEXT NOT NULL,
    instructions TEXT NOT NULL,
    cooking_time INT,
    serving_size INT,
    special_instructions TEXT,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table for Categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Table for Recipe Categories (Many-to-Many relationship)
CREATE TABLE recipe_categories (
    recipe_id INT NOT NULL,
    category_id INT NOT NULL,
    published BOOL NOT NULL DEFAULT FALSE,
    PRIMARY KEY (recipe_id, category_id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE recipe_image (
	image_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    image_url VARCHAR(255) NOT NULL,
    recipe_id INT NOT NULL,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT NOT NULL,
    user_id INT,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment VARCHAR(520),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (name, password, email, role)
VALUES ('admin', '$2y$10$At5cAHBgG5M3hMfq8Asij.7d8Ju4PSb/dW7r2DCWlfa2VK8doVtp.', 'admin@mail.com', 'admin');
-- email: admin@mail.com
-- password: root

