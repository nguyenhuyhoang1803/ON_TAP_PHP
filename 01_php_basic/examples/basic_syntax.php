<?php
// 01_php_basic/examples/basic_syntax.php

// 1. Khởi tạo mảng sản phẩm
$products = [
    ['id' => 101, 'name' => 'Bàn phím cơ', 'price' => 850000, 'in_stock' => true],
    ['id' => 102, 'name' => 'Chuột không dây', 'price' => 350000, 'in_stock' => false],
    ['id' => 103, 'name' => 'Tai nghe Gaming', 'price' => 1200000, 'in_stock' => true],
];

// 2. Hàm lọc và tính toán
function getAvailableProducts(array $items): array {
    $result = [];
    foreach ($items as $item) {
        if ($item['in_stock'] === true) {
            $result[] = $item;
        }
    }
    return $result;
}

$available = getAvailableProducts($products);

// 3. Hiển thị
echo "DANH SÁCH SẢN PHẨM CÒN HÀNG:\n";
$total = 0;
foreach ($available as $p) {
    echo "- {$p['name']}: " . number_format($p['price'], 0, ',', '.') . " VNĐ\n";
    $total += $p['price'];
}
echo "TỔNG GIÁ TRỊ: " . number_format($total, 0, ',', '.') . " VNĐ\n";
