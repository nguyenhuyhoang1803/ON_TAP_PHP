# CHECKPOINT 09: SQL CORE (JOIN, GROUP BY, HAVING)

> **Điều kiện vượt qua:** Đạt từ 75% trở lên (>= 7.5/10 điểm).

---

### PHẦN A – LÝ THUYẾT NHANH (5 câu x 0.6đ = 3.0đ)
1. Khi nào bắt buộc phải dùng `LEFT JOIN` thay vì `INNER JOIN`? Cho ví dụ thực tế trong đề thi.
2. Tại sao câu lệnh sau bị MySQL báo lỗi: `SELECT dept_id, AVG(salary) FROM employees WHERE AVG(salary) > 1000 GROUP BY dept_id;`?
3. Hàm `COALESCE(x, y)` hoạt động như thế nào khi biến `x` có giá trị `NULL`?
4. Trình bày cách dùng `NOT EXISTS` để tìm các sản phẩm chưa từng được mua trong bảng đơn hàng.
5. Thứ tự thực thi logic (Logical Processing Order) của một câu lệnh SQL gồm: `FROM`, `WHERE`, `GROUP BY`, `HAVING`, `SELECT`, `ORDER BY`, `LIMIT` diễn ra như thế nào?

---

### PHẦN B – ĐỌC CODE & BẮT BUG (2 đoạn code x 1.0đ = 2.0đ)

#### Bug 1:
```sql
SELECT c.name, COUNT(*) AS so_don
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id
GROUP BY c.id;
```
*Khách hàng "Nguyễn Văn A" vừa đăng ký tài khoản và chưa mua đơn nào. Câu query trên sẽ hiển thị số đơn của ông A là mấy? Tại sao sai và sửa lại như thế nào?*

#### Bug 2:
```sql
SELECT c.name, COUNT(o.id) AS so_don
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id
WHERE o.order_date >= '2026-01-01'
GROUP BY c.id, c.name;
```
*Đề bài yêu cầu: "Thống kê tất cả khách hàng và số đơn trong năm 2026 (kể cả khách chưa có đơn trong năm này)". Tại sao câu lệnh trên lại không hiển thị khách chưa có đơn?*

---

### PHẦN C – VIẾT CODE THỰC HÀNH (3.0đ)
Cho hai bảng:
- `categories` (`id`, `name`)
- `products` (`id`, `name`, `price`, `category_id`)
Viết câu lệnh SQL:
- Liệt kê: Tên danh mục, Số lượng sản phẩm của danh mục, Giá sản phẩm cao nhất (`max_price`), Giá sản phẩm thấp nhất (`min_price`).
- Phải hiển thị cả các danh mục hiện chưa có sản phẩm nào (Số lượng = 0, Max = 0, Min = 0).
- Chỉ hiển thị những danh mục có giá sản phẩm cao nhất $\ge 500.000$ VNĐ hoặc danh mục chưa có sản phẩm.
- Sắp xếp theo tên danh mục bảng chữ cái A-Z.

---

### PHẦN D – BIẾN THỂ ĐỀ THI (2.0đ)
Nếu đề bài yêu cầu: *"Tìm khách hàng đã chi tiêu nhiều tiền nhất trong hệ thống (nếu có 2 khách hàng có tổng chi tiêu bằng nhau và cùng cao nhất thì lấy cả 2 người)"*.  
Bạn sẽ viết câu SQL sử dụng Subquery như thế nào để giải quyết trường hợp đồng hạng này?
