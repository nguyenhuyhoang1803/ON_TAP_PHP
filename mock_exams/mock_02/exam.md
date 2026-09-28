# ĐỀ THI THỬ 02 (MOCK EXAM 02 - CHUẨN ĐỀ THI 60 PHÚT)
> **Mức độ:** Tương đương 100% đề thi chính thức.  
> **Thời gian:** 60 phút | **Tổng điểm:** 10.0 điểm | **Hình thức:** Thực hành trên máy

---

## Bối Cảnh Bài Toán: Quản Lý Khóa Học & Học Viên (EduCenter)
Trung tâm tin học EduCenter cần một ứng dụng web quản lý danh sách học viên, phân trang, lọc và thống kê học viên theo từng khóa học. CSDL mẫu được cung cấp tại file `schema.sql`.

---

## CÁC YÊU CẦU ĐỀ THI

### Câu 1: Xác Thực & Bảo Vệ Trang Quản Trị (2.0 điểm)
- Viết trang `login.php`:
  - Đăng nhập với tài khoản cố định: `admin` / `admin@2026`.
  - Có checkbox "Ghi nhớ tài khoản" (lưu cookie `remember_admin` trong 7 ngày).
  - Đăng nhập đúng: Lưu `$_SESSION['admin_user']`, lưu thông báo flash `$_SESSION['flash'] = "Đăng nhập thành công!"` và redirect sang `students.php`.
- Viết trang `logout.php`: Hủy toàn bộ session, redirect về `login.php`.
- Tại đầu trang `students.php`: Triển khai Auth Guard (nếu chưa đăng nhập thì chuyển về `login.php` và dừng chương trình bằng `exit`).

### Câu 2: Thêm Học Viên & Xử Lý Trùng Email / SĐT (3.0 điểm)
- Viết trang `add_student.php`:
  - Form gồm: `Họ và tên` (text), `Email` (text), `Số điện thoại` (text), `Khóa học đăng ký` (select box lấy từ bảng `courses`).
  - **Validation:**
    - Tất cả các trường bắt buộc nhập.
    - Email đúng định dạng.
    - Số điện thoại phải đúng 10 chữ số và bắt đầu bằng số `0` (Regex: `/^0[0-9]{9}$/`).
  - **Thực thi:**
    - Dùng PDO Prepared Statement để thêm vào bảng `students`.
    - Bắt ngoại lệ PDO khi trùng Email hoặc Số điện thoại (bắt mã `23000`) -> Báo lỗi đỏ rõ ràng, không làm sập website.
    - Áp dụng kỹ thuật Post/Redirect/Get (PRG) chuyển hướng về `students.php?msg=added`.
    - Sticky Form: Giữ lại dữ liệu đã nhập nếu có lỗi.

### Câu 3: Tìm Kiếm, Sắp Xếp Whitelist & Phân Trang (2.5 điểm)
- Tại trang `students.php`:
  - Hiển thị bảng danh sách học viên gồm: `Mã SV`, `Họ tên`, `Email`, `Số điện thoại`, `Tên khóa học` (`LEFT JOIN`), `Ngày đăng ký`.
  - **Tìm kiếm:** Theo `Họ tên` HOẶC `Email` (escape `%` và `_` trong LIKE).
  - **Sắp xếp Whitelist:** Theo `id`, `fullname`, `created_at` (chiều `ASC`/`DESC`). Mặc định là `id DESC`.
  - **Phân trang:** Hiển thị 5 học viên / trang.
  - **Bắt buộc:** Giữ toàn bộ tham số tìm kiếm và sắp xếp khi chuyển giữa các trang bằng hàm `http_build_query()`.
  - Thuật toán kẹp `page` trong khoảng `[1, totalPages]`.

### Câu 4: SQL Thống Kê Khóa Học (2.5 điểm)
- Viết file `report.php`:
  - Viết 1 câu lệnh SQL duy nhất để thống kê tất cả các khóa học trong hệ thống: `Mã khóa học`, `Tên khóa học`, `Học phí`, `Số lượng học viên đã đăng ký`.
  - **Yêu cầu quan trọng:** Bắt buộc hiển thị cả các khóa học **chưa có học viên nào đăng ký** (với số lượng học viên = 0).
  - Sắp xếp giảm dần theo số lượng học viên.
  - Hiển thị thông tin khóa học nào hiện đang có số lượng học viên đông nhất.

---

## PHẦN DÀNH CHO GIÁM THỊ: BẪY & TEST CASES
1. **Kiểm tra Auth Guard:** Mở tab ẩn danh truy cập `http://localhost/students.php` -> Nếu không bị chặn về `login.php` thì trừ 2.0 điểm Câu 1.
2. **Kiểm tra SQLi ở Sort:** Giám thị gõ `?sort=password` hoặc `?sort=id; DROP TABLE...` -> Nếu không qua whitelist thì trừ 1.0 điểm Câu 3.
3. **Kiểm tra bẫy `COUNT(*)`:** Ở Câu 4, nếu khóa học chưa có ai mà hiển thị là `1 học viên` (do dùng `COUNT(*)`) thì trừ 1.5 điểm Câu 4.
4. **Kiểm tra mất keyword:** Tìm kiếm tên "An", bấm sang trang 2 -> Nếu danh sách bị reset về tất cả thì trừ 1.0 điểm Câu 3.

---
*(Xem schema tại [schema.sql](file:///d:/MNM/OnTap/opensource_exam_training/mock_exams/mock_02/schema.sql) và lời giải tại [solutions/solution.md](file:///d:/MNM/OnTap/opensource_exam_training/mock_exams/mock_02/solutions/solution.md))*
