# BÀI 10: TÌM KIẾM, SẮP XẾP WHITELIST & PHÂN TRANG CHUẨN MỰC

## A. Mục tiêu
Sau bài này, bạn có thể:
1. Xử lý tìm kiếm đa điều kiện (tên, mã, danh mục) bằng Prepared Statement.
2. Escape an toàn ký tự đại diện `%` và `_` trong mệnh đề `LIKE`.
3. Phòng chống triệt để SQL Injection ở mệnh đề `ORDER BY` bằng cơ chế Whitelist (danh sách trắng).
4. Xây dựng thuật toán phân trang chuẩn toán học: tính `totalPages`, tính `OFFSET`, kẹp giá trị `page` hợp lệ (clamp).
5. Duy trì toàn bộ query parameters trên URL khi chuyển trang bằng `http_build_query()`.

---

## B. Bản chất
- **Tại sao không thể bind param cho `ORDER BY`?** PDO Prepared Statements chỉ tham số hóa được giá trị (Values), không thể tham số hóa tên cột hay từ khóa SQL (`ASC`/`DESC`). Nếu bạn viết `ORDER BY :col`, MySQL sẽ coi đó là chuỗi ký tự `'name'` chứ không phải tên cột, làm câu lệnh vô tác dụng hoặc lỗi. Do đó, bắt buộc phải dùng **Whitelist** với `in_array()`.
- **Tại sao cần escape `%` và `_` trong `LIKE`?** Ký tự `%` đại diện cho chuỗi bất kỳ. Nếu người dùng nhập vào ô tìm kiếm chữ `%`, hệ thống sẽ trả về toàn bộ database thay vì tìm sản phẩm có chứa ký tự `%`.
- **Thuật toán Kẹp (Clamp) `page`:** Người dùng có thể sửa URL thành `?page=-5` hoặc `?page=9999`. Bạn phải luôn ép `$page = max(1, min($page, $totalPages))` để tránh lỗi offset âm hoặc trang trắng.

---

## C. Cú pháp cốt lõi

### 1. Thuật toán Phân trang & Whitelist Hoàn chỉnh
```php
// 1. Nhận và làm sạch tham số
$keyword = trim($_GET['kw'] ?? '');
$catId   = filter_var($_GET['cat_id'] ?? '', FILTER_VALIDATE_INT);
$sort    = $_GET['sort'] ?? 'id';
$dir     = strtoupper($_GET['dir'] ?? 'ASC');
$page    = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT) ?: 1;
$limit   = 10;

// 2. Escape ký tự % và _
$escapedKw = strtr($keyword, ['%' => '\%', '_' => '\_']);

// 3. Xây dựng mệnh đề WHERE động
$where = ["1=1"];
$params = [];

if ($escapedKw !== '') {
    $where[] = "(p.name LIKE :kw OR p.code LIKE :kw)";
    $params['kw'] = "%$escapedKw%";
}
if ($catId !== false && $catId > 0) {
    $where[] = "p.category_id = :cat_id";
    $params['cat_id'] = $catId;
}
$whereSql = implode(' AND ', $where);

// 4. Đếm tổng số dòng
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM products p WHERE $whereSql");
$countStmt->execute($params);
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalRows / $limit));

// Kẹp trang hợp lệ
$page = max(1, min($page, $totalPages));
$offset = ($page - 1) * $limit;

// 5. Whitelist sắp xếp
$allowedCols = ['id' => 'p.id', 'name' => 'p.name', 'price' => 'p.price'];
$sortCol = $allowedCols[$sort] ?? 'p.id';
$sortDir = ($dir === 'DESC') ? 'DESC' : 'ASC';

// 6. Truy vấn lấy dữ liệu trang hiện tại
$dataSql = "SELECT p.*, c.name AS cat_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE $whereSql 
            ORDER BY $sortCol $sortDir 
            LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($dataSql);
foreach ($params as $k => $v) {
    $stmt->bindValue(":$k", $v);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$items = $stmt->fetchAll();
```

### 2. Giữ Query String khi chuyển trang
```php
<?php for ($p = 1; $p <= $totalPages; $p++): ?>
    <?php $url = '?' . http_build_query(array_merge($_GET, ['page' => $p])); ?>
    <a href="<?= htmlspecialchars($url) ?>" class="<?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
<?php endfor; ?>
```

---

## D. Ví dụ tối giản
```php
<?php
// pagination_simple.php
$page = max(1, (int)($_GET['p'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare("SELECT * FROM items LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

---

## E. Luồng tư duy
```text
Đọc tham số từ $_GET (kw, cat, sort, dir, page)
              ↓
Escape ký tự đại diện % và _ của LIKE
              ↓
Xây dựng WHERE động + mảng $params
              ↓
Query 1: SELECT COUNT(*) tính $totalRows → $totalPages = ceil(...)
              ↓
Kẹp $page = max(1, min($page, $totalPages)) → $offset = ($page - 1) * $limit
              ↓
Kiểm tra Whitelist cho $sort và $dir
              ↓
Query 2: SELECT dữ liệu có ORDER BY $col $dir LIMIT :limit OFFSET :offset
              ↓
Render bảng dữ liệu + Vòng lặp phân trang (dùng http_build_query($_GET))
```

---

## F. Những lỗi hay gặp
1. **Nối trực tiếp biến `$_GET['sort']` vào SQL:** Gây ra lỗ hổng SQL Injection nghiêm trọng nhất trên URL.
2. **Không ép kiểu `PDO::PARAM_INT` cho LIMIT và OFFSET:** Khi tắt giả lập prepare, PDO mặc định coi mọi giá trị bind là string `"10"`, làm MySQL văng lỗi cú pháp gần `LIMIT '10' OFFSET '0'`.
3. **Mất từ khóa tìm kiếm khi bấm sang trang 2:** Viết thẻ `<a href="?page=2">` thay vì dùng `http_build_query()`.
4. **Không xử lý trường hợp 0 kết quả:** Khi `$totalRows = 0`, nếu không có `max(1, ...)`, `$totalPages` sẽ bằng `0`, làm vỡ giao diện.

---

## G. Cách nhận dạng câu hỏi
- Đề có câu: *"Tìm kiếm sản phẩm theo tên, cho phép chọn sắp xếp theo giá tăng dần/giảm dần và phân trang mỗi trang 10 sản phẩm"* -> **Áp dụng trọn vẹn bộ khung Search + Sort Whitelist + Pagination 6 bước**.
- Đề có câu: *"Đảm bảo khi chuyển trang phân trang thì điều kiện tìm kiếm và sắp xếp vẫn được giữ nguyên"* -> **Bắt buộc dùng `http_build_query(array_merge($_GET, ['page' => $p]))`**.

---

## H. Mini challenge
> **Đề bài:** Viết trang `staff_list.php`:
> 1. Tìm kiếm nhân viên theo `name` HOẶC `phone`.
> 2. Lọc theo phòng ban (`department_id`).
> 3. Sắp xếp theo: `id`, `salary`, `join_date` (mặc định là `id DESC`). Chiều sắp xếp `ASC` hoặc `DESC`. Bắt buộc kiểm tra whitelist.
> 4. Phân trang: 5 nhân viên / trang. Hiển thị thông tin: "Đang xem trang X trên tổng số Y trang (Tổng Z nhân viên)".  
> *(Lời giải có trong thư mục `solutions/mini_challenge_solution.php`). Tự làm trước khi xem!*
