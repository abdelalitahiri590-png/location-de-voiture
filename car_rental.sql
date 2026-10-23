CREATE DATABASE IF NOT EXISTS car_rental CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE car_rental;

-- 1. جدول السيارات
CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(255) NOT NULL,
    price INT NOT NULL,
    category ENUM('normal', 'sport', 'luxury') NOT NULL,
    city VARCHAR(50) NOT NULL DEFAULT 'مراكش',
    available TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. جدول الحجوزات المطور (مرتبط بـ car_id)
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT NOT NULL,
    client_name VARCHAR(100) NOT NULL,
    client_phone VARCHAR(30) NOT NULL,
    client_city VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    days INT NOT NULL,
    total_price INT NOT NULL,
    lat DECIMAL(10, 8) NULL,
    lng DECIMAL(11, 8) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. جدول المشرفين لحماية لوحة التحكم (كلمة المرور الافتراضية: admin123)
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إدخال البيانات التجريبية
INSERT INTO admins (username, password) VALUES 
('admin', 'admin123');

INSERT INTO cars (name, image, price, category, city) VALUES
('Dacia Logan', 'images/dacia logane.jpg', 200, 'normal', 'مراكش'),
('Dacia Sandero', 'images/dacia sandero.jpg', 200, 'normal', 'الدار البيضاء'),
('Kia Picanto', 'images/kia picanto.webp', 200, 'normal', 'الراشيدية'),
('Golf 8.5 R-Line', 'images sp/golf 8.5 r line.jpg', 600, 'sport', 'مراكش'),
('Audi RS3', 'images sp/audi-rs3-by-manhart.jpg', 650, 'sport', 'الدار البيضاء'),
('BMW Série 7', 'Images 2/BMW Series 7.webp', 2200, 'luxury', 'الراشيدية');