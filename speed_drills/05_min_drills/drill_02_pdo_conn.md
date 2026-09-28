# SPEED DRILL 5M-02: KHỞI TẠO KẾT NỐI PDO CHUẨN
> **Thời gian tối đa:** 5 phút | **Mục tiêu:** Gõ thuộc lòng cấu hình PDO an toàn trong 2 phút.

---

## 1. ĐỀ BÀI
Tạo file `db_connect.php`:
- Tạo kết nối PDO tới MySQL: `localhost`, database `store_db`, user `root`, pass `''`.
- Thiết lập bắt buộc:
  1. `charset=utf8mb4` ngay trong DSN.
  2. Bật chế độ ngoại lệ `ERRMODE_EXCEPTION`.
  3. Chế độ fetch mặc định `FETCH_ASSOC`.
  4. Tắt chế độ giả lập prepare `ATTR_EMULATE_PREPARES => false`.
- Bọc toàn bộ trong khối `try...catch (PDOException $e)` và xuất thông báo thân thiện (không để lộ mật khẩu hay trace hệ thống).

---

## 2. LỜI GIẢI MẪU (SOLUTION)
```php
<?php
try {
    $dsn = 'mysql:host=localhost;dbname=store_db;charset=utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, 'root', '', $options);
} catch (PDOException $e) {
    // Không bao giờ echo $e->getMessage() trên môi trường thật vì có thể lộ pass/host
    die('Lỗi kết nối cơ sở dữ liệu! Vui lòng thử lại sau.');
}
```
