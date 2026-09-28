-- SCHEMA & SEED DATA CHO MOCK EXAM 04
CREATE DATABASE IF NOT EXISTS `mock04_hospital` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mock04_hospital`;

DROP TABLE IF EXISTS `prescriptions`;
DROP TABLE IF EXISTS `patients`;
DROP TABLE IF EXISTS `doctors`;

CREATE TABLE `doctors` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(20) UNIQUE NOT NULL,
    `fullname` VARCHAR(100) NOT NULL,
    `department` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `patients` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `id_card` VARCHAR(20) UNIQUE NOT NULL,
    `fullname` VARCHAR(100) NOT NULL,
    `dob` DATE NOT NULL,
    `gender` ENUM('Nam', 'Nữ') NOT NULL,
    `record_file` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `prescriptions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `doctor_id` INT NOT NULL,
    `patient_id` INT NOT NULL,
    `diagnosis` TEXT NOT NULL,
    `total_cost` DECIMAL(12,2) NOT NULL,
    `prescribed_at` DATE NOT NULL,
    FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`),
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- BÁC SĨ MẪU
INSERT INTO `doctors` (`id`, `code`, `fullname`, `department`) VALUES
(1, 'DOC01', 'BS.CKII Nguyễn Văn Tuấn', 'Khoa Tim Mạch'),
(2, 'DOC02', 'ThS.BS Trần Thị Thảo', 'Khoa Nội Tiết'),
(3, 'DOC03', 'BS. Đặng Hoàng O\'Connor', 'Khoa Cấp Cứu'),
(4, 'DOC04', 'BS. Lê Minh Trí (Bác sĩ mới về)', 'Khoa Ngoại Thần Kinh'); -- Bác sĩ chưa kê đơn nào trong tháng 9

-- 27 BỆNH NHÂN ĐỂ TEST PHÂN TRANG & TÌM KIẾM
INSERT INTO `patients` (`id`, `id_card`, `fullname`, `dob`, `gender`, `record_file`, `created_at`) VALUES
(1, '001099000001', 'Nguyễn Thị Hoa', '1985-05-12', 'Nữ', NULL, '2026-09-01 08:00:00'),
(2, '001099000002', 'Trần Văn Mạnh', '1978-11-20', 'Nam', NULL, '2026-09-01 08:30:00'),
(3, '001099000003', 'Patrick O\'Connor', '1990-02-14', 'Nam', NULL, '2026-09-02 09:00:00'), -- Tên nháy đơn
(4, '001099000004', 'Bệnh Nhân 100%_Sức Khỏe', '1995-07-22', 'Nữ', NULL, '2026-09-02 10:15:00'), -- Ký tự % và _
(5, '001099000005', 'Hacker <script>alert("XSS")</script>', '2000-01-01', 'Nam', NULL, '2026-09-03 11:00:00'), -- XSS payload
(6, '001099000006', 'Phạm Quỳnh Anh', '1989-09-09', 'Nữ', NULL, '2026-09-03 14:00:00'),
(7, '001099000007', 'Vũ Đức Đam', '1965-03-18', 'Nam', NULL, '2026-09-04 08:10:00'),
(8, '001099000008', 'Đỗ Mỹ Linh', '1996-10-13', 'Nữ', NULL, '2026-09-04 09:25:00'),
(9, '001099000009', 'Hoàng Dược Sư', '1950-12-05', 'Nam', NULL, '2026-09-05 10:30:00'),
(10, '001099000010', 'Bùi Kim Ngân', '1992-06-30', 'Nữ', NULL, '2026-09-05 11:45:00'),
(11, '001099000011', 'Lý Nam Đế', '1982-04-17', 'Nam', NULL, '2026-09-06 13:20:00'),
(12, '001099000012', 'Dương Quá', '1998-08-28', 'Nam', NULL, '2026-09-06 15:00:00'),
(13, '001099000013', 'Tiểu Long Nữ', '2001-11-11', 'Nữ', NULL, '2026-09-07 08:00:00'),
(14, '001099000014', 'Quách Tĩnh', '1980-01-25', 'Nam', NULL, '2026-09-07 09:15:00'),
(15, '001099000015', 'Hoàng Dung', '1983-05-02', 'Nữ', NULL, '2026-09-08 10:00:00'),
(16, '001099000016', 'Lệnh Hồ Xung', '1991-07-07', 'Nam', NULL, '2026-09-08 14:10:00'),
(17, '001099000017', 'Nhậm Doanh Doanh', '1994-09-20', 'Nữ', NULL, '2026-09-09 15:30:00'),
(18, '001099000018', 'Trương Vô Kỵ', '1988-12-12', 'Nam', NULL, '2026-09-09 16:00:00'),
(19, '001099000019', 'Triệu Mẫn', '1993-03-08', 'Nữ', NULL, '2026-09-10 08:30:00'),
(20, '001099000020', 'Chu Chỉ Nhược', '1995-10-10', 'Nữ', NULL, '2026-09-10 09:45:00'),
(21, '001099000021', 'Đoàn Dự', '1997-04-04', 'Nam', NULL, '2026-09-11 10:30:00'),
(22, '001099000022', 'Vương Ngữ Yên', '1999-02-02', 'Nữ', NULL, '2026-09-11 11:15:00'),
(23, '001099000023', 'Kiều Phong', '1975-06-15', 'Nam', NULL, '2026-09-12 13:40:00'),
(24, '001099000024', 'Hư Trúc', '1986-07-19', 'Nam', NULL, '2026-09-12 14:50:00'),
(25, '001099000025', 'Mộ Dung Phục', '1981-08-23', 'Nam', NULL, '2026-09-13 08:20:00'),
(26, '001099000026', 'A Châu', '1987-10-05', 'Nữ', NULL, '2026-09-13 09:10:00'),
(27, '001099000027', 'A Tử', '1993-12-01', 'Nữ', NULL, '2026-09-13 10:30:00');

-- TOA THUỐC TRONG THÁNG 09/2026 (DOC01 và DOC02 cùng đạt MAX 8.500.000 VNĐ để test đồng hạng)
INSERT INTO `prescriptions` (`doctor_id`, `patient_id`, `diagnosis`, `total_cost`, `prescribed_at`) VALUES
(1, 1, 'Tăng huyết áp vô căn độ 2', 4500000.00, '2026-09-05'),
(1, 2, 'Rối loạn nhịp xoang nhanh', 4000000.00, '2026-09-12'), -- Tổng DOC01 = 8.5tr
(2, 3, 'Đái tháo đường type 2 có biến chứng', 5000000.00, '2026-09-08'),
(2, 4, 'Bướu giáp nhân tuyến giáp lành tính', 3500000.00, '2026-09-15'), -- Tổng DOC02 = 8.5tr (Đồng hạng)
(3, 5, 'Chấn thương phần mềm đùi trái', 2200000.00, '2026-09-10'); -- DOC03 = 2.2tr
