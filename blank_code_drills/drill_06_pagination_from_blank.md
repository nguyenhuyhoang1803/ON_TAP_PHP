# BLANK-06: THUẬT TOÁN VÀ GIAO DIỆN PHÂN TRANG TỪ FILE TRẮNG
> **Thời gian:** 9 phút | **Yêu cầu:** Tự viết trọn vẹn kết nối PDO, đếm tổng, tính trang, kẹp offset và in HTML thanh phân trang.

---

## 1. YÊU CẦU BÀI TẬP
Tạo file `list_paged.php` từ file trống:
1. Giả định kết nối PDO tới bảng `items(id, title, price)`.
2. Đặt `$limit = 5`.
3. Nhận `$page` từ `$_GET['page']`.
4. Đếm tổng số dòng qua `SELECT COUNT(*) FROM items`.
5. Tính `$totalPages = max(1, (int)ceil($total / $limit))`.
6. Kẹp `$page` hợp lệ: `max(1, min($page, $totalPages))`.
7. Tính `$offset = ($page - 1) * $limit`.
8. Chuẩn bị câu lệnh lấy dữ liệu: `SELECT * FROM items ORDER BY id DESC LIMIT :limit OFFSET :offset`.
   - Bắt buộc bind kiểu `PDO::PARAM_INT`.
9. Hiển thị bảng dữ liệu và thanh điều hướng: Nút "Trước", các số trang `1, 2, 3...`, và nút "Sau".

---

## 2. LỜI GIẢI MẪU ĐỐI CHIẾU
```php
<?php
$pdo = new PDO('mysql:host=localhost;dbname=test_db;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
]);

$limit = 5;

// Đếm tổng
$totalRecords = (int)$pdo->query("SELECT COUNT(*) FROM items")->fetchColumn();
$totalPages = max(1, (int)ceil($totalRecords / $limit));

$rawPage = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
$page = ($rawPage === false || $rawPage < 1) ? 1 : min($rawPage, $totalPages);

$offset = ($page - 1) * $limit;

// Lấy dữ liệu
$stmt = $pdo->prepare("SELECT id, title, price FROM items ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Phân Trang Chuẩn</title></head>
<body>
    <h2>Danh Sách Sản Phẩm (Trang <?= $page ?> / <?= $totalPages ?>)</h2>
    <table border="1" cellpadding="6">
        <tr><th>ID</th><th>Tiêu đề</th><th>Giá</th></tr>
        <?php foreach ($items as $it): ?>
            <tr>
                <td><?= (int)$it['id'] ?></td>
                <td><?= htmlspecialchars($it['title']) ?></td>
                <td><?= number_format($it['price'], 0, ',', '.') ?> đ</td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div style="margin-top: 15px;">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>">« Trước</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" style="<?= $i === $page ? 'font-weight:bold; color:red;' : '' ?>">
                [<?= $i ?>]
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>">Sau »</a>
        <?php endif; ?>
    </div>
</body>
</html>
```
