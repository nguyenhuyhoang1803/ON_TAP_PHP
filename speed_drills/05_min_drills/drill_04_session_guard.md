# SPEED DRILL 5M-04: SESSION AUTH GUARD & FLASH MESSAGE
> **Thời gian tối đa:** 5 phút | **Mục tiêu:** Viết file bảo vệ trang (Auth Guard) không quên `exit` và xử lý thông báo 1 lần (Flash message).

---

## 1. ĐỀ BÀI
Tạo file `admin_guard.php`:
- Bật session.
- Kiểm tra nếu `$_SESSION['auth_user']` chưa tồn tại hoặc quyền (`$_SESSION['auth_user']['role']`) không phải `'admin'`:
  - Ghi vào session thông báo lỗi: `$_SESSION['flash_error'] = 'Bạn phải đăng nhập với quyền Admin!'`.
  - Chuyển hướng về `login.php`.
  - **Bắt buộc:** Phải dừng mã thực thi ngay lập tức.
- Nếu hợp lệ: Trả về mảng thông tin user để file gọi nó sử dụng tiếp.

---

## 2. LỜI GIẢI MẪU (SOLUTION)
```php
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['auth_user']) || ($_SESSION['auth_user']['role'] ?? '') !== 'admin') {
    $_SESSION['flash_error'] = 'Bạn phải đăng nhập với quyền Admin!';
    header('Location: login.php');
    exit; // CỰC KỲ QUAN TRỌNG: Không có exit là mất trọn điểm bảo mật
}

$currentUser = $_SESSION['auth_user'];
```
