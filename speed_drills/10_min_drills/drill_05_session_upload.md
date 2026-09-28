# SPEED DRILL 10M-05: SESSION AUTH + UPLOAD FILE & LOCK_EX
> **Thời gian tối đa:** 10 phút | **Mục tiêu:** Viết file tiếp nhận tài liệu đính kèm yêu cầu đăng nhập, kiểm tra dung lượng, đổi tên ngẫu nhiên và ghi log an toàn.

---

## 1. ĐỀ BÀI
Tạo file `upload_doc_protected.php`:
- Bật session. Kiểm tra nếu `!isset($_SESSION['logged_user'])`, chuyển hướng ngay về `login.php` (kèm `exit;`).
- Form POST upload file `doc_file`.
- Validate file:
  - Định dạng: `pdf`, `docx`, `png`.
  - Kích thước: tối đa 2MB.
- Đổi tên: `doc_[user_id]_[uniqid].[ext]`.
- Lưu vào `storage/` và ghi 1 dòng log vào `storage.log` dùng `FILE_APPEND | LOCK_EX`: `[Thời gian] - [User ID] - [Tên file mới]`.

---

## 2. LỜI GIẢI MẪU (SOLUTION)
```php
<?php
session_start();

if (!isset($_SESSION['logged_user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['logged_user']; // ['id' => 12, 'name' => 'John']
$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['doc_file'])) {
    $f = $_FILES['doc_file'];
    $allowed = ['pdf', 'docx', 'png'];
    $maxSize = 2 * 1024 * 1024;

    if ($f['error'] !== UPLOAD_ERR_OK) {
        $err = 'Lỗi upload: mã ' . $f['error'];
    } else {
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            $err = 'Định dạng file không được phép!';
        } elseif ($f['size'] > $maxSize) {
            $err = 'Dung lượng file vượt quá giới hạn 2MB!';
        } else {
            $dir = __DIR__ . '/storage/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            $newName = sprintf("doc_%d_%s.%s", $user['id'], uniqid('', true), $ext);
            if (move_uploaded_file($f['tmp_name'], $dir . $newName)) {
                $log = sprintf("[%s] - User %d - %s\n", date('Y-m-d H:i:s'), $user['id'], $newName);
                file_put_contents(__DIR__ . '/storage.log', $log, FILE_APPEND | LOCK_EX);
                $msg = 'Upload thành công: ' . $newName;
            } else {
                $err = 'Không thể lưu file vào hệ thống.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<body>
    <h2>Upload Tài Liệu - Người dùng: <?= htmlspecialchars($user['name']) ?></h2>
    <?php if ($msg): ?><p style="color:green"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
    <?php if ($err): ?><p style="color:red"><?= htmlspecialchars($err) ?></p><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="doc_file" required><br><br>
        <button type="submit">Lưu File</button>
    </form>
</body>
</html>
```
