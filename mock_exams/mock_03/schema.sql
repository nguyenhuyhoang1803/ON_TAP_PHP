-- SCHEMA & SEED DATA CHO MOCK EXAM 03
CREATE DATABASE IF NOT EXISTS `mock03_restaurant` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mock03_restaurant`;

DROP TABLE IF EXISTS `bills`;
DROP TABLE IF EXISTS `reservations`;
DROP TABLE IF EXISTS `tables`;

CREATE TABLE `tables` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(20) UNIQUE NOT NULL,
    `name` VARCHAR(50) NOT NULL,
    `type` ENUM('indoor', 'outdoor', 'vip') NOT NULL DEFAULT 'indoor',
    `capacity` INT NOT NULL,
    `status` ENUM('available', 'occupied', 'reserved') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `reservations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(15) NOT NULL,
    `guest_count` INT NOT NULL,
    `booking_date` DATE NOT NULL,
    `booking_time` TIME NOT NULL,
    `note` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `bills` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `table_id` INT NOT NULL,
    `total_amount` DECIMAL(12,2) NOT NULL,
    `bill_date` DATE NOT NULL,
    FOREIGN KEY (`table_id`) REFERENCES `tables`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- CHÈN DỮ LIỆU BÀN ĂN
INSERT INTO `tables` (`id`, `code`, `name`, `type`, `capacity`, `status`) VALUES
(1, 'TB01', 'Bàn Cửa Sổ 01', 'indoor', 4, 'available'),
(2, 'TB02', 'Bàn Cửa Sổ 02', 'indoor', 4, 'occupied'),
(3, 'TB03', 'Bàn Đại Tiệc 01', 'indoor', 12, 'available'),
(4, 'TB04', 'Bàn Sân Vườn Hoàng Hôn', 'outdoor', 6, 'available'),
(5, 'TB05', 'Bàn Sân Vườn Gió Mát', 'outdoor', 8, 'reserved'),
(6, 'VIP01', 'Phòng Tổng Thống VIP 1', 'vip', 10, 'available'),
(7, 'VIP02', 'Phòng Hoàng Gia VIP 2', 'vip', 10, 'available'),
(8, 'TB06', 'Bàn Dự Phòng Mới Mở', 'outdoor', 2, 'available'); -- Bàn chưa có đơn nào để test LEFT JOIN

-- CHÈN DỮ LIỆU HÓA ĐƠN ĐỂ TEST ĐỒNG HẠNG (TIES) DOANH THU CAO NHẤT
-- Cả VIP01 và VIP02 đều đạt doanh thu cao nhất là 15.000.000 VNĐ
INSERT INTO `bills` (`table_id`, `total_amount`, `bill_date`) VALUES
(1, 2500000.00, '2026-09-10'),
(1, 1800000.00, '2026-09-15'),
(2, 4200000.00, '2026-09-12'),
(4, 5500000.00, '2026-09-14'),
(6, 15000000.00, '2026-09-18'), -- VIP01: 15tr
(7, 10000000.00, '2026-09-20'),
(7, 5000000.00, '2026-09-22');  -- VIP02: tổng 15tr (Đồng hạng 1 với VIP01)
