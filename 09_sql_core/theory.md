# BÀI 09: SQL CORE - JOIN, GROUP BY, HAVING & AGGREGATES

## A. Mục tiêu
Sau bài này, bạn có thể:
1. Phân biệt chính xác tuyệt đối khi nào dùng `INNER JOIN` và khi nào bắt buộc dùng `LEFT JOIN`.
2. Truy vấn tìm các bản ghi "chưa từng tồn tại quan hệ" (Khách hàng chưa từng mua đơn nào, Sản phẩm chưa từng bán được) bằng `LEFT JOIN ... WHERE ... IS NULL` hoặc `NOT EXISTS`.
3. Thành thạo mệnh đề `GROUP BY` kết hợp các hàm tổng hợp: `COUNT()`, `SUM()`, `AVG()`, `ROUND()`, `MIN()`, `MAX()`.
4. Phân biệt ranh giới rõ ràng giữa điều kiện dòng `WHERE` và điều kiện nhóm `HAVING`.
5. Viết Subquery (truy vấn con) lồng nhau và kết hợp sắp xếp `ORDER BY` + `LIMIT`.

---

## B. Bản chất
- **INNER JOIN vs LEFT JOIN:**
  - `INNER JOIN`: Chỉ lấy những dòng mà cả 2 bảng ĐỀU CÓ DỮ LIỆU KHỚP NHAU. Nếu khách hàng chưa có đơn, khách hàng đó BỊ LOẠI BỎ.
  - `LEFT JOIN`: Giữ lại 100% các dòng ở bảng bên trái (Bảng chính). Nếu bảng bên phải không có dữ liệu khớp, các cột của bảng bên phải tự động điền `NULL`.
- **WHERE vs HAVING:**
  - `WHERE`: Lọc từng dòng đơn lẻ **TRƯỚC KHI** gom nhóm. Không bao giờ được đặt hàm tổng hợp (`SUM`, `COUNT`) trong `WHERE`.
  - `HAVING`: Lọc các nhóm kết quả **SAU KHI** đã gom nhóm và tính toán hàm tổng hợp xong.

---

## C. Cú pháp cốt lõi

### 1. LEFT JOIN & Bẫy `COUNT(*)` vs `COUNT(cột)`
```sql
-- Lấy tất cả khách hàng và số đơn hàng của họ (Kể cả khách chưa có đơn)
SELECT c.id, c.name, COUNT(o.id) AS total_orders, COALESCE(SUM(o.amount), 0) AS total_spent
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id
GROUP BY c.id, c.name;
```
> ⚠️ **BẪY CỰC LỚN:** Phải dùng `COUNT(o.id)`, KHÔNG ĐƯỢC dùng `COUNT(*)`. Vì với khách chưa có đơn, dòng đó toàn `NULL`. `COUNT(*)` sẽ đếm dòng NULL là `1`, trong khi `COUNT(o.id)` bỏ qua `NULL` và trả về đúng `0`!

### 2. Tìm bản ghi chưa từng có quan hệ (Khách chưa mua gì)
```sql
-- Cách 1: LEFT JOIN + IS NULL
SELECT c.id, c.name, c.email
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id
WHERE o.id IS NULL;

-- Cách 2: NOT EXISTS (Rất tối ưu và tự nhiên)
SELECT c.id, c.name, c.email
FROM customers c
WHERE NOT EXISTS (
    SELECT 1 FROM orders o WHERE o.customer_id = c.id
);
```

### 3. GROUP BY + HAVING
```sql
-- Tìm các danh mục có từ 3 sản phẩm trở lên VÀ giá trung bình > 500.000 VNĐ
SELECT cat.id, cat.name, COUNT(p.id) AS total_products, ROUND(AVG(p.price), 2) AS avg_price
FROM categories cat
INNER JOIN products p ON cat.id = p.category_id
WHERE p.status = 1                             -- WHERE: Lọc sản phẩm đang kinh doanh
GROUP BY cat.id, cat.name                      -- Gom theo danh mục
HAVING COUNT(p.id) >= 3 AND AVG(p.price) > 500000 -- HAVING: Lọc nhóm
ORDER BY avg_price DESC;
```

