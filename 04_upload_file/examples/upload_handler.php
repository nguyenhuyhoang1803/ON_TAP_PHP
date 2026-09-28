<?php
// 04_upload_file/examples/upload_handler.php

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

$message = '';
$uploadDir = __DIR__ . '/uploads/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['attachment'])) {
    $f = $_FILES['attachment'];

    if ($f['error'] !== UPLOAD_ERR_OK) {
        $message = "Lỗi upload file: Mã lỗi " . $f['error'];
    } elseif ($f['size'] > 1024 * 1024) { // 1MB
        $message = "File quá lớn! Giới hạn tối đa là 1MB.";
    } else {
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'png', 'pdf'];

        if (!in_array($ext, $allowed, true)) {
            $message = "Chỉ chấp nhận các định dạng: " . implode(', ', $allowed);
        } else {
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $cleanName = uniqid('file_', true) . '.' . $ext;
            $destination = $uploadDir . $cleanName;

            if (move_uploaded_file($f['tmp_name'], $destination)) {
                $message = "Tải file lên thành công! Tên file lưu trữ: " . $cleanName;
                // Ghi log
                $logLine = date('Y-m-d H:i:s') . " | Saved: $cleanName | Size: {$f['size']} bytes\n";
                file_put_contents(__DIR__ . '/uploads.log', $logLine, FILE_APPEND | LOCK_EX);
            } else {
                $message = "Lỗi khi lưu trữ file vào thư mục đích.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Upload File Mẫu</title></head>
<body>
    <h2>Upload File Đính Kèm</h2>
    <?php if ($message): ?>
        <p><strong><?= e($message) ?></strong></p>
    <?php endif; ?>
    <form method="POST" action="" enctype="multipart/form-data">
        <input type="file" name="attachment"><br><br>
        <button type="submit">Upload ngay</button>
    </form>
</body>
</html>
