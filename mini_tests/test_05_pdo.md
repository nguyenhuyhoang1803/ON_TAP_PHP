# MINI TEST 05: PDO CRUD & BẮT LỖI TRÙNG KHÓA
- **Thời gian làm bài:** 15 phút
- **Tổng điểm:** 10 điểm
- **Mục tiêu:** Kiểm tra kỹ năng kết nối PDO chuẩn `utf8mb4`, dựng chức năng Thêm mới nhân viên, bắt ngoại lệ SQLSTATE 23000 khi trùng email, và áp dụng mô hình PRG.

---

## Đề Bài: Quản Lý Nhân Sự (Employee Management)
Tạo cơ sở dữ liệu `exam_hr` và bảng `employees`:
```sql
CREATE DATABASE IF NOT EXISTS exam_hr CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE exam_hr;
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_code VARCHAR(20) UNIQUE NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    salary DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

Viết ứng dụng quản trị trong file `employees.php`:
1. **Kết nối CSDL bằng PDO:** Có `ERRMODE_EXCEPTION`, `utf8mb4`, tắt `EMULATE_PREPARES`.
2. **Form Thêm mới nhân viên gồm:** Mã nhân viên (`emp_code`), Họ tên (`fullname`), Email (`email`), Lương cơ bản (`salary`).
3. **Validation trước khi Insert:**
   - Mã nhân viên không được rỗng.
   - Họ tên không được rỗng.
   - Email đúng định dạng qua `filter_var(..., FILTER_VALIDATE_EMAIL)`.
   - Lương phải là số thực $> 0$.
4. **Xử lý Insert & Xử lý Trùng Khóa (Duplicate Key):**
   - Dùng Prepared Statement 100%.
   - Nếu vi phạm ràng buộc UNIQUE của `emp_code` hoặc `email` -> Bắt mã lỗi ngoại lệ `23000` và hiển thị thông báo lỗi thân thiện màu đỏ: "Mã nhân viên hoặc Email đã tồn tại trong hệ thống!".
   - Không được để lộ thông báo lỗi database nội bộ (`$e->getMessage()`).
5. **Mẫu thiết kế Post/Redirect/Get (PRG):**
   - Sau khi thêm thành công, chuyển hướng bằng `header('Location: employees.php?msg=added'); exit;`.
6. **Hiển thị danh sách:**
   - Bảng hiển thị danh sách toàn bộ nhân viên sắp xếp theo `id DESC`.
   - Cột lương được định dạng tiền tệ Việt Nam.
   - Chống XSS khi hiển thị họ tên và mã nhân viên.

---

## Test Cases & Rubric Chấm Điểm (10 điểm)

| Tiêu chí | Điểm | Test Case Kiểm Thử |
|---|:---:|---|
| **Cấu hình Kết nối PDO** | 1.5đ | Đầy đủ DSN UTF-8, cấu hình ngoại lệ và tắt giả lập prepare |
| **Validation Form & Sticky** | 2.0đ | Bắt lỗi email sai định dạng, lương âm; giữ lại dữ liệu khi lỗi |
| **Prepared Statement Insert** | 2.0đ | 100% không nối chuỗi SQL |
| **Bắt Lỗi Trùng Khóa 23000** | 2.5đ | Thêm 2 nhân viên cùng email -> Không văng Fatal Error, báo đỏ thân thiện |
| **Mẫu thiết kế PRG** | 1.0đ | Bấm submit thành công -> Bấm F5 không bị hỏi gửi lại form |
| **Hiển thị & Chống XSS** | 1.0đ | `htmlspecialchars` đầy đủ, format lương đẹp mắt |

---
*(Xem lời giải chi tiết tại [mini_tests/solutions/test_05_solution.md](file:///d:/MNM/OnTap/opensource_exam_training/mini_tests/solutions/test_05_solution.md))*
