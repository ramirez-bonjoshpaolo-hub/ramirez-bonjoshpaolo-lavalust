-- Ramirez LavaLust Labs - Aiven MySQL schema
-- Run this in the Aiven query editor before opening /users or /products.

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    username VARCHAR(100) NOT NULL UNIQUE
);

INSERT INTO users (firstname, lastname, email, username) VALUES
    ('Bon Josh Paolo', 'Ramirez', 'bon.ramirez@gmail.com', 'bonramirez'),
    ('Mika', 'Santos', 'mika.santos@gmail.com', 'mikasantos'),
    ('Andre', 'Reyes', 'andre.reyes@gmail.com', 'andrereyes'),
    ('Lia', 'Garcia', 'lia.garcia@gmail.com', 'liagarcia'),
    ('Noel', 'Mendoza', 'noel.mendoza@gmail.com', 'noelmendoza')
ON DUPLICATE KEY UPDATE firstname = VALUES(firstname);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    quantity INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
