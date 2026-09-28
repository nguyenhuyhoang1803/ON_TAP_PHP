# SPEED DRILL 5M-03: PDO PREPARED STATEMENT SELECT
> **Thời gian tối đa:** 5 phút | **Mục tiêu:** Truy vấn dữ liệu có lọc tham số an toàn 100% bằng PDO.

---

## 1. ĐỀ BÀI
Tạo file `find_user.php`:
- Giả định đã có biến `$pdo`.
- Nhận tham số GET `email` và `role`.
- Truy vấn bảng `users(id, fullname, email, role, status)` với điều kiện: `email = :email AND role = :role AND status = 1`.
- Lấy ra chính xác 1 dòng duy nhất bằng `fetch()`. Nếu tìm thấy, in ra `"ID: [id] - Họ tên: [fullname]"`, nếu không thấy in `"Không tồn tại người dùng"`.

---

## 2. LỜI GIẢI MẪU (SOLUTION)
```php
<?php
// require 'db_connect.php';

$email = trim($_GET['email'] ?? '');
$role  = trim($_GET['role'] ?? '');

$sql = "SELECT id, fullname, email FROM users WHERE email = :email AND role = :role AND status = 1 LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':email' => $email,
    ':role'  => $role
]);
$user = $stmt->fetch();

if ($user) {
    echo "ID: " . (int)$user['id'] . " - Họ tên: " . htmlspecialchars($user['fullname'], ENT_QUOTES, 'UTF-8');
} else {
    echo "Không tồn tại người dùng";
}
```
