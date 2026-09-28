# CHECKPOINT 04: UPLOAD FILE & ĐỌC GHI FILE AN TOÀN

> **Điều kiện vượt qua:** Đạt từ 75% trở lên (>= 7.5/10 điểm).

---

### PHẦN A – LÝ THUYẾT NHANH (5 câu x 0.6đ = 3.0đ)
1. Hãy liệt kê ý nghĩa của các mã lỗi: `UPLOAD_ERR_OK` (0), `UPLOAD_ERR_INI_SIZE` (1), và `UPLOAD_ERR_NO_FILE` (4).
2. Tại sao việc kiểm tra `$file['type']` gửi từ client lên là chưa đủ an toàn và hacker có thể dễ dàng qua mặt?
3. Hàm `pathinfo($name, PATHINFO_EXTENSION)` trả về gì? Tại sao phải bọc thêm hàm `strtolower()`?
4. Nêu sự khác nhau giữa `file_get_contents()` và `readfile()`.
5. Nếu thư mục lưu file upload không được cấp quyền ghi (write permission), hàm `move_uploaded_file()` sẽ trả về giá trị gì và sinh ra thông báo lỗi gì?

---

### PHẦN B – ĐỌC CODE & BẮT BUG (2 đoạn code x 1.0đ = 2.0đ)

#### Bug 1:
```php
if (isset($_POST['upload'])) {
    $file = $_FILES['avatar'];
    move_uploaded_file($file['tmp_name'], "uploads/" . $file['name']);
    echo "Thành công!";
}
```
*Chỉ ra 3 lỗi nguy hiểm trong đoạn code upload trên.*

#### Bug 2:
```php
// Ghi nhận lượt truy cập
$fp = fopen("hits.txt", "w");
$count = (int)file_get_contents("hits.txt");
fwrite($fp, $count + 1);
fclose($fp);
```
*Đoạn code trên bị lỗi logic gì nghiêm trọng khi đếm lượt truy cập? Sửa lại thế nào?*

---

### PHẦN C – VIẾT CODE THỰC HÀNH (3.0đ)
Viết một hàm PHP:
`function saveAvatar(array $fileInfo, string $destFolder, int $maxMb = 2): array`
- Kiểm tra `$fileInfo` có lỗi không, kích thước có $\le \$maxMb$ không, đuôi mở rộng có thuộc `['jpg', 'png', 'jpeg']` không.
- Nếu hợp lệ: Đổi tên file ngẫu nhiên, lưu vào `$destFolder`, trả về mảng `['success' => true, 'filename' => $newName]`.
- Nếu không hợp lệ: Trả về `['success' => false, 'error' => $errorMessage]`.

---

### PHẦN D – BIẾN THỂ ĐỀ THI (2.0đ)
Nếu đề bài yêu cầu: *"Cho phép người dùng upload cùng lúc nhiều ảnh (Multiple File Upload) trong cùng một ô input"*, cấu trúc của thẻ HTML và cách duyệt mảng `$_FILES['photos']` trong PHP thay đổi như thế nào? Viết đoạn code duyệt minh họa.
