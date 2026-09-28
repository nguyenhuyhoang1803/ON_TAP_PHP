-- SCHEMA & SEED DATA CHO MOCK EXAM 01
CREATE DATABASE IF NOT EXISTS `mock01_stationery` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mock01_stationery`;

DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) UNIQUE NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `category_id` INT NOT NULL,
    `price` DECIMAL(12,2) NOT NULL,
    `quantity` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- DỮ LIỆU MẪU CÓ EDGE CASES (Tiếng Việt, Nháy đơn, Ký tự lạ)
INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Bút viết & Mực'),
(2, 'Sổ tay & Vở ghi'),
(3, 'Dụng cụ học tập & Vẽ'),
(4, 'Thiết bị văn phòng');

INSERT INTO `products` (`id`, `code`, `name`, `category_id`, `price`, `quantity`) VALUES
(1, 'SP001', 'Bút bi Thiên Long 0.5mm - Xanh', 1, 5000.00, 120),
(2, 'SP002', 'Bút máy Picasso O\'Connor Classic', 1, 450000.00, 15),
(3, 'SP003', 'Vở ô ly 96 trang Hồng Hà (100%_cotton)', 2, 12000.00, 0), -- Số lượng 0 để test bẫy
(4, 'SP004', 'Sổ da cao cấp Khóa số A5', 2, 185000.00, 30),
(5, 'SP005', 'Bộ thước kẻ compa <script>alert("XSS")</script>', 3, 35000.00, 50), -- Test XSS
(6, 'SP006', 'Máy dập ghim Deli 24/6 kèm ghim', 4, 65000.00, 25);
