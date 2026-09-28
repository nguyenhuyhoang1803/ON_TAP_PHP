<?php
// 12_security/solutions/mini_challenge_solution.php

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

$cat  = filter_var($_GET['cat'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
$sort = $_GET['sort'] ?? 'id';

$allowedCols = ['id', 'price', 'name'];
$sortCol = in_array($sort, $allowedCols, true) ? $sort : 'id';

try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE cat_id = :cat ORDER BY $sortCol ASC");
    $stmt->execute(['cat' => $cat]);

    echo "<ul>\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>" . e($row['name']) . " - " . number_format($row['price'], 0, ',', '.') . " đ</li>\n";
    }
    echo "</ul>\n";
} catch (PDOException $e) {
    error_log("Database query error: " . $e->getMessage());
    echo "<p style='color: red;'>Không thể tải danh sách sản phẩm vào lúc này.</p>";
}
