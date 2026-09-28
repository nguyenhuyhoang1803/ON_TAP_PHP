# DEBUG-03: SAI JOIN, SAI GROUP BY & LẪN LỘN WHERE VỚI HAVING
> **Thời gian:** 4 phút | **Nhiệm vụ:** Tìm 3 lỗi tư duy logic SQL trong câu truy vấn sau.

---

## 1. ĐOẠN CODE BỊ LỖI (BUGGY CODE)

```sql
-- Đề bài: "Lấy danh sách tất cả các khách hàng (kể cả khách chưa mua đơn nào) 
-- và tổng tiền đã chi. Chỉ hiển thị những người có tổng tiền >= 5 triệu."

SELECT 
    c.id, 
    c.fullname, 
    SUM(o.total_amount) AS total_spent
FROM customers c
INNER JOIN orders o ON c.id = o.customer_id
WHERE total_spent >= 5000000
GROUP BY c.fullname;
```

---

## 2. PHÂN TÍCH 3 LỖI LOGIC
1. **Lỗi 1 (Dùng `INNER JOIN` thay vì `LEFT JOIN`):** Đề bài yêu cầu *"kể cả khách chưa mua đơn nào"*, dùng `INNER JOIN` sẽ loại bỏ toàn bộ khách hàng không có đơn. Đồng thời, `SUM(o.total_amount)` cần bọc `COALESCE(..., 0)` để tránh trả về `NULL`.
2. **Lỗi 2 (Dùng `WHERE` cho hàm tổng hợp):** Không thể dùng bí danh `total_spent` hoặc hàm `SUM()` trong mệnh đề `WHERE` vì `WHERE` lọc các dòng **trước** khi gom nhóm. Bắt buộc phải dùng `HAVING` đặt sau `GROUP BY`.
3. **Lỗi 3 (`GROUP BY c.fullname` không an toàn):** Chỉ nhóm theo `c.fullname` sẽ gom nhầm những người trùng họ tên làm 1! Phải luôn luôn nhóm theo khóa chính `c.id` (hoặc `c.id, c.fullname`).

---

## 3. CÂU SQL ĐÃ SỬA CHUẨN (FIXED)

```sql
SELECT 
    c.id, 
    c.fullname, 
    COALESCE(SUM(o.total_amount), 0) AS total_spent
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id
GROUP BY c.id, c.fullname
HAVING total_spent >= 5000000
ORDER BY total_spent DESC;
```
