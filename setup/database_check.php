<?php
/**
 * SCRIPT KIỂM TRA KẾT NỐI VÀ XỬ LÝ DATABASE MYSQL
 * Môn: Lập trình mã nguồn mở
 */

echo "========================================================\n";
echo "   KIỂM TRA CSDL MYSQL QUA PDO (DATABASE HEALTH CHECK)\n";
echo "========================================================\n\n";

$host = '127.0.0.1';
$port = 3306;
$username = 'root';
$password = ''; // Mặc định trên Laragon là rỗng

echo "[1] Đang thử kết nối tới MySQL ($host:$port) với tài khoản '$username'...\n";

try {
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    echo "    ==> [PASS] Kết nối PDO thành công!\n";

    // Lấy phiên bản MySQL
    $version = $pdo->query("SELECT VERSION() AS ver")->fetchColumn();
    echo "    ==> Phiên bản MySQL: $version\n\n";

    // 2. Kiểm tra tạo database test và bảng tạm
    echo "[2] Kiểm tra quyền thao tác DDL & DML (Tạo DB, Bảng, Insert, Select UTF-8)...\n";
    $testDb = "_mnm_exam_env_test";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$testDb` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$testDb`");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `test_samples` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `score` DECIMAL(4,2) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // 3. Test insert dữ liệu tiếng Việt có dấu nháy đơn (Test chống lỗi charset và nháy đơn)
    $sampleText = "Đặng Hoàng O'Connor - Lập trình mã nguồn mở 2026";
    $stmt = $pdo->prepare("INSERT INTO `test_samples` (`title`, `score`) VALUES (:t, :s)");
    $stmt->execute(['t' => $sampleText, 's' => 9.75]);
    $insertedId = $pdo->lastInsertId();

    // 4. Test select lại và kiểm tra tính toàn vẹn
    $stmtSelect = $pdo->prepare("SELECT `title`, `score` FROM `test_samples` WHERE `id` = :id");
    $stmtSelect->execute(['id' => $insertedId]);
    $row = $stmtSelect->fetch();

    if ($row && $row['title'] === $sampleText && (float)$row['score'] === 9.75) {
        echo "    ==> [PASS] Chèn và đọc dữ liệu Tiếng Việt Unicode + Dấu nháy đơn chính xác 100%!\n";
    } else {
        echo "    ==> [FAIL] Dữ liệu đọc lại không khớp! Có thể do cấu hình charset chưa chuẩn.\n";
    }

    // 5. Dọn dẹp database test
    $pdo->exec("DROP DATABASE IF EXISTS `$testDb`");
    echo "    ==> [PASS] Dọn dẹp cơ sở dữ liệu tạm hoàn tất.\n\n";

    echo "========================================================\n";
    echo " KẾT LUẬN: HỆ THỐNG DATABASE MYSQL HOÀN TOÀN SẴN SÀNG!\n";
    echo "========================================================\n";

} catch (PDOException $e) {
    echo "\n    ==> [FAIL] Lỗi kết nối CSDL: " . $e->getMessage() . "\n\n";
    echo "HƯỚNG DẪN KHẮC PHỤC:\n";
    echo "1. Đảm bảo Laragon đã bấm 'Start All' (MySQL đang chạy ở cổng 3306).\n";
    echo "2. Nếu MySQL có đặt password khác rỗng, hãy mở file setup/database_check.php để cập nhật biến \$password.\n";
    echo "3. Kiểm tra xem cổng 3306 có bị xung đột với XAMPP hoặc MySQL service khác không.\n";
    echo "========================================================\n";
}
