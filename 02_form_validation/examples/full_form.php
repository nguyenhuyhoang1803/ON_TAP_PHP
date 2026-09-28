<?php
// 02_form_validation/examples/full_form.php

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

$errors = [];
$fullname = '';
$email = '';
$score = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $score    = trim($_POST['score'] ?? '');

    // Validate họ tên
    if ($fullname === '') {
        $errors['fullname'] = 'Họ và tên không được để trống.';
    } elseif (mb_strlen($fullname) < 3) {
        $errors['fullname'] = 'Họ và tên phải có ít nhất 3 ký tự.';
    }

    // Validate email
    if ($email === '') {
        $errors['email'] = 'Email không được để trống.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Định dạng email không hợp lệ.';
    }

    // Validate điểm (Chấp nhận từ 0 đến 10, phân số thập phân)
    if ($score === '') {
        $errors['score'] = 'Điểm số không được để trống.';
    } elseif (!is_numeric($score) || (float)$score < 0 || (float)$score > 10) {
        $errors['score'] = 'Điểm số phải là số từ 0.0 đến 10.0.';
    }

    if (empty($errors)) {
        $msgSuccess = "Dữ liệu hợp lệ! Điểm của " . e($fullname) . " là: " . (float)$score;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Form Validation Chuẩn</title></head>
<body>
    <h2>Đăng Ký Điểm Sinh Viên</h2>
    <?php if (isset($msgSuccess)): ?>
        <p style="color: green; font-weight: bold;"><?= $msgSuccess ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div>
            <label>Họ tên:</label><br>
            <input type="text" name="fullname" value="<?= e($fullname) ?>">
            <?php if (isset($errors['fullname'])): ?>
                <span style="color: red;"><?= e($errors['fullname']) ?></span>
            <?php endif; ?>
        </div>
        <br>
        <div>
            <label>Email:</label><br>
            <input type="text" name="email" value="<?= e($email) ?>">
            <?php if (isset($errors['email'])): ?>
                <span style="color: red;"><?= e($errors['email']) ?></span>
            <?php endif; ?>
        </div>
        <br>
        <div>
            <label>Điểm thi:</label><br>
            <input type="text" name="score" value="<?= e($score) ?>">
            <?php if (isset($errors['score'])): ?>
                <span style="color: red;"><?= e($errors['score']) ?></span>
            <?php endif; ?>
        </div>
        <br>
        <button type="submit">Lưu Dữ Liệu</button>
    </form>
</body>
</html>
