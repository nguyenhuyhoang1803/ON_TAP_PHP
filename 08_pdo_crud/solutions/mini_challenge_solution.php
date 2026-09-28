<?php
// 08_pdo_crud/solutions/mini_challenge_solution.php

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

// Giả lập kết nối CSDL (Cần database 'test' hoặc tương đương trong MySQL)
try {
    $pdo = new PDO("mysql:host=localhost;dbname=test;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Tạo bảng nếu chưa tồn tại để test
    $pdo->exec("CREATE TABLE IF NOT EXISTS mini_products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(50) UNIQUE NOT NULL,
        name VARCHAR(150) NOT NULL,
        price DECIMAL(12,2) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

$error = '';
$code = '';
$name = '';
$price = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code  = trim($_POST['code'] ?? '');
    $name  = trim($_POST['name'] ?? '');
    $price = trim($_POST['price'] ?? '');

    if ($code === '' || $name === '' || $price === '') {
        $error = 'Vui lòng nhập đầy đủ tất cả các trường!';
    } elseif (!is_numeric($price) || (float)$price < 0) {
        $error = 'Giá bán phải là số không âm!';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO mini_products (code, name, price) VALUES (:c, :n, :p)");
            $stmt->execute([
                'c' => $code,
                'n' => $name,
                'p' => (float)$price
            ]);

            // Áp dụng PRG: Chuyển hướng sau khi POST thành công
            header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=success');
            exit;
        } catch (PDOException $ex) {
            if ($ex->getCode() == 23000) {
                $error = "Mã sản phẩm [{$code}] đã tồn tại trong hệ thống. Vui lòng chọn mã khác!";
            } else {
                error_log($ex->getMessage());
                $error = "Có lỗi xảy ra khi lưu vào CSDL!";
            }
        }
    }
}

// Lấy danh sách sản phẩm để hiển thị
$products = $pdo->query("SELECT * FROM mini_products ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Quản Lý Sản Phẩm Mini</title></head>
<body>
    <h2>Thêm Mới Sản Phẩm (Chống Trùng Mã)</h2>

    <?php if ($error): ?><p style="color: red; font-weight: bold;"><?= e($error) ?></p><?php endif; ?>
    <?php if (($_GET['msg'] ?? '') === 'success'): ?>
        <p style="color: green; font-weight: bold;">Thêm sản phẩm thành công!</p>
    <?php endif; ?>

    <form method="POST" action="">
        <div>
            Mã sản phẩm: <input type="text" name="code" value="<?= e($code) ?>">
        </div><br>
        <div>
            Tên sản phẩm: <input type="text" name="name" value="<?= e($name) ?>">
        </div><br>
        <div>
            Giá bán: <input type="number" step="1000" name="price" value="<?= e($price) ?>">
        </div><br>
        <button type="submit">Lưu Sản Phẩm</button>
    </form>

    <hr>
    <h3>Danh Sách Sản Phẩm Đã Thêm</h3>
    <table border="1" cellpadding="6" style="border-collapse: collapse;">
        <tr><th>ID</th><th>Mã</th><th>Tên sản phẩm</th><th>Giá bán</th></tr>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= e($p['code']) ?></td>
                <td><?= e($p['name']) ?></td>
                <td><?= number_format($p['price'], 0, ',', '.') ?> đ</td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