---

## D. Ví dụ tối giản

```sql
-- Lấy top 3 sinh viên có điểm trung bình cao nhất khoa CNTT
SELECT s.id, s.fullname, ROUND(AVG(g.score), 2) AS avg_score
FROM students s
INNER JOIN grades g ON s.id = g.student_id
WHERE s.faculty = 'CNTT'
GROUP BY s.id, s.fullname
HAVING AVG(g.score) >= 7.0
ORDER BY avg_score DESC
LIMIT 3;
```

---

## E. Luồng tư duy
```text
Xác định các bảng cần lấy dữ liệu
              ↓
Cần lấy cả những mục chưa có quan hệ không?
   ├─ CÓ (kể cả chưa có đơn, chưa có điểm): Bắt buộc dùng LEFT JOIN
   └─ KHÔNG (chỉ lấy mục đã có giao dịch): Dùng INNER JOIN
              ↓
Cần gom nhóm tính tổng / đếm / trung bình không?
   ├─ CÓ: Thêm mệnh đề GROUP BY các cột định danh (id, name)
   └─ Điều kiện lọc có chứa hàm COUNT/SUM/AVG không?
         ├─ CÓ: Bắt buộc đặt ở HAVING
         └─ KHÔNG: Đặt ở WHERE
              ↓
Sắp xếp (ORDER BY) & Giới hạn số lượng (LIMIT)
```

---

## F. Những lỗi hay gặp
1. **Đặt điều kiện bảng phụ trong `WHERE`:** Viết `LEFT JOIN orders o ON ... WHERE o.status = 'paid'` làm các dòng có `o.id IS NULL` bị loại sạch, biến `LEFT JOIN` thành `INNER JOIN`. *(Khắc phục: Đưa điều kiện vào `ON o.status = 'paid'`)*.
2. **Dùng `COUNT(*)` khi `LEFT JOIN`:** Làm cho khách hàng chưa có đơn nào vẫn bị đếm là có 1 đơn.
3. **Quên liệt kê các cột không tổng hợp vào `GROUP BY`:** Trong MySQL với cờ `ONLY_FULL_GROUP_BY`, câu query sẽ bị báo lỗi nếu SELECT cột mà không có trong GROUP BY.
4. **Viết `WHERE COUNT(*) > 5`:** Báo lỗi `Invalid use of group function`. Bắt buộc phải là `HAVING COUNT(*) > 5`.

---

## G. Cách nhận dạng câu hỏi
- Đề có câu: *"Hiển thị toàn bộ danh sách lớp và sĩ số của từng lớp (kể cả lớp chưa có sinh viên nào)"* -> **`FROM classes c LEFT JOIN students s ON ... GROUP BY c.id` kèm `COUNT(s.id)`**.
- Đề có câu: *"Tìm các sản phẩm chưa từng có khách hàng nào đặt mua"* -> **`products p LEFT JOIN order_details d ON ... WHERE d.product_id IS NULL`**.
- Đề có câu: *"Tìm những khách hàng có tổng chi tiêu trên 10 triệu đồng"* -> **`GROUP BY c.id HAVING SUM(o.total_amount) > 10000000`**.

---

## H. Mini challenge
> **Đề bài:** Cho 2 bảng:
> - `departments` (id, name)
> - `employees` (id, name, salary, dept_id)
> Viết một câu lệnh SQL duy nhất để:
> 1. Hiển thị tên phòng ban (`dept_name`), số lượng nhân viên (`total_emp`), và mức lương trung bình làm tròn 2 số thập phân (`avg_salary`).
> 2. Phải hiển thị cả các phòng ban hiện tại chưa có nhân viên nào (số lượng = 0, lương tb = 0).
> 3. Chỉ lấy các phòng ban có từ 2 nhân viên trở lên HOẶC phòng ban chưa có ai.
> 4. Sắp xếp giảm dần theo số lượng nhân viên.  
> *(Lời giải có trong thư mục `solutions/mini_challenge_solution.php`). Tự làm trước khi xem!*
