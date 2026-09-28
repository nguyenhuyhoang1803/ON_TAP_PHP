<?php
// setup/check_mysql_live.php

echo "1. Checking PDO MySQL Extension: " . (extension_loaded('pdo_mysql') ? "LOADED (PASS)" : "NOT LOADED (FAIL)") . "\n";
echo "2. Attempting PDO connection to 127.0.0.1:3306 (user 'root', blank password)...\n";

try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 2
    ]);
    echo "   ==> [SUCCESS] Kết nối MySQL thành công!\n";
    $stmt = $pdo->query("SELECT VERSION() AS ver, @@character_set_connection AS cs, CURRENT_TIMESTAMP() AS now");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   ==> MySQL Version: " . $row['ver'] . "\n";
    echo "   ==> Charset kết nối: " . $row['cs'] . "\n";
    echo "   ==> SELECT test: CURRENT_TIMESTAMP = " . $row['now'] . "\n";
} catch (PDOException $e) {
    echo "   ==> [TRẠNG THÁI: MYSQL CHƯA CHẠY / KHÔNG KẾT NỐI ĐƯỢC]\n";
    echo "   ==> Thông báo lỗi thực tế: " . $e->getMessage() . "\n";
    echo "   ==> HƯỚNG DẪN: Dịch vụ MySQL hiện chưa được bật trên máy tính. Bạn chỉ cần mở ứng dụng Laragon và nhấn nút 'Start All'.\n";
}
