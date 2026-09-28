# LỜI GIẢI THỰC HÀNH ĐỊNH VỊ NĂNG LỰC (CODING DIAGNOSTIC SOLUTION)

---

## Lời Giải Task A: Form POST + Validation + htmlspecialchars
```php
<?php
function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

$errors   = [];
$fullname = '';
$email    = '';
$qty      = '';
$success  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $qty      = trim($_POST['quantity'] ?? '');

    // 1. Validate fullname
    if ($fullname === '') {
        $errors['fullname'] = 'Họ và tên không được để trống.';
    } elseif (mb_strlen($fullname) < 3) {
        $errors['fullname'] = 'Họ và tên phải có tối thiểu 3 ký tự.';
    }

    // 2. Validate email
    if ($email === '') {
        $errors['email'] = 'Email không được để trống.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email không đúng định dạng.';
    }

    // 3. Validate quantity (Bẫy số 0: === '' thay vì empty)
    if ($qty === '') {
        $errors['quantity'] = 'Số lượng không được để trống.';
    } elseif (!filter_var($qty, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]])) {
        $errors['quantity'] = 'Số lượng phải là số nguyên không âm (>= 0).';
    }

    if (empty($errors)) {
        $success = "Đăng ký thành công cho: " . e($fullname) . " - " . e($email) . " - Số lượng: " . (int)$qty;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Task A</title></head>
<body>
    <?php if ($success): ?><p style="color: green; font-weight: bold;"><?= $success ?></p><?php endif; ?>

    <form method="POST" action="">
        <div>
            Họ tên: <input type="text" name="fullname" value="<?= e($fullname) ?>">
            <?php if (isset($errors['fullname'])): ?><span style="color:red"><?= e($errors['fullname']) ?></span><?php endif; ?>
        </div><br>
        <div>
            Email: <input type="text" name="email" value="<?= e($email) ?>">
            <?php if (isset($errors['email'])): ?><span style="color:red"><?= e($errors['email']) ?></span><?php endif; ?>
        </div><br>
        <div>
            Số lượng: <input type="number" name="quantity" value="<?= e($qty) ?>">
            <?php if (isset($errors['quantity'])): ?><span style="color:red"><?= e($errors['quantity']) ?></span><?php endif; ?>
        </div><br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
```

---

## Lời Giải Task B: Session Login Guard & Flash Message
```php
<?php
// admin.php
session_start();

// 1. Auth Guard bắt buộc
if (empty($_SESSION['logged_user'])) {
    header('Location: login.php?error=unauthorized');
    exit; // LỆNH SỐNG CÒN BẮT BUỘC
}

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

// 2. Lấy flash message và xóa ngay để tránh lặp khi F5
$flash = $_SESSION['flash_msg'] ?? null;
unset($_SESSION['flash_msg']);
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Admin Dashboard</title></head>
<body>
    <?php if ($flash): ?>
        <div style="background: #e6ffed; border: 1px solid #34d058; padding: 10px; margin-bottom: 10px;">
            <?= e($flash) ?>
        </div>
    <?php endif; ?>

    <h2>Chào mừng, <?= e($_SESSION['logged_user']) ?>! | <a href="logout.php">Đăng xuất</a></h2>
</body>
</html>
```

---

## Lời Giải Task C: PDO Prepared Statement Search
```php
<?php
// Giả định đã có biến $pdo là kết nối PDO

$keyword = trim($_GET['kw'] ?? '');

// Escape ký tự % và _ để không bị hiểu nhầm là ký tự đại diện trong LIKE
$escapedKw = strtr($keyword, [
    '%' => '\%',
    '_' => '\_'
]);

$sql = "SELECT * FROM products WHERE (name LIKE :kw OR code LIKE :kw) ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'kw' => "%$escapedKw%"
]);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

---

## Lời Giải Task D: SQL LEFT JOIN + GROUP BY + NULL Handling
```sql
SELECT 
    c.id AS category_id,
    c.name AS category_name,
    COUNT(p.id) AS total_products
FROM categories c
LEFT JOIN products p ON c.id = p.category_id
GROUP BY c.id, c.name
HAVING COUNT(p.id) >= 2 OR COUNT(p.id) = 0
ORDER BY total_products DESC;
```
> **Điểm cần nhớ:** Dùng `COUNT(p.id)` thay vì `COUNT(*)` vì với danh mục chưa có sản phẩm, các cột của `p` nhận giá trị `NULL`. `COUNT(p.id)` bỏ qua `NULL` nên trả về đúng `0`.
