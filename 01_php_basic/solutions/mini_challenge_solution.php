<?php
// 01_php_basic/solutions/mini_challenge_solution.php

$orders = [
    ['code' => 'HD01', 'qty' => 5, 'price' => 200000],
    ['code' => 'HD02', 'qty' => 0, 'price' => 150000],
    ['code' => 'HD03', 'qty' => 2, 'price' => 500000]
];

$totalRevenue = 0;
echo "KẾT QUẢ XỬ LÝ ĐƠN HÀNG:\n";

foreach ($orders as $order) {
    if ($order['qty'] <= 0) {
        continue; // Bỏ qua đơn rác
    }
    
    $rawTotal = $order['qty'] * $order['price'];
    $discount = ($rawTotal >= 1000000) ? 0.10 : 0.0;
    $finalTotal = $rawTotal * (1 - $discount);
    
    $totalRevenue += $finalTotal;
    
    echo "- Đơn {$order['code']}: Số lượng = {$order['qty']} | Thành tiền = " 
         . number_format($finalTotal, 0, ',', '.') . " VNĐ"
         . ($discount > 0 ? " (Đã giảm 10%)\n" : "\n");
}

echo "----------------------------------------\n";
echo "TỔNG DOANH THU HỢP LỆ: " . number_format($totalRevenue, 0, ',', '.') . " VNĐ\n";
