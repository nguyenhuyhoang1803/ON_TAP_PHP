-- SCHEMA & SEED DATA CHO MOCK EXAM 02
CREATE DATABASE IF NOT EXISTS `mock02_educenter` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mock02_educenter`;

DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `courses`;

CREATE TABLE `courses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(20) UNIQUE NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `tuition_fee` DECIMAL(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `students` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `fullname` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `phone` VARCHAR(15) UNIQUE NOT NULL,
    `course_id` INT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- CHÈN KHÓA HỌC
INSERT INTO `courses` (`id`, `code`, `title`, `tuition_fee`) VALUES
(1, 'PHP_CORE', 'Lập trình PHP & MySQL Chuyên Sâu', 4500000.00),
(2, 'JS_FULL', 'JavaScript Hiện Đại & Fetch API', 3800000.00),
(3, 'PYTHON_AI', 'Lập trình Python & Phân Tích Dữ Liệu', 5200000.00),
(4, 'DEVOPS_PRO', 'DevOps & CI/CD Toàn Diện', 6500000.00),
(5, 'RUST_BASIC', 'Lập trình Rust Căn Bản (Chưa có ai đăng ký)', 7000000.00); -- Khóa học rỗng để test LEFT JOIN

-- CHÈN 28 HỌC VIÊN ĐỂ TEST PHÂN TRANG (Pagination)
INSERT INTO `students` (`id`, `fullname`, `email`, `phone`, `course_id`, `created_at`) VALUES
(1, 'Nguyễn Văn An', 'an.nv@gmail.com', '0901000001', 1, '2026-09-01 08:00:00'),
(2, 'Trần Thị Bình', 'binh.tt@gmail.com', '0901000002', 1, '2026-09-01 08:30:00'),
(3, 'Lê Hoàng Cường', 'cuong.lh@gmail.com', '0901000003', 2, '2026-09-01 09:00:00'),
(4, 'Phạm Quỳnh Dung', 'dung.pq@gmail.com', '0901000004', 3, '2026-09-01 09:15:00'),
(5, 'Vũ Quốc Đạt', 'dat.vq@gmail.com', '0901000005', 1, '2026-09-02 10:00:00'),
(6, 'Đỗ Mỹ Giang', 'giang.dm@gmail.com', '0901000006', 2, '2026-09-02 11:20:00'),
(7, 'Hoàng Minh Hải', 'hai.hm@gmail.com', '0901000007', 3, '2026-09-03 14:00:00'),
(8, 'Bùi Thị Thu Hà', 'ha.btt@gmail.com', '0901000008', 1, '2026-09-03 15:10:00'),
(9, 'Đặng Thái Khang', 'khang.dt@gmail.com', '0901000009', 2, '2026-09-04 08:45:00'),
(10, 'Ngô Gia Khánh', 'khanh.ng@gmail.com', '0901000010', 4, '2026-09-04 09:30:00'),
(11, 'Lý Tiểu Lan', 'lan.lt@gmail.com', '0901000011', 1, '2026-09-05 10:15:00'),
(12, 'Dương Đình Long', 'long.dd@gmail.com', '0901000012', 2, '2026-09-05 11:00:00'),
(13, 'Mai Tuyết Mai', 'mai.mt@gmail.com', '0901000013', 3, '2026-09-06 13:40:00'),
(14, 'Trịnh Hoài Nam', 'nam.th@gmail.com', '0901000014', 1, '2026-09-06 14:20:00'),
(15, 'Lâm Thúy Nga', 'nga.lt@gmail.com', '0901000015', 4, '2026-09-07 15:00:00'),
(16, 'Phan Văn Phong', 'phong.pv@gmail.com', '0901000016', 2, '2026-09-07 16:30:00'),
(17, 'Tô Quốc Quân', 'quan.tq@gmail.com', '0901000017', 3, '2026-09-08 08:10:00'),
(18, 'Quách Ngọc Sơn', 'son.qn@gmail.com', '0901000018', 1, '2026-09-08 09:00:00'),
(19, 'Tạ Đức Thắng', 'thang.td@gmail.com', '0901000019', 2, '2026-09-09 10:45:00'),
(20, 'Hồ Thảo Trang', 'trang.ht@gmail.com', '0901000020', 4, '2026-09-09 11:30:00'),
(21, 'Lưu Vĩnh Trí', 'tri.lv@gmail.com', '0901000021', 1, '2026-09-10 13:15:00'),
(22, 'Vương Cẩm Tú', 'tu.vc@gmail.com', '0901000022', 3, '2026-09-10 14:00:00'),
(23, 'Cao Khắc Uy', 'uy.ck@gmail.com', '0901000023', 2, '2026-09-11 15:20:00'),
(24, 'Đinh Như Vân', 'van.dn@gmail.com', '0901000024', 1, '2026-09-11 16:10:00'),
(25, 'Triệu Khánh Vy', 'vy.tk@gmail.com', '0901000025', 4, '2026-09-12 08:30:00'),
(26, 'Patrick O\'Connor', 'patrick.oc@gmail.com', '0901000026', 1, '2026-09-12 09:15:00'), -- Dấu nháy đơn
(27, 'Hacker <script>alert(1)</script>', 'hacker.xss@gmail.com', '0901000027', 2, '2026-09-13 10:00:00'), -- Mã độc XSS
(28, 'Sinh Viên 100%_Chăm Chỉ', 'chamchi.pct@gmail.com', '0901000028', 3, '2026-09-13 11:00:00'); -- Ký tự % và _
