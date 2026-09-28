# ĐÁP ÁN HOÀN CHỈNH: MOCK EXAM 01

### 1. `connect.php`
```php
<?php
$host = '127.0.0.1';
$db   = 'mock01_stationery';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
```

---

### 2. `index.php` (Hiển thị + Tìm kiếm + Nút Xóa)
```php
<?php
require_once 'connect.php';

$kw = trim($_GET['kw'] ?? '');
$params = [];
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        INNER JOIN categories c ON p.category_id = c.id";

if ($kw !== '') {
    $escaped = strtr($kw, ['%' => '\%', '_' => '\_']);
    $sql .= " WHERE p.name LIKE :kw";
    $params['kw'] = "%$escaped%";
}

$sql .= " ORDER BY p.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Quản Lý Sản Phẩm</title></head>
<body>
    <h2>DANH SÁCH SẢN PHẨM VĂN PHÒNG PHẨM</h2>
    <p><a href="create.php">➕ Thêm sản phẩm mới</a></p>

    <?php if (($_GET['msg'] ?? '') === 'added'): ?>
        <p style="color: green;">Thêm sản phẩm mới thành công!</p>
    <?php elseif (($_GET['msg'] ?? '') === 'deleted'): ?>
        <p style="color: blue;">Đã xóa sản phẩm thành công!</p>
    <?php endif; ?>

    <form method="GET" action="">
        Tìm kiếm: <input type="text" name="kw" value="<?= e($kw) ?>" placeholder="Nhập tên sản phẩm...">
        <button type="submit">Tìm</button>
        <?php if ($kw !== ''): ?><a href="index.php">Bỏ lọc</a><?php endif; ?>
    </form>
    <br>

    <table border="1" cellpadding="6" style="border-collapse: collapse; width: 800px;">
        <tr bgcolor="#eee">
            <th>Mã SP</th>
            <th>Tên sản phẩm</th>
            <th>Danh mục</th>
            <th>Giá bán</th>
            <th>Tồn kho</th>
            <th>Hành động</th>
        </tr>
        <?php if (empty($products)): ?>
            <tr><td colspan="6" align="center">Không tìm thấy sản phẩm nào!</td></tr>
        <?php else: ?>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= e($p['code']) ?></td>
                    <td><?= e($p['name']) ?></td>
                    <td><?= e($p['category_name']) ?></td>
                    <td align="right"><?= number_format($p['price'], 0, ',', '.') ?> đ</td>
                    <td align="center"><?= (int)$p['quantity'] ?></td>
                    <td align="center">
                        <a href="delete.php?id=<?= $p['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>
</html>
```

---

### 3. `create.php` (Thêm mới + Bắt lỗi trùng mã + Bẫy số 0)
```php
<?php
require_once 'connect.php';

// Lấy danh mục cho dropdown
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

$errors = [];
$code = '';
$name = '';
$catId = '';
$price = '';
$qty = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code  = trim($_POST['code'] ?? '');
    $name  = trim($_POST['name'] ?? '');
    $catId = trim($_POST['category_id'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $qty   = trim($_POST['quantity'] ?? '');

    if ($code === '') $errors['code'] = 'Mã sản phẩm không được rỗng.';
    if ($name === '') $errors['name'] = 'Tên sản phẩm không được rỗng.';
    if ($catId === '') $errors['category_id'] = 'Vui lòng chọn danh mục.';

    // Validate giá (> 0)
    if ($price === '') {
        $errors['price'] = 'Giá bán không được rỗng.';
    } elseif (!is_numeric($price) || (float)$price <= 0) {
        $errors['price'] = 'Giá bán phải là số thực lớn hơn 0.';
    }

    // Validate số lượng (>= 0, không dùng empty vì số 0 vẫn hợp lệ)
    if ($qty === '') {
        $errors['quantity'] = 'Số lượng không được rỗng.';
    } elseif (!filter_var($qty, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]])) {
        $errors['quantity'] = 'Số lượng phải là số nguyên không âm (>= 0).';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (code, name, category_id, price, quantity) 
                                   VALUES (:c, :n, :cat, :p, :q)");
            $stmt->execute([
                'c'   => $code,
                'n'   => $name,
                'cat' => (int)$catId,
                'p'   => (float)$price,
                'q'   => (int)$qty,
            ]);

            // PRG Pattern
            header('Location: index.php?msg=added');
            exit;
        } catch (PDOException $ex) {
            if ($ex->getCode() == 23000) {
                $errors['general'] = "Mã sản phẩm [{$code}] đã tồn tại trong hệ thống!";
            } else {
                error_log($ex->getMessage());
                $errors['general'] = "Lỗi khi lưu vào CSDL.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Thêm Sản Phẩm Mới</title></head>
<body>
    <h2>THÊM MỚI SẢN PHẨM</h2>
    <?php if (isset($errors['general'])): ?><p style="color:red"><?= e($errors['general']) ?></p><?php endif; ?>

    <form method="POST" action="">
        <div>
            Mã sản phẩm (*):<br>
            <input type="text" name="code" value="<?= e($code) ?>">
            <?php if (isset($errors['code'])): ?><span style="color:red"><?= e($errors['code']) ?></span><?php endif; ?>
        </div><br>

        <div>
            Tên sản phẩm (*):<br>
            <input type="text" name="name" value="<?= e($name) ?>" style="width: 300px;">
            <?php if (isset($errors['name'])): ?><span style="color:red"><?= e($errors['name']) ?></span><?php endif; ?>
        </div><br>

        <div>
            Danh mục (*):<br>
            <select name="category_id">
                <option value="">-- Chọn danh mục --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($catId == $cat['id']) ? 'selected' : '' ?>>
                        <?= e($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['category_id'])): ?><span style="color:red"><?= e($errors['category_id']) ?></span><?php endif; ?>
        </div><br>

        <div>
            Giá bán (VNĐ) (*):<br>
            <input type="number" step="1000" name="price" value="<?= e($price) ?>">
            <?php if (isset($errors['price'])): ?><span style="color:red"><?= e($errors['price']) ?></span><?php endif; ?>
        </div><br>

        <div>
            Số lượng tồn kho (*):<br>
            <input type="number" name="quantity" value="<?= e($qty) ?>">
            <?php if (isset($errors['quantity'])): ?><span style="color:red"><?= e($errors['quantity']) ?></span><?php endif; ?>
        </div><br>

        <button type="submit">Lưu Sản Phẩm</button>
        <a href="index.php">Quay lại</a>
    </form>
</body>
</html>
```

---

### 4. `delete.php` (Xóa an toàn)
```php
<?php
require_once 'connect.php';

$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
if ($id && $id > 0) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

header('Location: index.php?msg=deleted');
exit;
```
