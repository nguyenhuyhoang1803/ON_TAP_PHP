# LỜI GIẢI MINI TEST 05: PDO CRUD & DUPLICATE KEY HANDLING

```php
<?php
// mini_tests/solutions/test_05_solution.php

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

// 1. KẾT NỐI PDO CHUẨN
$host = '127.0.0.1';
$db   = 'exam_hr';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("Lỗi kết nối cơ sở dữ liệu. Vui lòng thử lại sau!");
}

$errors   = [];
$empCode  = '';
$fullname = '';
$email    = '';
$salary   = '';

// 2. XỬ LÝ POST THÊM MỚI
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empCode  = trim($_POST['emp_code'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $salary   = trim($_POST['salary'] ?? '');

    // Validate
    if ($empCode === '') {
        $errors['emp_code'] = 'Mã nhân viên không được để trống.';
    }
    if ($fullname === '') {
        $errors['fullname'] = 'Họ và tên không được để trống.';
    }
    if ($email === '') {
        $errors['email'] = 'Email không được để trống.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email không đúng định dạng.';
    }
    if ($salary === '') {
        $errors['salary'] = 'Lương cơ bản không được để trống.';
    } elseif (!is_numeric($salary) || (float)$salary <= 0) {
        $errors['salary'] = 'Lương cơ bản phải là số thực lớn hơn 0.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO employees (emp_code, fullname, email, salary) 
                                   VALUES (:code, :name, :email, :salary)");
            $stmt->execute([
                'code'   => $empCode,
                'name'   => $fullname,
                'email'  => $email,
                'salary' => (float)$salary,
            ]);

            // Áp dụng PRG: Redirect sau khi POST thành công
            header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=added');
            exit;
        } catch (PDOException $ex) {
            // 3. BẮT MÃ LỖI 23000 (DUPLICATE KEY)
            if ($ex->getCode() == 23000) {
                $errors['general'] = "Mã nhân viên [{$empCode}] hoặc Email [{$email}] đã tồn tại trong hệ thống!";
            } else {
                error_log("DB Insert Error: " . $ex->getMessage());
                $errors['general'] = "Có lỗi xảy ra trong quá trình lưu dữ liệu.";
            }
        }
    }
}

// 4. LẤY DANH SÁCH NHÂN VIÊN
$stmt = $pdo->query("SELECT * FROM employees ORDER BY id DESC");
$employees = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Quản Lý Nhân Sự</title></head>
<body>
    <h2>QUẢN LÝ NHÂN SỰ PHÒNG THI</h2>

    <?php if (isset($errors['general'])): ?>
        <p style="color: red; font-weight: bold; background: #ffe6e6; padding: 10px; border: 1px solid red;">
            <?= e($errors['general']) ?>
        </p>
    <?php endif; ?>

    <?php if (($_GET['msg'] ?? '') === 'added'): ?>
        <p style="color: green; font-weight: bold; background: #e6ffed; padding: 10px; border: 1px solid green;">
            Thêm mới nhân viên thành công!
        </p>
    <?php endif; ?>

    <form method="POST" action="">
        <div>
            Mã nhân viên (*):<br>
            <input type="text" name="emp_code" value="<?= e($empCode) ?>">
            <?php if (isset($errors['emp_code'])): ?><span style="color:red"><?= e($errors['emp_code']) ?></span><?php endif; ?>
        </div><br>

        <div>
            Họ và tên (*):<br>
            <input type="text" name="fullname" value="<?= e($fullname) ?>">
            <?php if (isset($errors['fullname'])): ?><span style="color:red"><?= e($errors['fullname']) ?></span><?php endif; ?>
        </div><br>

        <div>
            Email (*):<br>
            <input type="text" name="email" value="<?= e($email) ?>">
            <?php if (isset($errors['email'])): ?><span style="color:red"><?= e($errors['email']) ?></span><?php endif; ?>
        </div><br>

        <div>
            Lương cơ bản (VNĐ) (*):<br>
            <input type="number" step="100000" name="salary" value="<?= e($salary) ?>">
            <?php if (isset($errors['salary'])): ?><span style="color:red"><?= e($errors['salary']) ?></span><?php endif; ?>
        </div><br>

        <button type="submit">Thêm Nhân Viên</button>
    </form>

    <hr>
    <h3>DANH SÁCH NHÂN VIÊN HIỆN CÓ</h3>
    <table border="1" cellpadding="6" style="border-collapse: collapse; width: 700px;">
        <thead>
            <tr bgcolor="#f2f2f2">
                <th>ID</th>
                <th>Mã NV</th>
                <th>Họ và Tên</th>
                <th>Email</th>
                <th>Lương cơ bản</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($employees)): ?>
                <tr><td colspan="6" align="center">Chưa có nhân viên nào trong hệ thống!</td></tr>
            <?php else: ?>
                <?php foreach ($employees as $row): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= e($row['emp_code']) ?></td>
                        <td><?= e($row['fullname']) ?></td>
                        <td><?= e($row['email']) ?></td>
                        <td align="right"><?= number_format($row['salary'], 0, ',', '.') ?> VNĐ</td>
                        <td><?= $row['created_at'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
```
