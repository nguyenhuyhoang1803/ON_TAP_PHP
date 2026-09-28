# SPEED DRILL 7M-02: UPLOAD FILE AN TOÀN VỚI TÊN RANDOM
> **Thời gian tối đa:** 7 phút | **Mục tiêu:** Viết trọn vẹn luồng upload file với đầy đủ validation và lưu tên ngẫu nhiên.

---

## 1. ĐỀ BÀI
Tạo file `upload_avatar_quick.php`:
- Form upload `avatar`.
- Kiểm tra chặt chẽ:
  1. `$_FILES['avatar']['error'] === UPLOAD_ERR_OK`.
  2. Phần mở rộng chỉ cho phép: `jpg`, `jpeg`, `png`, `webp`.
  3. Kích thước tối đa: 1MB ($1 \times 1024 \times 1024$ bytes).
- Đổi tên file ngẫu nhiên dạng: `avatar_[uniqid].[ext]`.
- Di chuyển file vào thư mục `uploads/` (tự tạo nếu chưa có).
- Ghi log vào file `upload_history.log` (có khóa `LOCK_EX`): `[Thời gian] - [Tên file lưu] - [Kích thước]`.

---

## 2. LỜI GIẢI MẪU (SOLUTION)
```php
<?php
$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $f = $_FILES['avatar'];
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $maxSize = 1024 * 1024; // 1MB

    if ($f['error'] !== UPLOAD_ERR_OK) {
        $err = 'Lỗi upload: mã ' . $f['error'];
    } else {
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            $err = 'Định dạng file không được hỗ trợ.';
        } elseif ($f['size'] > $maxSize) {
            $err = 'Dung lượng file vượt quá giới hạn 1MB.';
        } else {
            $targetDir = __DIR__ . '/uploads/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

            $newName = uniqid('avatar_', true) . '.' . $ext;
            if (move_uploaded_file($f['tmp_name'], $targetDir . $newName)) {
                $log = sprintf("[%s] - %s - %d bytes\n", date('Y-m-d H:i:s'), $newName, $f['size']);
                file_put_contents(__DIR__ . '/upload_history.log', $log, FILE_APPEND | LOCK_EX);
                $msg = 'Upload thành công: ' . $newName;
            } else {
                $err = 'Không thể lưu file vào máy chủ.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<body>
    <?php if ($msg): ?><p style="color:green"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
    <?php if ($err): ?><p style="color:red"><?= htmlspecialchars($err) ?></p><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="avatar" required><br><br>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
```
