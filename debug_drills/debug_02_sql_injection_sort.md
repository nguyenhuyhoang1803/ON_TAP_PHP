# DEBUG-02: SQL INJECTION, SORT INJECTION & WILDCARD LIKE
> **Thời gian:** 4 phút | **Nhiệm vụ:** Tìm 4 lỗi bảo mật và truy vấn trong đoạn code sau.

---

## 1. ĐOẠN CODE BỊ LỖI (BUGGY CODE)

```php
<?php
$kw = $_GET['kw'];
$sort = $_GET['sort'];

// Kết nối PDO
$pdo = new PDO('mysql:host=localhost;dbname=shop', 'root', '');

// Tìm kiếm và sắp xếp
$sql = "SELECT * FROM products WHERE name LIKE '%$kw%' ORDER BY $sort DESC";
$stmt = $pdo->query($sql);
$products = $stmt->fetchAll();
```

---

## 2. PHÂN TÍCH 4 LỖI TRÍ MẠNG
1. **Lỗi 1 (SQL Injection trực tiếp):** Nối biến `$kw` thẳng vào chuỗi câu lệnh SQL trong `LIKE '%$kw%'`. Kẻ tấn công có thể chèn `' OR 1=1 -- ` để khai thác DB.
2. **Lỗi 2 (Sort Injection nguy hiểm):** Biến `$sort` được nối trực tiếp vào mệnh đề `ORDER BY $sort`. Vì PDO không thể bind tham số cho tên cột trong `ORDER BY`, hacker có thể chèn các câu lệnh SQL độc hại (ví dụ: `(SELECT CASE WHEN (1=1) THEN SLEEP(5) ELSE 0 END)`). Bắt buộc phải dùng Whitelist!
3. **Lỗi 3 (Ký tự Wildcard SQL không được xử lý):** Nếu người dùng tìm dấu `%` hoặc `_`, MySQL sẽ hiểu là ký tự đại diện khớp với toàn bộ dữ liệu thay vì tìm chính xác ký tự đó.
4. **Lỗi 4 (Notice Undefined index):** Truy cập trực tiếp `$_GET['kw']` và `$_GET['sort']` mà không kiểm tra tồn tại bằng `?? ''`.

---

## 3. CODE ĐÃ SỬA CHUẨN (FIXED)

```php
<?php
$pdo = new PDO('mysql:host=localhost;dbname=shop;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$kw = trim($_GET['kw'] ?? '');

// SỬA: Whitelist cột sort
$allowedSort = ['id' => 'id', 'price' => 'price', 'name' => 'name'];
$sortInput = $_GET['sort'] ?? 'id';
$sortColumn = $allowedSort[$sortInput] ?? 'id';

$where = "WHERE 1=1";
$params = [];

if ($kw !== '') {
    $where .= " AND name LIKE :kw ESCAPE '\\\\'";
    // SỬA: Escape ký tự đặc biệt % và _
    $escaped = addcslashes($kw, '%_');
    $params[':kw'] = '%' . $escaped . '%';
}

$sql = "SELECT id, name, price FROM products $where ORDER BY $sortColumn DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
```
