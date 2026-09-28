# BÀI 04: UPLOAD FILE & ĐỌC GHI TẬP TIN AN TOÀN

## A. Mục tiêu
Sau bài này, bạn có thể:
1. Xử lý form upload file với thuộc tính bắt buộc `enctype="multipart/form-data"`.
2. Bắt lỗi upload qua `$_FILES['file']['error']` (kiểm tra `UPLOAD_ERR_OK`).
3. Xác thực an toàn: whitelist phần mở rộng (extension), giới hạn kích thước dung lượng file (file size), và kiểm tra MIME type.
4. Đổi tên file ngẫu nhiên bằng `uniqid()` kết hợp `move_uploaded_file()` để chống ghi đè và chống Path Traversal.
5. Đọc và ghi file dữ liệu/log bằng `file_get_contents()` và `file_put_contents()` với cờ `FILE_APPEND` và khóa `LOCK_EX`.

---

## B. Bản chất
- **Tại sao cần `enctype="multipart/form-data"`?** Thuộc tính mã hóa mặc định của form chỉ gửi chuỗi text. `multipart/form-data` báo cho trình duyệt chia nhỏ file nhị phân thành các luồng byte kèm tiêu đề để gửi lên server.
- **Khi nào dùng `uniqid()`?** Nếu 2 thí sinh cùng upload file tên `avatar.png`, file của người sau sẽ đè mất file của người trước. Sinh tên ngẫu nhiên `uniqid('img_', true)` giải quyết triệt để xung đột.
- **Tại sao cần `LOCK_EX` khi ghi file?** Trong môi trường nhiều người dùng, nếu 2 tiến trình cùng ghi vào một file log tại cùng một mili-giây, file sẽ bị phân mảnh hoặc rỗng ruột (race condition). `LOCK_EX` (Exclusive Lock) bắt tiến trình sau phải đợi tiến trình trước ghi xong.

---

## C. Cú pháp cốt lõi

### 1. Form HTML Upload
```html
<form method="POST" action="" enctype="multipart/form-data">
    <input type="file" name="avatar">
    <button type="submit">Tải lên</button>
</form>
```

### 2. Quy trình Xử lý Upload Chuẩn Phòng Thi
```php
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];

    // 1. Kiểm tra mã lỗi upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Lỗi upload hoặc chưa chọn file (Mã: ' . $file['error'] . ')';
    } else {
        // 2. Kiểm tra kích thước (Ví dụ tối đa 2MB = 2 * 1024 * 1024)
        if ($file['size'] > 2097152) {
            $errors[] = 'Dung lượng file không được vượt quá 2MB.';
        }

        // 3. Whitelist phần mở rộng
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            $errors[] = 'Chỉ chấp nhận file ảnh: jpg, jpeg, png, webp.';
        }

        // 4. Di chuyển file vào thư mục đích
        if (empty($errors)) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $newName = uniqid('avatar_', true) . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                $success = "Upload thành công file: " . $newName;
            } else {
                $errors[] = "Không thể lưu file vào thư mục đích!";
            }
        }
    }
}
```

### 3. Đọc & Ghi File An Toàn
```php
// Ghi thêm log không làm mất dữ liệu cũ, an toàn đa luồng
$logFile = __DIR__ . '/access.log';
$logEntry = date('Y-m-d H:i:s') . " - User uploaded file\n";
file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

// Đọc toàn bộ nội dung file
$content = file_exists($logFile) ? file_get_contents($logFile) : '';
```

---

## D. Ví dụ tối giản

```php
<?php
// simple_upload.php
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['doc'])) {
    $f = $_FILES['doc'];
    if ($f['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        if ($ext === 'txt' || $ext === 'pdf') {
            $dir = __DIR__ . '/uploads/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $target = $dir . uniqid() . '.' . $ext;
            move_uploaded_file($f['tmp_name'], $target);
            $msg = "Đã lưu thành công!";
        } else {
            $msg = "Chỉ nhận TXT hoặc PDF";
        }
    } else {
        $msg = "Chưa chọn file hợp lệ";
    }
}
?>
<form method="POST" enctype="multipart/form-data">
    <p><?= $msg ?></p>
    <input type="file" name="doc">
    <button type="submit">Upload</button>
</form>
```

---

## E. Luồng tư duy
```text
Thẻ <form enctype="multipart/form-data">
              ↓
Kiểm tra $_FILES['file']['error'] === UPLOAD_ERR_OK ?
       ├─ SAI: Báo lỗi mã upload
       └─ ĐÚNG:
              ↓
           Kiểm tra dung lượng ($file['size'] <= MAX_SIZE)
              ↓
           Kiểm tra đuôi file (pathinfo + in_array)
              ↓
           Tạo thư mục uploads nếu chưa có (mkdir)
              ↓
           Đổi tên file ngẫu nhiên (uniqid)
              ↓
           Di chuyển từ tmp_name sang thư mục đích (move_uploaded_file)
              ↓
           Ghi lịch sử vào file log (file_put_contents + FILE_APPEND | LOCK_EX)
```

---

## F. Những lỗi hay gặp
1. **Quên `enctype="multipart/form-data"` trên thẻ `<form>`:** Mảng `$_FILES` sẽ luôn rỗng tinh.
2. **Lưu thẳng tên gốc của client `$file['name']`:** Người dùng đặt tên tiếng Việt có dấu, khoảng trắng hoặc upload shell `hack.php` đè vào hệ thống.
3. **Dùng nhầm `copy()` hoặc `rename()` thay vì `move_uploaded_file()`:** `move_uploaded_file()` có cơ chế an toàn nội tại kiểm tra xem file có thực sự được tải lên qua HTTP POST hay không.
4. **Không tạo thư mục `uploads/` trước khi di chuyển:** `move_uploaded_file()` sẽ trả về `false` mà không rõ nguyên nhân.

---

## G. Cách nhận dạng câu hỏi
- Đề có câu: *"Cho phép người dùng upload ảnh đại diện tối đa 1MB, đổi tên file để tránh trùng lặp và lưu đường dẫn vào CSDL"* -> **Upload chuẩn: `$_FILES['avatar']` + `size <= 1048576` + `uniqid()` + `move_uploaded_file()`**.
- Đề có câu: *"Mỗi lần có người đặt hàng, ghi lại lịch sử vào file `orders.txt`"* -> **Dùng `file_put_contents('orders.txt', ..., FILE_APPEND | LOCK_EX)`**.

---

## H. Mini challenge
> **Đề bài:** Tạo script `upload_profile.php` cho phép người dùng nhập `username` và tải lên `cv` (file PDF).
> - Kiểm tra `username` không được rỗng.
> - File CV bắt buộc phải có đuôi `.pdf` và dung lượng $\le 3\text{MB}$.
> - Lưu file vào thư mục `storage/cvs/` với tên dạng: `cv_[username]_[uniqid].pdf`.
> - Ghi một dòng vào file `storage/upload_history.log`: `[Thời gian] - [username] đã upload [tên file mới]`.
> - Thông báo kết quả rõ ràng ra màn hình.  
> *(Lời giải có trong thư mục `solutions/mini_challenge_solution.php`). Tự làm trước khi xem!*
