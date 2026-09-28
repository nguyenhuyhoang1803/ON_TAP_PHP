# BÀI 11: SQL BÁO CÁO NÂNG CAO & THỐNG KÊ DOANH THU

## A. Mục tiêu
Sau bài này, bạn có thể:
1. Viết các câu lệnh thống kê phức tạp liên kết từ 3 đến 5 bảng (`JOIN` nhiều cấp).
2. Xử lý chính xác các trường hợp rỗng/NULL bằng `COALESCE()` hoặc `IFNULL()`.
3. Báo cáo doanh thu theo khoảng thời gian (theo tháng, theo năm, giữa 2 mốc ngày) kết hợp `LEFT JOIN`.
4. Tìm đối tượng đạt thành tích cao nhất (Top Spender, Top Selling Category).
5. Xử lý triệt để bài toán Đồng hạng (Ties): Lấy toàn bộ các đối tượng có cùng giá trị cao nhất bằng Subquery.

---

## B. Bản chất
- **Bài toán Đồng hạng (Ties) là gì?** Nếu đề yêu cầu *"Tìm khách hàng chi tiêu nhiều nhất"*, nếu bạn chỉ viết `ORDER BY total DESC LIMIT 1`, lỡ có 2 khách hàng cùng tiêu đúng 50.000.000 VNĐ thì bạn bị mất nửa số kết quả và bị trừ điểm. Giải pháp đúng bản chất: Dùng Subquery tìm `MAX(total_spent)` rồi lọc những ai có tổng chi tiêu bằng con số MAX đó.
- **Bẫy khoảng ngày trong `LEFT JOIN`:** Khi thống kê đơn hàng trong tháng 10 của toàn bộ khách hàng, nếu để `WHERE o.created_at BETWEEN ...` thì khách chưa mua hàng trong tháng 10 sẽ bị loại sạch! Phải đưa điều kiện ngày vào mệnh đề `ON`.

---

## C. Cú pháp cốt lõi

### 1. Báo cáo Doanh thu Khách hàng (Kèm điều kiện ngày trong ON)
```sql
SELECT 
    c.id, 
    c.name, 
    c.email,
    COUNT(o.id) AS total_orders,
    COALESCE(SUM(o.total_amount), 0) AS revenue_in_month
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id 
    AND o.created_at >= '2026-09-01' 
    AND o.created_at < '2026-10-01'
    AND o.status = 'completed'
GROUP BY c.id, c.name, c.email
ORDER BY revenue_in_month DESC;
```

### 2. Xử lý Đồng Hạng (Ties) Bằng Subquery MAX (Không dùng LIMIT 1)
```sql
SELECT s.id, s.fullname, AVG(g.score) AS avg_score
FROM students s
JOIN grades g ON s.id = g.student_id
GROUP BY s.id, s.fullname
HAVING avg_score = (
    SELECT MAX(sub.avg_val)
    FROM (
        SELECT AVG(score) AS avg_val
        FROM grades 
        GROUP BY student_id
    ) sub
);
```

### 3. Thống kê Danh mục Bán chạy nhất
```sql
SELECT 
    cat.id,
    cat.name AS category_name,
    COALESCE(SUM(oi.quantity), 0) AS total_sold_qty,
    COALESCE(SUM(oi.quantity * oi.unit_price), 0) AS total_sales
FROM categories cat
LEFT JOIN products p ON cat.id = p.category_id
LEFT JOIN order_items oi ON p.id = oi.product_id
GROUP BY cat.id, cat.name
ORDER BY total_sales DESC;
```

---

## D. Ví dụ tối giản
```sql
SELECT 
    MONTH(order_date) AS order_month,
    COUNT(id) AS total_orders,
    SUM(total_amount) AS monthly_revenue
FROM orders
WHERE YEAR(order_date) = 2026
GROUP BY MONTH(order_date)
ORDER BY order_month ASC;
```

---

## E. Luồng tư duy
```text
Xác định mục tiêu báo cáo & các bảng tham gia
              ↓
Có yêu cầu hiển thị "Tất cả kể cả chưa phát sinh" không?
   ├─ CÓ: Dùng LEFT JOIN
   │      ⚠️ CHÚ Ý: Điều kiện lọc khoảng thời gian/trạng thái phải nằm trong ON
   └─ KHÔNG: Dùng INNER JOIN
              ↓
GROUP BY các khóa chính của bảng trung tâm (id, name, email)
              ↓
Dùng COALESCE(..., 0) bọc quanh SUM() và AVG()
              ↓
Kiểm tra yêu cầu "Lấy cao nhất / Nhiều nhất":
   ├─ Đề yêu cầu duy nhất 1 người: ORDER BY ... DESC LIMIT 1
   └─ Đề yêu cầu lấy toàn bộ người cao nhất (Đồng hạng): Dùng Subquery so sánh với MAX
```

---

## F. Những lỗi hay gặp
1. **Lỗi biến `LEFT JOIN` thành `INNER JOIN`:** Đặt `WHERE o.order_date >= ...` thay vì đặt ở mệnh đề `ON`.
2. **Quên `COALESCE`:** Khi khách chưa có đơn, tổng doanh thu hiện chữ `NULL` thay vì số `0`.
3. **Chỉ dùng `LIMIT 1` khi có đồng hạng:** Bỏ sót người đứng thứ 2 có cùng điểm/tiền cao nhất.

---

## G. Cách nhận dạng câu hỏi
- Đề có câu: *"Thống kê doanh số bán hàng theo từng danh mục trong Quý 3 năm 2026, hiển thị cả danh mục chưa bán được sản phẩm nào"* -> **`categories LEFT JOIN products LEFT JOIN order_items` + điều kiện ngày ở `ON` + `COALESCE(SUM(...), 0)`**.
- Đề có câu: *"Tìm các khách hàng có tổng giá trị đơn hàng lớn nhất"* -> **Subquery tìm MAX hoặc `HAVING SUM(...) = (SELECT MAX...)`**.

---

## H. Mini challenge
> **Đề bài:** Cho 3 bảng:
> - `customers` (id, fullname)
> - `orders` (id, customer_id, order_date, total_money)
> Viết câu lệnh SQL:
> 1. Thống kê toàn bộ khách hàng và tổng tiền đã mua trong tháng 08/2026.
> 2. Khách chưa mua đơn nào trong tháng 8 phải hiển thị số tiền là 0.
> 3. Lấy ra những khách hàng có tổng tiền mua hàng bằng với mức cao nhất trong tháng 8 đó.  
> *(Lời giải có trong thư mục `solutions/mini_challenge_solution.php`). Tự làm trước khi xem!*
