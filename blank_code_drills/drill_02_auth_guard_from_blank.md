# BLANK-02: BẢO VỆ TRANG BẰNG AUTH GUARD TỪ FILE TRẮNG
> **Thời gian:** 5 phút | **Yêu cầu:** Gõ từ đầu file bảo vệ phiên làm việc không sai 1 chữ!

---

## 1. YÊU CẦU BÀI TẬP
Tạo file `guard.php` từ file trống:
1. Bật session nếu chưa được bật.
2. Kiểm tra nếu `!isset($_SESSION['user'])`:
   - Ghi thông báo lỗi: `$_SESSION['flash_msg'] = 'Bạn cần đăng nhập trước!'`.
   - Điều hướng về `login.php`.
   - **Bắt buộc:** Dừng ngay thực thi bằng `exit;`.
3. Nếu đã đăng nhập: Khởi tạo biến `$loggedUser = $_SESSION['user'];`.

---

## 2. LỜI GIẢI MẪU ĐỐI CHIẾU
```php
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    $_SESSION['flash_msg'] = 'Bạn cần đăng nhập trước!';
    header('Location: login.php');
    exit;
}

$loggedUser = $_SESSION['user'];
```
