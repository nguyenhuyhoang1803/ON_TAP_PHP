# CHECKPOINT 03: SESSION, COOKIE & BẢO VỆ TRANG

> **Điều kiện vượt qua:** Đạt từ 75% trở lên (>= 7.5/10 điểm).

---

### PHẦN A – LÝ THUYẾT NHANH (5 câu x 0.6đ = 3.0đ)
1. Hàm `session_start()` có nhiệm vụ gì và điều kiện tiên quyết khi gọi hàm này là gì?
2. Tại sao nếu không có lệnh `exit;` ngay sau `header('Location: login.php');` thì trang web có thể bị tấn công vượt quyền (Bypass Auth Guard)?
3. Cookie được lưu trữ ở đâu (Client hay Server) và Session data được lưu trữ ở đâu?
4. Muốn xóa hoàn toàn một Cookie có tên là `user_token` trên trình duyệt, ta phải truyền các tham số như thế nào vào hàm `setcookie()`?
5. Kỹ thuật "Flash Message" (thông báo một lần sau khi redirect ví dụ: "Cập nhật thành công") được triển khai bằng `$_SESSION` như thế nào?

---

### PHẦN B – ĐỌC CODE & BẮT BUG (2 đoạn code x 1.0đ = 2.0đ)

#### Bug 1:
```php
<?php
echo "<h3>Trang quản trị</h3>";
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
}
?>
```
*Chỉ ra 2 lỗi nghiêm trọng trong đoạn code trên.*

#### Bug 2:
```php
// Đoạn code logout:
session_start();
unset($_SESSION);
header("Location: login.php");
exit;
```
*Tại sao việc gán `unset($_SESSION);` là một sai lầm trong PHP và cách sửa chuẩn là gì?*

---

### PHẦN C – VIẾT CODE THỰC HÀNH (3.0đ)
Viết script kiểm soát lượt đoán số trong phiên:
- Hệ thống sinh ngẫu nhiên một số bí mật từ 1 đến 100 lưu vào `$_SESSION['secret_number']` (chỉ sinh 1 lần khi bắt đầu phiên).
- `$_SESSION['attempts']` lưu số lần đã đoán.
- Người dùng nhập số vào form:
  - Nếu đoán đúng: Báo chúc mừng và reset game.
  - Nếu số nhập < số bí mật: Báo "Bạn cần đoán số LỚN hơn".
  - Nếu số nhập > số bí mật: Báo "Bạn cần đoán số NHỎ hơn".
  - Tối đa 7 lượt đoán, nếu vượt quá 7 lượt mà chưa đúng thì báo Thua và hiện số bí mật.

---

### PHẦN D – BIẾN THỂ ĐỀ THI (2.0đ)
Nếu đề bài yêu cầu: *"Chỉ cho phép tài khoản có quyền `role === 'admin'` truy cập trang quản trị, còn tài khoản có `role === 'staff'` chỉ được xem mà không được xóa, khách chưa đăng nhập phải chuyển về login"*.  
Hãy viết hàm `checkPermission(string $requiredRole)` chuẩn để nhúng vào đầu các trang tương ứng.
