# SPEED DRILL 5M-05: SQL JOIN CƠ BẢN & LỌC TRẠNG THÁI
> **Thời gian tối đa:** 5 phút | **Mục tiêu:** Viết câu lệnh SQL kết nối bảng chính xác không nhầm lẫn giữa INNER JOIN và LEFT JOIN.

---

## 1. ĐỀ BÀI
Cho 2 bảng:
- `departments(id, department_name)`
- `employees(id, department_id, fullname, salary, is_active)`
Viết câu truy vấn: Lấy mã phòng ban, tên phòng ban, họ tên nhân viên, và lương.
- Điều kiện: Chỉ lấy nhân viên đang hoạt động (`is_active = 1`).
- Yêu cầu: Phòng ban nào không có nhân viên nào đang hoạt động vẫn phải xuất hiện trong kết quả (họ tên và lương hiển thị `NULL`).
- Sắp xếp theo tên phòng ban tăng dần, lương giảm dần.

---

## 2. LỜI GIẢI MẪU (SOLUTION)
```sql
SELECT 
    d.id AS department_id,
    d.department_name,
    e.fullname,
    e.salary
FROM departments d
LEFT JOIN employees e ON d.id = e.department_id AND e.is_active = 1
ORDER BY d.department_name ASC, e.salary DESC;
```
> [!NOTE]
> **Chú ý vị trí `AND e.is_active = 1`:** Đặt trong mệnh đề `ON` của `LEFT JOIN`. Nếu đặt ở `WHERE`, toàn bộ các phòng ban không có nhân viên sẽ bị loại bỏ (vô tình biến thành INNER JOIN)!
