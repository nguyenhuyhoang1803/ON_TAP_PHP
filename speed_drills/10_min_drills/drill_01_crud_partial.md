# SPEED DRILL 10M-01: CRUD THÊM MỚI BẮT TRÙNG KHÓA & PRG PATTERN
> **Thời gian tối đa:** 10 phút | **Mục tiêu:** Viết trọn vẹn luồng thêm mới dữ liệu qua PDO, bẫy Exception 23000 và chuyển hướng PRG.

---

## 1. ĐỀ BÀI
Tạo file `create_member.php`:
- Kết nối cơ sở dữ liệu `club_db` bảng `members(id, member_code, fullname, email, joined_date)` với `member_code` là UNIQUE.
- Nhận POST: `member_code`, `fullname`, `email`.
- Validate: Cả 3 trường không được rỗng; email đúng định dạng.
- Thực thi INSERT bằng Prepared Statement.
- Bắt lỗi: Nếu trùng `member_code` (mã lỗi `23000`), hiển thị lỗi `"Mã hội viên đã tồn tại!"`.
- Nếu thành công: Áp dụng PRG Pattern redirect sang `member_list.php?msg=success` (kèm `exit;`).

---

## 2. LỜI GIẢI MẪU (SOLUTION)
```php
<?php
$pdo = new PDO('mysql:host=localhost;dbname=club_db;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$errors = [];
$code = trim($_POST['member_code'] ?? '');
$name = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($code === '') $errors['code'] = 'Vui lòng nhập mã hội viên.';
    if ($name === '') $errors['name'] = 'Vui lòng nhập họ tên.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email không hợp lệ.';

    if (empty($errors)) {
        try {
            $sql = "INSERT INTO members (member_code, fullname, email, joined_date) VALUES (:code, :name, :email, CURDATE())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':code'  => $code,
                ':name'  => $name,
                ':email' => $email
            ]);

            header('Location: member_list.php?msg=success');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors['code'] = 'Mã hội viên này đã tồn tại trong hệ thống!';
            } else {
                $errors['db'] = 'Lỗi hệ thống: không thể thêm dữ liệu.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<body>
    <h2>Thêm Hội Viên</h2>
    <?php if (!empty($errors)): ?>
        <ul style="color:red"><?php foreach ($errors as $err): ?><li><?= htmlspecialchars($err) ?></li><?php endforeach; ?></ul>
    <?php endif; ?>
    <form method="POST">
        Mã HV: <input type="text" name="member_code" value="<?= htmlspecialchars($code) ?>"><br><br>
        Họ tên: <input type="text" name="fullname" value="<?= htmlspecialchars($name) ?>"><br><br>
        Email: <input type="text" name="email" value="<?= htmlspecialchars($email) ?>"><br><br>
        <button type="submit">Lưu Hội Viên</button>
    </form>
</body>
</html>
```
