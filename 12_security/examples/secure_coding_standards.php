<?php
// 12_security/examples/secure_coding_standards.php

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

$catId = filter_var($_GET['cat_id'] ?? 0, FILTER_VALIDATE_INT) ?: 1;
$sort  = $_GET['sort'] ?? 'id';

$allowedSort = ['id', 'price', 'name'];
$col = in_array($sort, $allowedSort, true) ? $sort : 'id';

try {
    $pdo = new PDO("mysql:host=localhost;dbname=test;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE category_id = :cat ORDER BY $col ASC");
    $stmt->execute(['cat' => $catId]);
    $items = $stmt->fetchAll();
} catch (PDOException $ex) {
    error_log($ex->getMessage());
    $items = [];
    $errorMsg = "Có lỗi xảy ra khi truy vấn dữ liệu.";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Bảo Mật Chuẩn</title></head>
<body>
    <h2>Danh Sách Sản Phẩm An Toàn</h2>
    <?php if (isset($errorMsg)): ?><p style="color:red"><?= e($errorMsg) ?></p><?php endif; ?>
    <ul>
        <?php foreach ($items as $it): ?>
            <li><?= e($it['name']) ?> - <?= number_format($it['price'], 0, ',', '.') ?> VNĐ</li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
