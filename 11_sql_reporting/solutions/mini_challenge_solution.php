<?php
// 11_sql_reporting/solutions/mini_challenge_solution.php

$sql = <<<SQL
SELECT 
    c.id, 
    c.fullname, 
    COALESCE(SUM(o.total_money), 0) AS total_spent_august
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id 
    AND o.order_date >= '2026-08-01' 
    AND o.order_date <= '2026-08-31'
GROUP BY c.id, c.fullname
HAVING total_spent_august = (
    SELECT MAX(sub.total_spent)
    FROM (
        SELECT COALESCE(SUM(o2.total_money), 0) AS total_spent
        FROM customers c2
        LEFT JOIN orders o2 ON c2.id = o2.customer_id 
            AND o2.order_date >= '2026-08-01' 
            AND o2.order_date <= '2026-08-31'
        GROUP BY c2.id
    ) sub
);
SQL;

echo "CÂU TRUY VẤN XỬ LÝ ĐỒNG HẠNG CHUẨN XÁC (DÙNG SUBQUERY MAX, KHÔNG DÙNG LIMIT 1):\n";
echo "--------------------------------------------------------\n";
echo $sql . "\n";
