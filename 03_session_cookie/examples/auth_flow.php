<?php
// 03_session_cookie/examples/auth_flow.php
session_start();

// Xử lý action login/logout/redirect HOÀN TOÀN Ở ĐẦU FILE trước mọi output HTML
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'fake_login') {
        $_SESSION['user'] = 'Giám Thị Đẹp Trai';
        header('Location: auth_flow.php');
        exit;
    } elseif ($_GET['action'] === 'logout') {
        unset($_SESSION['user']);
        header('Location: auth_flow.php');
        exit;
    }
}

// Đếm lượt truy cập trong phiên sau khi đã xử lý redirect
$_SESSION['visit_count'] = ($_SESSION['visit_count'] ?? 0) + 1;
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Session Demo</title></head>
<body>
    <h2>Trang Chủ Demo Session</h2>
    <p>Bạn đã truy cập trang này <strong><?= (int)$_SESSION['visit_count'] ?></strong> lần trong phiên làm việc hiện tại.</p>

    <?php if ($user): ?>
        <p style="color: green;">Đang đăng nhập dưới quyền: <strong><?= htmlspecialchars($user, ENT_QUOTES, 'UTF-8') ?></strong></p>
        <a href="?action=logout">Đăng xuất</a>
    <?php else: ?>
        <p style="color: orange;">Bạn đang xem với tư cách Khách (Chưa đăng nhập).</p>
        <a href="?action=fake_login">Bấm vào đây để Giả lập Đăng nhập</a>
    <?php endif; ?>
</body>
</html>
