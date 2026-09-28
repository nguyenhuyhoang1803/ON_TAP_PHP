<?php
// 03_session_cookie/solutions/mini_challenge_solution.php

// File gộp giải pháp mô phỏng đầy đủ chu trình: login -> books (guard) -> logout
session_start();

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

$action = $_GET['action'] ?? 'login';

// ROUTER ĐƠN GIẢN
if ($action === 'logout') {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain']);
    }
    session_destroy();
    header('Location: ?action=login&msg=logged_out');
    exit;
}

if ($action === 'books') {
    // 1. LOGIN GUARD BẮT BUỘC
    if (empty($_SESSION['library_user'])) {
        header('Location: ?action=login&err=unauthorized');
        exit;
    }

    // Đếm lượt xem
    $_SESSION['book_views'] = ($_SESSION['book_views'] ?? 0) + 1;
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head><meta charset="UTF-8"><title>Thư Viện Sách</title></head>
    <body>
        <h2>HỆ THỐNG QUẢN LÝ THƯ VIỆN</h2>
        <p>Xin chào: <strong><?= e($_SESSION['library_user']) ?></strong> | <a href="?action=logout">Đăng xuất</a></p>
        <p>Số lần bạn đã xem trang sách này: <strong><?= $_SESSION['book_views'] ?></strong></p>
        <table border="1" cellpadding="5">
            <tr><th>Mã sách</th><th>Tên sách</th><th>Tác giả</th></tr>
            <tr><td>B01</td><td>Lập trình PHP chuyên sâu</td><td>Rasmus Lerdorf</td></tr>
            <tr><td>B02</td><td>MySQL tối ưu hóa</td><td>Michael Widenius</td></tr>
        </table>
    </body>
    </html>
    <?php
    exit;
}

// MẶC ĐỊNH: TRANG LOGIN
// Nếu user đã đăng nhập mà mở login -> redirect trực tiếp sang protected page
if (!empty($_SESSION['library_user'])) {
    header('Location: ?action=books');
    exit;
}

$error = '';
$cookieUser = $_COOKIE['remember_lib_user'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = trim($_POST['password'] ?? '');
    $remember = isset($_POST['remember']);

    if ($u === 'thuthu' && $p === 'lib123') {
        $_SESSION['library_user'] = $u;
        $_SESSION['book_views'] = 0; // Reset counter cho phiên đăng nhập mới
        
        if ($remember) {
            setcookie('remember_lib_user', $u, time() + 3 * 86400, '/');
        } else {
            setcookie('remember_lib_user', '', time() - 3600, '/');
        }

        header('Location: ?action=books');
        exit;
    } else {
        $error = 'Sai tài khoản hoặc mật khẩu! (Thử: thuthu / lib123)';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Đăng Nhập Thư Viện</title></head>
<body>
    <h2>ĐĂNG NHẬP THƯ VIỆN</h2>
    <?php if ($error): ?><p style="color: red;"><?= e($error) ?></p><?php endif; ?>
    <?php if (($_GET['msg'] ?? '') === 'logged_out'): ?><p style="color: blue;">Đã đăng xuất thành công.</p><?php endif; ?>
    <?php if (($_GET['err'] ?? '') === 'unauthorized'): ?><p style="color: red;">Vui lòng đăng nhập để vào thư viện!</p><?php endif; ?>

    <form method="POST" action="?action=login">
        <div>
            Tài khoản: <input type="text" name="username" value="<?= e($cookieUser) ?>">
        </div><br>
        <div>
            Mật khẩu: <input type="password" name="password">
        </div><br>
        <div>
            <label><input type="checkbox" name="remember" <?= $cookieUser ? 'checked' : '' ?>> Ghi nhớ tài khoản (3 ngày)</label>
        </div><br>
        <button type="submit">Đăng nhập</button>
    </form>
</body>
</html>
