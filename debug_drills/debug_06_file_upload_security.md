# DEBUG-06: FILE UPLOAD - LỖI ERROR CODE, GHI ĐÈ & RACE CONDITION
> **Thời gian:** 4 phút | **Nhiệm vụ:** Tìm 4 lỗi nguy hiểm trong module tải file sau.

---

## 1. ĐOẠN CODE BỊ LỖI (BUGGY CODE)

```php
<?php
// Form upload avatar
if (isset($_FILES['avatar'])) {
    $filename = $_FILES['avatar']['name'];
    $tmp = $_FILES['avatar']['tmp_name'];

    // Lưu file vào thư mục public
    move_uploaded_file($tmp, "uploads/" . $filename);

    // Ghi log
    $log = date('Y-m-d H:i:s') . " - Uploaded " . $filename . "\n";
    file_put_contents("upload.log", $log, FILE_APPEND);
}
```

---

## 2. PHÂN TÍCH 4 LỖI CỰC KỲ NGUY HIỂM
1. **Lỗi 1 (Không kiểm tra `error === UPLOAD_ERR_OK`):** Nếu file upload vượt quá `upload_max_filesize` trong `php.ini`, `$_FILES['avatar']` vẫn tồn tại nhưng `$tmp` bị rỗng. Lệnh `move_uploaded_file` sẽ thất bại mà không có cảnh báo.
2. **Lỗi 2 (Lỗ hổng Arbitrary File Upload):** Cho phép upload bất kỳ file nào mà không kiểm tra định dạng mở rộng hoặc MIME. Kẻ xấu có thể tải lên `shell.php` và chiếm quyền kiểm soát server.
3. **Lỗi 3 (Lưu giữ tên file gốc của người dùng):** Gây ra 2 nguy cơ:
   - Path Traversal nếu tên file là `../../var/www/malicious.php`.
   - Ghi đè file nếu 2 người dùng tải lên 2 ảnh cùng tên `avatar.png`.
4. **Lỗi 4 (Ghi log thiếu khóa độc quyền `LOCK_EX`):** Gây ra hiện tượng tranh chấp ghi (Race Condition) làm mất hoặc hỏng file nhật ký khi nhiều người upload cùng lúc.

---

## 3. CODE ĐÃ SỬA CHUẨN (FIXED)

```php
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $f = $_FILES['avatar'];

    // SỬA 1: Kiểm tra mã lỗi upload
    if ($f['error'] !== UPLOAD_ERR_OK) {
        die('Lỗi khi tải file lên máy chủ (Mã: ' . $f['error'] . ')');
    }

    // SỬA 2: Whitelist định dạng và giới hạn dung lượng
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    $maxSize = 2 * 1024 * 1024; // 2MB

    if (!in_array($ext, $allowed, true) || $f['size'] > $maxSize) {
        die('File không đúng định dạng hoặc vượt quá 2MB!');
    }

    // SỬA 3: Đổi tên file ngẫu nhiên chống ghi đè & path traversal
    $targetDir = __DIR__ . '/uploads/';
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

    $safeName = uniqid('avatar_', true) . '.' . $ext;

    if (move_uploaded_file($f['tmp_name'], $targetDir . $safeName)) {
        // SỬA 4: Ghi log có khóa LOCK_EX
        $log = sprintf("[%s] %s (%d bytes)\n", date('Y-m-d H:i:s'), $safeName, $f['size']);
        file_put_contents(__DIR__ . '/upload.log', $log, FILE_APPEND | LOCK_EX);
        echo 'Upload thành công!';
    } else {
        die('Không thể lưu file.');
    }
}
```
