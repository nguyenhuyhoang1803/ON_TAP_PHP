# SPEED DRILL 7M-04: THUẬT TOÁN TÍNH PHÂN TRANG CHUẨN
> **Thời gian tối đa:** 7 phút | **Mục tiêu:** Tính toán đầy đủ biến phân trang ($totalPages, $page, $offset) và render link điều hướng.

---

## 1. ĐỀ BÀI
Tạo file `pagination_calc.php`:
- Giả định: Có tổng số bản ghi `$totalRecords = 47`, số bản ghi trên mỗi trang `$limit = 10`.
- Nhận tham số `page` từ `$_GET['page']`.
- Viết thuật toán:
  1. Tính `$totalPages = ceil($totalRecords / $limit)`.
  2. Clamp giá trị `$page`: Bắt buộc nằm trong đoạn `[1, $totalPages]`. Nếu người dùng truyền `page = -3` hoặc `page = 100`, giá trị phải tự động điều chỉnh về 1 hoặc `$totalPages`.
  3. Tính `$offset = ($page - 1) * $limit`.
- Render danh sách nút phân trang HTML: Nút "Trước", các trang `1 2 3 ...`, và nút "Sau".

---

## 2. LỜI GIẢI MẪU (SOLUTION)
```php
<?php
$totalRecords = 47;
$limit = 10;

$totalPages = max(1, (int)ceil($totalRecords / $limit));

$rawPage = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
$page = ($rawPage === false || $rawPage < 1) ? 1 : min($rawPage, $totalPages);

$offset = ($page - 1) * $limit;
?>
<!DOCTYPE html>
<html>
<body>
    <h3>Phân Trang (Trang <?= $page ?> / <?= $totalPages ?>)</h3>
    <p>Hiển thị bản ghi từ <?= $offset + 1 ?> đến <?= min($offset + $limit, $totalRecords) ?> trên tổng số <?= $totalRecords ?></p>

    <div>
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
