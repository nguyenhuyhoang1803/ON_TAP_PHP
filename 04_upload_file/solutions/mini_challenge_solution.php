<?php
// 04_upload_file/solutions/mini_challenge_solution.php

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

$error = '';
$success = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');

    if ($username === '') {
        $error = 'Vui lòng nhập Username!';
    } elseif (!isset($_FILES['cv']) || $_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Vui lòng chọn file CV hợp lệ!';
    } else {
        $file = $_FILES['cv'];
        $maxSize = 3 * 1024 * 1024; // 3MB

        if ($file['size'] > $maxSize) {
            $error = 'Dung lượng file CV không được vượt quá 3MB!';
        } else {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($ext !== 'pdf') {
                $error = 'Chỉ chấp nhận file CV định dạng PDF!';
            } else {
                $storageDir = __DIR__ . '/storage/cvs/';
                if (!is_dir($storageDir)) {
                    mkdir($storageDir, 0777, true);
                }

                // Sinh tên file: cv_[username]_[uniqid].pdf
                $safeUser = preg_replace('/[^a-zA-Z0-9_-]/', '', $username);
                $newFilename = "cv_{$safeUser}_" . uniqid() . ".pdf";
                $destination = $storageDir . $newFilename;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $success = "Tải lên CV thành công! File lưu trữ: " . $newFilename;
                    // Ghi log
                    $logFile = __DIR__ . '/storage/upload_history.log';
                    $logContent = date('Y-m-d H:i:s') . " - {$safeUser} đã upload {$newFilename}\n";
                    file_put_contents($logFile, $logContent, FILE_APPEND | LOCK_EX);
                } else {
                    $error = 'Lỗi hệ thống: Không thể lưu file vào thư mục đích!';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Nộp Hồ Sơ CV</title></head>
<body>
    <h2>Hệ Thống Tiếp Nhận Hồ Sơ CV</h2>
    <?php if ($error): ?><p style="color: red;"><?= e($error) ?></p><?php endif; ?>
    <?php if ($success): ?><p style="color: green;"><?= e($success) ?></p><?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div>
            Username: <input type="text" name="username" value="<?= e($username) ?>">
        </div><br>
        <div>
            File CV (PDF, tối đa 3MB): <input type="file" name="cv" accept=".pdf">
        </div><br>
        <button type="submit">Nộp Hồ Sơ</button>
    </form>
</body>
</html>
