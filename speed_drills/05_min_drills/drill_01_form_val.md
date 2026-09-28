# SPEED DRILL 5M-01: FORM VALIDATION SỐ DƯƠNG & XSS
> **Thời gian tối đa:** 5 phút | **Mục tiêu:** Viết code xử lý form không do dự, chống XSS, giữ lại giá trị (Sticky).

---

## 1. ĐỀ BÀI
Tạo file `form_process.php`:
- Form POST gồm `product_name` (chuỗi) và `quantity` (số lượng).
- Yêu cầu validation:
  1. `product_name` không được rỗng sau khi `trim()`.
  2. `quantity` bắt buộc là số nguyên dương $\ge 1$.
- Nếu lỗi: Hiển thị lỗi, giữ lại dữ liệu trong ô input.
- Nếu đúng: Hiển thị thông báo: `"Đã tiếp nhận sản phẩm [product_name] với số lượng [quantity]"` (an toàn tuyệt đối với mã XSS).

---

## 2. TEST CASES TỰ KIỂM TRA
- Case 1: Nhập `product_name = ""` $\rightarrow$ Báo lỗi.
- Case 2: Nhập `quantity = 0` hoặc `-5` $\rightarrow$ Báo lỗi.
- Case 3: Nhập `product_name = "<script>alert('xss')</script>"` $\rightarrow$ Hiển thị dạng text, không nảy popup alert.

---

## 3. LỜI GIẢI MẪU (SOLUTION)
```php
<?php
$name = trim($_POST['product_name'] ?? '');
$qty = trim($_POST['quantity'] ?? '');
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($name === '') {
        $errors['name'] = 'Tên sản phẩm không được để trống.';
    }
    if ($qty === '' || !ctype_digit($qty) || (int)$qty < 1) {
        $errors['qty'] = 'Số lượng phải là số nguyên >= 1.';
    }
    if (empty($errors)) {
        $success = "Đã tiếp nhận sản phẩm " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . " với số lượng " . (int)$qty;
        $name = '';
        $qty = '';
    }
}
?>
<!DOCTYPE html>
<html>
<body>
    <?php if ($success): ?><p style="color:green"><?= $success ?></p><?php endif; ?>
    <?php if (!empty($errors)): ?>
        <ul style="color:red"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
    <?php endif; ?>
    <form method="POST">
        Tên: <input type="text" name="product_name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"><br>
        Số lượng: <input type="number" name="quantity" value="<?= htmlspecialchars($qty) ?>"><br>
        <button type="submit">Gửi</button>
    </form>
</body>
</html>
```
