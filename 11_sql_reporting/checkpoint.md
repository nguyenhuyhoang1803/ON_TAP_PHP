# CHECKPOINT 11: SQL BÁO CÁO NÂNG CAO

> **Điều kiện vượt qua:** Đạt từ 75% trở lên (>= 7.5/10 điểm).

---

### PHẦN A – LÝ THUYẾT NHANH (5 câu x 0.6đ = 3.0đ)
1. Hàm `COALESCE()` có thể nhận bao nhiêu tham số và nguyên tắc trả về giá trị của nó là gì?
2. Khi nào nên dùng `COUNT(DISTINCT p.id)` thay vì `COUNT(p.id)` khi thực hiện JOIN 3 bảng: `categories` -> `products` -> `order_items`?
3. Tại sao trong báo cáo thống kê theo tháng, ta nên dùng `>= '2026-08-01' AND < '2026-09-01'` thay vì `BETWEEN '2026-08-01' AND '2026-08-31'` đối với cột kiểu `DATETIME`?
4. Trình bày tư duy giải bài toán "Tìm danh mục sản phẩm có doanh thu thấp nhất (nhưng phải lớn hơn 0)".
5. Khi nào mệnh đề `GROUP BY` đòi hỏi phải nhóm theo cả `id` và `name` của bảng?

---

### PHẦN B – ĐỌC CODE & BẮT BUG (2 đoạn code x 1.0đ = 2.0đ)

#### Bug 1:
```sql
SELECT c.name, SUM(o.amount) AS total
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id
WHERE o.order_date BETWEEN '2026-01-01' AND '2026-12-31'
GROUP BY c.id, c.name;
```
*Đề bài yêu cầu hiển thị cả khách chưa có đơn trong năm 2026 với số tiền là 0. Câu trên sai ở đâu và sửa lại thế nào?*

#### Bug 2:
```sql
SELECT dept_id, MAX(salary)
FROM employees
WHERE salary = MAX(salary)
GROUP BY dept_id;
```
*Câu lệnh trên bị MySQL báo lỗi cú pháp gì? Sửa lại thế nào để tìm nhân viên có lương cao nhất mỗi phòng ban?*

---

### PHẦN C – VIẾT CODE THỰC HÀNH (3.0đ)
Cho 3 bảng:
- `authors` (`id`, `name`)
- `books` (`id`, `title`, `author_id`, `price`)
- `order_details` (`order_id`, `book_id`, `quantity`)
Viết câu lệnh SQL thống kê:
- Tên tác giả, Tổng số đầu sách đã xuất bản, Tổng số lượng sách đã bán được ra thị trường.
- Hiển thị cả tác giả chưa xuất bản sách nào, và tác giả đã có sách nhưng chưa bán được cuốn nào.
- Sắp xếp theo số lượng sách bán được giảm dần.

---

### PHẦN D – BIẾN THỂ ĐỀ THI (2.0đ)
Nếu đề bài yêu cầu: *"Xếp hạng (Ranking) các khách hàng theo tổng chi tiêu: Nếu chi tiêu >= 20tr thì xếp loại 'Kim cương', từ 10tr đến dưới 20tr là 'Vàng', dưới 10tr là 'Bạc', chưa mua gì là 'Tiềm năng'"*.  
Bạn sẽ sử dụng cấu trúc `CASE WHEN ... THEN ... ELSE ... END` trong câu lệnh SQL như thế nào?
