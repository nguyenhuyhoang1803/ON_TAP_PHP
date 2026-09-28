# CHECKPOINT 12: BẢO MẬT TOÀN DIỆN PHÒNG THI

> **Điều kiện vượt qua:** Đạt từ 75% trở lên (>= 7.5/10 điểm).

---

### PHẦN A – LÝ THUYẾT NHANH (5 câu x 0.6đ = 3.0đ)
1. Hãy giải thích tại sao Prepared Statement trong PDO lại miễn nhiễm 100% với SQL Injection đối với dữ liệu người dùng?
2. Kỹ thuật Whitelist phòng chống lỗ hổng gì và tại sao Prepared Statement không thay thế được Whitelist cho mệnh đề `ORDER BY`?
3. Tại sao trong môi trường chấm thi, việc để lộ dòng `Fatal error: Uncaught PDOException` sẽ bị trừ điểm rất nặng?
4. Trình bày cách phòng chống tấn công tải lên file mã độc (Web Shell Upload).
5. Lỗ hổng Reflected XSS thường xuất hiện ở những vị trí nào trên một website bán hàng?

---

### PHẦN B – ĐỌC CODE & BẮT BUG (2 đoạn code x 1.0đ = 2.0đ)

#### Bug 1:
```php
$user = $_POST['user'];
$pass = $_POST['pass'];
$query = "SELECT * FROM accounts WHERE username = '$user' AND password = '$pass'";
$result = $pdo->query($query);
```
*Hãy viết một chuỗi payload nhập vào `$user` để đăng nhập thành công vào tài khoản `admin` mà không cần biết mật khẩu.*

#### Bug 2:
```php
<?php
$title = $_GET['title'] ?? '';
?>
<h1>Kết quả tìm kiếm cho: <?= $title ?></h1>
<input type="text" name="kw" value="<?= $title ?>">
```
*Chỉ ra lỗ hổng bảo mật và viết lại 2 dòng in an toàn.*

---

### PHẦN C – VIẾT CODE THỰC HÀNH (3.0đ)
Viết một hàm xử lý cập nhật mật khẩu người dùng:
`function updatePassword(PDO $pdo, int $userId, string $oldPassword, string $newPassword): array`
- Truy vấn lấy mật khẩu cũ trong CSDL (dùng `password_verify`).
- Kiểm tra độ dài mật khẩu mới (tối thiểu 6 ký tự).
- Băm mật khẩu mới bằng `password_hash($newPassword, PASSWORD_DEFAULT)`.
- Thực hiện Prepared Statement cập nhật.
- Tuyệt đối không để lộ thông tin lỗi CSDL nếu có ngoại lệ.

---

### PHẦN D – BIẾN THỂ ĐỀ THI (2.0đ)
Nếu đề bài yêu cầu: *"Xây dựng tính năng khôi phục mật khẩu gửi mã token qua link URL `reset.php?token=xyz`. Hãy nêu 3 biện pháp kỹ thuật để đảm bảo link này không bị hacker đoán mò hoặc sử dụng lại nhiều lần"*.
