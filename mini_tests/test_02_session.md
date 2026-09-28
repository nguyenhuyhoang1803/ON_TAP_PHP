# MINI TEST 02: SESSION, COOKIE & AUTH GUARD
- **Thời gian làm bài:** 15 phút
- **Tổng điểm:** 10 điểm
- **Mục tiêu:** Kiểm tra kỹ năng bảo vệ trang nội bộ, cơ chế redirect an toàn có `exit`, Flash message và Remember Me bằng Cookie.

---

## Đề Bài: Hệ Thống Đăng Nhập & Quản Lý Mượn Sách
Xây dựng một hệ thống mini gồm 3 file:
1. `login.php`:
   - Form đăng nhập gồm `username`, `password` và checkbox `remember_me`.
   - Tài khoản hợp lệ: `thuthu` / `matkhau123`.
   - Nếu đăng nhập đúng:
     - Lưu thông tin vào `$_SESSION['auth_user']`.
     - Lưu thông báo Flash message vào `$_SESSION['flash_msg'] = "Đăng nhập thành công!"`.
     - Nếu có tick chọn `remember_me`, lưu cookie `saved_username` trong 7 ngày. Nếu không tick, xóa cookie cũ (nếu có).
     - Chuyển hướng sang `dashboard.php`.
   - Nếu sai: Báo lỗi "Tài khoản hoặc mật khẩu không chính xác!".
2. `dashboard.php`:
   - **Auth Guard:** Bắt buộc kiểm tra đăng nhập. Nếu chưa đăng nhập, chuyển hướng ngay về `login.php?error=unauthorized` kèm lệnh `exit`.
   - Hiển thị thông báo Flash message (nếu có) và sau đó xóa flash message khỏi `$_SESSION` để F5 không hiện lại.
   - Hiển thị lời chào: "Xin chào, [username]!".
   - Có nút / link "Đăng xuất" trỏ đến `logout.php`.
3. `logout.php`:
   - Xóa sạch mảng `$_SESSION`, hủy cookie phiên và gọi `session_destroy()`.
   - Chuyển hướng về `login.php?msg=logged_out`.

---

## Test Cases & Rubric Chấm Điểm (10 điểm)

| Tiêu chí | Điểm | Test Case Kiểm Thử |
|---|:---:|---|
| **Auth Guard** | 3.0đ | Mở tab ẩn danh truy cập thẳng `dashboard.php` -> Phải bị đuổi về `login.php` |
| **Lệnh `exit` sau redirect** | 1.0đ | Kiểm tra code có `exit` ngay sau lệnh `header('Location: ...')` không |
| **Logic Đăng nhập & Flash Message** | 2.5đ | Nhập đúng -> Chuyển sang dashboard, hiện flash msg, F5 thì flash msg biến mất |
| **Cookie Remember Me** | 1.5đ | Tick remember -> F5 trang login thấy ô username tự điền sẵn tên |
| **Quy trình Logout** | 2.0đ | Bấm logout -> Session bị hủy hoàn toàn, bấm back trình duyệt không vào lại được |

---
*(Xem lời giải chi tiết tại [mini_tests/solutions/test_02_solution.md](file:///d:/MNM/OnTap/opensource_exam_training/mini_tests/solutions/test_02_solution.md))*
