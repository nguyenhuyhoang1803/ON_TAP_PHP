# LỜI GIẢI MINI TEST 06: SQL CORE & BÁO CÁO THỐNG KÊ

### Câu 1: Báo Cáo Doanh Thu Theo Danh Mục (Kể Cả Chưa Bán Được)
```sql
SELECT 
    c.id AS category_id,
    c.name AS category_name,
    COALESCE(SUM(oi.quantity), 0) AS total_qty,
    COALESCE(SUM(oi.quantity * oi.unit_price), 0) AS total_revenue
FROM categories c
LEFT JOIN products p ON c.id = p.category_id
LEFT JOIN order_items oi ON p.id = oi.product_id
GROUP BY c.id, c.name
ORDER BY total_revenue DESC;
```
> **Phân tích:** Phải dùng 2 lần `LEFT JOIN` liên tiếp: từ `categories` sang `products` và từ `products` sang `order_items`. Dùng `COALESCE(..., 0)` để chuyển các giá trị `NULL` thành `0`.

---

### Câu 2: Tìm Danh Sách Khách Hàng "Chưa Từng Đặt Đơn Hàng Nào"

#### Cách 1: Sử dụng `LEFT JOIN ... WHERE ... IS NULL`
```sql
SELECT c.id, c.fullname, c.email
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id
WHERE o.id IS NULL;
```

#### Cách 2: Sử dụng `NOT EXISTS`
```sql
SELECT c.id, c.fullname, c.email
FROM customers c
WHERE NOT EXISTS (
    SELECT 1 FROM orders o WHERE o.customer_id = c.id
);
```

---

### Câu 3: Tìm Khách Hàng Chi Tiêu Nhiều Nhất (Xử lý Đồng Hạng / Ties)
```sql
SELECT 
    c.id, 
    c.fullname, 
    c.email, 
    SUM(oi.quantity * oi.unit_price) AS total_spent
FROM customers c
JOIN orders o ON c.id = o.customer_id
JOIN order_items oi ON o.id = oi.order_id
GROUP BY c.id, c.fullname, c.email
HAVING total_spent = (
    -- Subquery tính số tiền chi tiêu lớn nhất từng có của một khách hàng
    SELECT SUM(oi2.quantity * oi2.unit_price)
    FROM customers c2
    JOIN orders o2 ON c2.id = o2.customer_id
    JOIN order_items oi2 ON o2.id = oi2.order_id
    GROUP BY c2.id
    ORDER BY SUM(oi2.quantity * oi2.unit_price) DESC
    LIMIT 1
);
```
> **Phân tích:** 
> - Subquery trong `HAVING` tìm ra con số chi tiêu cao nhất (ví dụ: `50.000.000`).
> - Mệnh đề `HAVING total_spent = (SELECT ...)` giữ lại tất cả các khách hàng có tổng chi tiêu bằng đúng con số này, không bị sót ai khi có nhiều người đồng hạng 1.
