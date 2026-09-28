# LỜI GIẢI MINI TEST 02: AUTH SYSTEM & SESSION GUARD

### File 1: `login.php`
```php
<?php
session_start();

// Nếu đã đăng nhập rồi thì vào thẳng dashboard
if (!empty($_SESSION['auth_user'])) {
    header('Location: dashboard.php');
    exit;
}

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

$error = '';
$savedUser = $_COOKIE['saved_username'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = trim($_POST['password'] ?? '');
    $remember = isset($_POST['remember_me']);

    if ($u === 'thuthu' && $p === 'matkhau123') {
        $_SESSION['auth_user'] = $u;
        $_SESSION['flash_msg'] = "Đăng nhập thành công! Chào mừng bạn quay trở lại.";

        if ($remember) {
            setcookie('saved_username', $u, time() + 7 * 86400, '/');
        } else {
            setcookie('saved_username', '', time() - 3600, '/');
        }

        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Tài khoản hoặc mật khẩu không chính xác!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Đăng Nhập Thư Viện</title></head>
<body>
    <h2>ĐĂNG NHẬP HỆ THỐNG</h2>
    <?php if ($error): ?><p style="color:red"><?= e($error) ?></p><?php endif; ?>
    <?php if (($_GET['error'] ?? '') === 'unauthorized'): ?>
        <p style="color:red">Bạn phải đăng nhập để truy cập trang này!</p>
    <?php endif; ?>
    <?php if (($_GET['msg'] ?? '') === 'logged_out'): ?>
        <p style="color:blue">Bạn đã đăng xuất thành công.</p>
    <?php endif; ?>

    <form method="POST" action="">
        <div>
            Tài khoản: <input type="text" name="username" value="<?= e($savedUser) ?>">
        </div><br>
        <div>
            Mật khẩu: <input type="password" name="password">
        </div><br>
        <div>
            <label><input type="checkbox" name="remember_me" <?= $savedUser ? 'checked' : '' ?>> Ghi nhớ tài khoản (7 ngày)</label>
        </div><br>
        <button type="submit">Đăng Nhập</button>
    </form>
</body>
</html>
```

---

### File 2: `dashboard.php`
```php
<?php
session_start();

// 1. AUTH GUARD BẮT BUỘC
if (empty($_SESSION['auth_user'])) {
    header('Location: login.php?error=unauthorized');
    exit; // BẮT BUỘC CÓ EXIT
}

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

// 2. Xử lý Flash Message (Chỉ hiển thị 1 lần)
$flash = $_SESSION['flash_msg'] ?? null;
unset($_SESSION['flash_msg']);
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Bảng Điều Khiển</title></head>
<body>
    <?php if ($flash): ?>
        <div style="background: #e6ffed; border: 1px solid #34d058; padding: 10px; margin-bottom: 15px;">
            <?= e($flash) ?>
        </div>
    <?php endif; ?>

    <h2>BẢNG ĐIỀU KHIỂN THƯ VIỆN</h2>
    <p>Xin chào, <strong><?= e($_SESSION['auth_user']) ?></strong>! | <a href="logout.php">Đăng Xuất</a></p>
    <p>Nội dung quản lý mượn trả sách nội bộ...</p>
</body>
</html>
```

---

### File 3: `logout.php`
```php
<?php
session_start();

// 1. Xóa sạch mảng Session
$_SESSION = [];

// 2. Xóa Cookie Session trên trình duyệt
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Hủy phiên trên server
session_destroy();

// 4. Chuyển hướng
header('Location: login.php?msg=logged_out');
exit;
```
