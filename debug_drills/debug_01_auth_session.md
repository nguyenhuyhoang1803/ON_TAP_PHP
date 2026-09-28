# DEBUG-01: SESSION, AUTH GUARD & REDIRECT BYPASSING
> **Thời gian:** 3 phút | **Nhiệm vụ:** Tìm 3 lỗi nghiêm trọng trong đoạn code bảo vệ trang sau.

---

## 1. ĐOẠN CODE BỊ LỖI (BUGGY CODE)

```php
<?php
// File: admin_dashboard.php

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
}

$user = $_SESSION['username'];
?>
<h1>Chào mừng Admin <?= $user ?></h1>
<p>Dữ liệu bí mật của hệ thống: [Token: XYZ-12345]</p>
```

---

## 2. PHÂN TÍCH 3 LỖI TRÍ MẠNG
1. **Lỗi 1 (Thiếu `session_start()`):** Không gọi `session_start()` ở đầu file. Biến `$_SESSION` sẽ luôn không tồn tại hoặc báo cảnh báo `Undefined variable $_SESSION`.
2. **Lỗi 2 (Thiếu `exit;` sau redirect):** Lệnh `header('Location: login.php');` chỉ gửi header HTTP điều hướng về trình duyệt, mã PHP bên dưới **vẫn tiếp tục chạy**. Hacker có thể dùng `curl` hoặc chặn redirect để đọc toàn bộ dữ liệu bí mật phía sau!
3. **Lỗi 3 (Lỗ hổng XSS):** Xuất trực tiếp `<?= $user ?>` ra HTML mà không qua `htmlspecialchars()`.

---

## 3. CODE ĐÃ SỬA CHUẨN (FIXED)

```php
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit; // SỬA: Bắt buộc dừng thực thi ngay
}

$user = $_SESSION['username'] ?? 'Khách';
?>
<!DOCTYPE html>
<html>
<body>
    <!-- SỬA: Chống XSS an toàn -->
    <h1>Chào mừng Admin <?= htmlspecialchars($user, ENT_QUOTES, 'UTF-8') ?></h1>
    <p>Dữ liệu bí mật của hệ thống: [Token: XYZ-12345]</p>
</body>
</html>
```
