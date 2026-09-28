<?php
// 08_pdo_crud/examples/crud_demo.php

// 1. Kết nối CSDL
$dsn = "mysql:host=localhost;charset=utf8mb4";
$pdo = new PDO($dsn, 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

// Tạo database và bảng demo
$pdo->exec("CREATE DATABASE IF NOT EXISTS exam_demo_crud CHARACTER SET utf8mb4");
$pdo->exec("USE exam_demo_crud");
$pdo->exec("CREATE TABLE IF NOT EXISTS members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// 2. Thao tác INSERT an toàn
$stmtInsert = $pdo->prepare("INSERT IGNORE INTO members (username, fullname) VALUES (:u, :fn)");
$stmtInsert->execute(['u' => 'hoangnam', 'fn' => 'Đặng Hoàng Nam']);

// 3. Thao tác SELECT
$stmtSelect = $pdo->prepare("SELECT * FROM members ORDER BY id DESC");
$stmtSelect->execute();
$members = $stmtSelect->fetchAll();

echo "DANH SÁCH THÀNH VIÊN TỪ CSDL:\n";
foreach ($members as $m) {
    echo "- ID: {$m['id']} | Tài khoản: {$m['username']} | Tên: {$m['fullname']}\n";
}
