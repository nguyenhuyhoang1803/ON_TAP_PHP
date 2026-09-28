<?php
// 09_sql_core/solutions/mini_challenge_solution.php

// Câu lệnh SQL giải quyết trọn vẹn Mini Challenge:
$sql = <<<SQL
SELECT 
    d.name AS dept_name,
    COUNT(e.id) AS total_emp,
    COALESCE(ROUND(AVG(e.salary), 2), 0) AS avg_salary
FROM departments d
LEFT JOIN employees e ON d.id = e.dept_id
GROUP BY d.id, d.name
HAVING COUNT(e.id) >= 2 OR COUNT(e.id) = 0
ORDER BY total_emp DESC;
SQL;

echo "CÂU TRUY VẤN SQL CHUẨN:\n";
echo "----------------------------------------\n";
echo $sql . "\n\n";
echo "GIẢI THÍCH CHI TIẾT:\n";
echo "1. Dùng `LEFT JOIN` để giữ lại các phòng ban chưa có nhân viên nào.\n";
echo "2. Dùng `COUNT(e.id)` để đếm đúng số nhân viên (trả về 0 nếu NULL, không dùng COUNT(*)).\n";
echo "3. Dùng `COALESCE(ROUND(AVG(e.salary), 2), 0)` để thay thế giá trị NULL thành 0 khi phòng ban không có lương.\n";
echo "4. Dùng `HAVING COUNT(e.id) >= 2 OR COUNT(e.id) = 0` để lọc nhóm theo đúng yêu cầu đề bài.\n";
