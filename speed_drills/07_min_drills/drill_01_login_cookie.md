# SPEED DRILL 7M-01: ĐĂNG NHẬP + REMEMBER ME COOKIE
> **Thời gian tối đa:** 7 phút | **Mục tiêu:** Xử lý xác thực Form POST, Session và Cookie 7 ngày.

---

## 1. ĐỀ BÀI
Tạo file `login_remember.php`:
- Form POST: `username`, `password`, checkbox `remember`.
- Tài khoản mẫu hợp lệ: `tester` / `secure123`.
- Nếu đăng nhập đúng:
  - Lưu `$_SESSION['logged_in_user'] = $username`.
  - Nếu có chọn checkbox `remember`: Lưu cookie `remembered_username` có hạn 7 ngày. Nếu không chọn, hủy cookie nếu trước đó đã có.
  - Redirect sang `welcome.php` (kèm `exit;`).
- Nếu sai: Báo lỗi `"Tài khoản hoặc mật khẩu không chính xác"`.
- Giá trị của `username` trong form tự động điền giá trị từ cookie `remembered_username` nếu có.

---

## 2. LỜI GIẢI MẪU (SOLUTION)
```php
<?php
session_start();

$remembered = $_COOKIE['remembered_username'] ?? '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = trim($_POST['password'] ?? '');
    $rem = isset($_POST['remember']);

    if ($u === 'tester' && $p === 'secure123') {
        $_SESSION['logged_in_user'] = $u;
        if ($rem) {
            setcookie('remembered_username', $u, time() + (7 * 86400), '/');
        } else {
            setcookie('remembered_username', '', time() - 3600, '/');
        }
        header('Location: welcome.php');
        exit;
    } else {
        $error = 'Tài khoản hoặc mật khẩu không chính xác!';
    }
}
?>
<!DOCTYPE html>
<html>
<body>
    <h2>Đăng Nhập Hệ Thống</h2>
    <?php if ($error): ?><p style="color:red"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="POST">
        Username: <input type="text" name="username" value="<?= htmlspecialchars($remembered) ?>"><br><br>
        Password: <input type="password" name="password"><br><br>
        <label><input type="checkbox" name="remember" <?= $remembered ? 'checked' : '' ?>> Ghi nhớ 7 ngày</label><br><br>
        <button type="submit">Đăng nhập</button>
    </form>
</body>
</html>
```
