# SPEED DRILL 10M-03: SQL BÁO CÁO 3 BẢNG & TIES FOR MAX
> **Thời gian tối đa:** 10 phút | **Mục tiêu:** Viết câu truy vấn đa bảng (3 bảng) xử lý đồng hạng cao nhất bằng Subquery.

---

## 1. ĐỀ BÀI
Cho 3 bảng trong hệ thống bán hàng:
- `customers(id, fullname, city)`
- `orders(id, customer_id, order_date)`
- `order_items(id, order_id, product_name, quantity, unit_price)`
Viết câu truy vấn SQL:
1. Thống kê theo từng khách hàng tại `'Hà Nội'`: Mã khách hàng, Tên khách hàng, Tổng số đơn hàng (`COUNT(DISTINCT orders.id)`), Tổng số tiền đã mua (`SUM(quantity * unit_price)`). Kể cả khách hàng chưa từng mua đơn nào cũng phải hiển thị (tiền mua = 0).
2. Tìm khách hàng (hoặc các khách hàng nếu đồng hạng) có tổng số tiền mua cao nhất tại Hà Nội. Tuyệt đối không dùng `LIMIT 1`.

---

## 2. LỜI GIẢI MẪU (SOLUTION)
```sql
-- 1. Thống kê theo khách hàng Hà Nội
SELECT 
    c.id,
    c.fullname,
    COUNT(DISTINCT o.id) AS total_orders,
    COALESCE(SUM(oi.quantity * oi.unit_price), 0) AS total_spent
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id
LEFT JOIN order_items oi ON o.id = oi.order_id
WHERE c.city = 'Hà Nội'
GROUP BY c.id, c.fullname
ORDER BY total_spent DESC;

-- 2. Khách hàng chi tiêu cao nhất (xử lý đồng hạng)
SELECT 
    c.id,
    c.fullname,
    COALESCE(SUM(oi.quantity * oi.unit_price), 0) AS max_spent
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id
LEFT JOIN order_items oi ON o.id = oi.order_id
WHERE c.city = 'Hà Nội'
GROUP BY c.id, c.fullname
HAVING max_spent = (
    SELECT MAX(sub.total_spent)
    FROM (
        SELECT COALESCE(SUM(oi2.quantity * oi2.unit_price), 0) AS total_spent
        FROM customers c2
        LEFT JOIN orders o2 ON c2.id = o2.customer_id
        LEFT JOIN order_items oi2 ON o2.id = oi2.order_id
        WHERE c2.city = 'Hà Nội'
        GROUP BY c2.id
    ) sub
);
```
