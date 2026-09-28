<?php
// SANDBOX SCRATCHPAD
// Nơi gõ thử nghiệm các hàm PHP, mảng, thuật toán...

echo "=== SANDBOX TEST SCRIPT ===\n";

$students = [
    ['name' => 'Nguyễn Văn An', 'score' => 8.5],
    ['name' => 'Trần Thị Bình', 'score' => 9.0],
    ['name' => 'Lê Hoàng Cường', 'score' => 7.5],
];

echo "Danh sách sinh viên:\n";
foreach ($students as $s) {
    echo "- " . $s['name'] . ": " . $s['score'] . " điểm\n";
}
