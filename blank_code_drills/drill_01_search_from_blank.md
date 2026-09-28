# BLANK-01: TẠO FILE TÌM KIẾM PDO TỪ FILE TRẮNG
> **Thời gian:** 7 phút | **Yêu cầu:** Bắt đầu từ file hoàn toàn trống, không xem tài liệu!

---

## 1. YÊU CẦU BÀI TẬP
Tạo file `search.php` từ file trống:
1. Kết nối PDO tới database `exam_db` (`host=localhost`, user `root`, pass `''`) có cấu hình ngoại lệ và UTF-8.
2. Tiếp nhận tham số tìm kiếm từ URL qua phương thức GET: `keyword`.
3. Nếu người dùng có nhập từ khóa (không rỗng sau khi trim):
   - Chuẩn bị câu lệnh SQL: `SELECT id, name, price FROM products WHERE name LIKE :kw ORDER BY id DESC`.
   - Thực thi với tham số `'%$keyword%'`.
   - Lấy toàn bộ kết quả.
4. Hiển thị giao diện gồm:
   - Form GET với ô input chứa lại từ khóa người dùng đã nhập.
   - Bảng danh sách kết quả (hoặc thông báo "Không tìm thấy" nếu không có dòng nào).
   - Chống tuyệt đối lỗ hổng XSS tại ô input và danh sách kết quả.

---

## 2. LỜI GIẢI MẪU ĐỐI CHIẾU
```php
<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=exam_db;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Lỗi kết nối cơ sở dữ liệu");
}

$keyword = trim($_GET['keyword'] ?? '');
$products = [];
$searched = false;

if ($keyword !== '') {
    $searched = true;
    $stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE name LIKE :kw ORDER BY id DESC");
    $stmt->execute([':kw' => '%' . $keyword . '%']);
    $products = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Tìm Kiếm Sản Phẩm</title></head>
<body>
    <h2>Tìm Kiếm Sản Phẩm</h2>
    <form method="GET">
        <input type="text" name="keyword" value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>" placeholder="Nhập tên sản phẩm...">
        <button type="submit">Tìm</button>
    </form>

    <?php if ($searched): ?>
        <h3>Kết quả tìm kiếm cho: "<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>"</h3>
        <?php if (empty($products)): ?>
            <p>Không tìm thấy sản phẩm nào.</p>
        <?php else: ?>
            <table border="1" cellpadding="6" cellspacing="0">
                <tr><th>ID</th><th>Tên sản phẩm</th><th>Giá</th></tr>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><?= (int)$p['id'] ?></td>
                        <td><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= number_format((float)$p['price'], 0, ',', '.') ?> đ</td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
```
