# BLANK-03: MODULE UPLOAD FILE AN TOÀN TỪ FILE TRẮNG
> **Thời gian:** 7 phút | **Yêu cầu:** Tự viết form HTML có multipart, bắt lỗi $_FILES, đổi tên ngẫu nhiên và append log.

---

## 1. YÊU CẦU BÀI TẬP
Tạo file `upload.php` từ file trống:
1. Tạo form upload method POST, thuộc tính `enctype="multipart/form-data"`. Input tên là `myfile`.
2. Tiếp nhận file khi POST:
   - Kiểm tra mã lỗi `UPLOAD_ERR_OK`.
   - Kiểm tra phần mở rộng: Chỉ cho phép `jpg`, `png`, `pdf`.
   - Giới hạn kích thước tối đa 2MB ($2 \times 1024 \times 1024$ bytes).
3. Đổi tên file ngẫu nhiên: `doc_[uniqid].[ext]`.
4. Di chuyển vào thư mục `uploads/` (tự động tạo thư mục nếu chưa có bằng `mkdir($dir, 0755, true)`).
5. Ghi nhật ký vào `upload.log` có khóa `LOCK_EX`.

---

## 2. LỜI GIẢI MẪU ĐỐI CHIẾU
```php
<?php
$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['myfile'])) {
    $file = $_FILES['myfile'];
    $allowed = ['jpg', 'png', 'pdf'];
    $maxSize = 2 * 1024 * 1024;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $err = 'Lỗi upload: ' . $file['error'];
    } else {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            $err = 'Định dạng file không được hỗ trợ (chỉ nhận JPG, PNG, PDF).';
        } elseif ($file['size'] > $maxSize) {
            $err = 'Kích thước file không được vượt quá 2MB.';
        } else {
            $dir = __DIR__ . '/uploads/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            $newName = uniqid('doc_', true) . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $dir . $newName)) {
                $log = sprintf("[%s] %s (%d bytes)\n", date('Y-m-d H:i:s'), $newName, $file['size']);
                file_put_contents(__DIR__ . '/upload.log', $log, FILE_APPEND | LOCK_EX);
                $msg = 'Upload thành công: ' . $newName;
            } else {
                $err = 'Không thể lưu file.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<body>
    <h2>Upload Tập Tin</h2>
    <?php if ($msg): ?><p style="color:green"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
    <?php if ($err): ?><p style="color:red"><?= htmlspecialchars($err) ?></p><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="myfile" required><br><br>
        <button type="submit">Tải lên</button>
    </form>
</body>
</html>
```
