# DEBUG-04: LỖI PHÂN TRANG (OFFSET BUG, THIẾU CLAMP & BIND SAI)
> **Thời gian:** 4 phút | **Nhiệm vụ:** Tìm 4 lỗi tính toán và cơ sở dữ liệu trong phân trang.

---

## 1. ĐOẠN CODE BỊ LỖI (BUGGY CODE)

```php
<?php
$page = $_GET['page'];
$limit = 10;

// Tính offset
$offset = $page * $limit;

// Đếm tổng dòng
$total = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$totalPages = round($total / $limit);

// Truy vấn
$stmt = $pdo->prepare("SELECT * FROM articles LIMIT :limit OFFSET :offset");
$stmt->execute([
    ':limit' => $limit,
    ':offset' => $offset
]);
```

---

## 2. PHÂN TÍCH 4 LỖI CỐT TỬ
1. **Lỗi 1 (Tính sai offset):** `$offset = $page * $limit` sẽ làm mất hoàn toàn dữ liệu trang 1! Trang 1 sẽ lấy từ dòng 10, trong khi các dòng từ 0 đến 9 bị bỏ qua. Công thức đúng phải là: `($page - 1) * $limit`.
2. **Lỗi 2 (Không Clamp giá trị `$page`):** Nếu người dùng nhập `?page=-1` hoặc `?page=abc` hoặc `?page=999999`, hệ thống sẽ tính ra `$offset` âm hoặc truy vấn rỗng. Cần ép kiểu số nguyên và kẹp `$page = max(1, min($page, $totalPages))`.
3. **Lỗi 3 (Dùng hàm `round()` thay vì `ceil()`):** Ví dụ có 11 bài viết, `11 / 10 = 1.1`. Hàm `round(1.1)` làm tròn thành `1` trang $\rightarrow$ Mất bài viết thứ 11! Phải dùng `ceil()`.
4. **Lỗi 4 (Truyền tham số LIMIT/OFFSET dạng mảng trong `execute()`):** Khi `$stmt->execute([...])`, mặc định PDO bind toàn bộ tham số dưới dạng chuỗi (`STRING`). Trong MySQL với chế độ tắt emulate prepare, lệnh `LIMIT '10' OFFSET '0'` sẽ gây ra Fatal Syntax Error! Bắt buộc phải dùng `$stmt->bindValue(':limit', $limit, PDO::PARAM_INT)`.

---

## 3. CODE ĐÃ SỬA CHUẨN (FIXED)

```php
<?php
$limit = 10;

// SỬA: Đếm tổng và dùng ceil()
$total = (int)$pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$totalPages = max(1, (int)ceil($total / $limit));

// SỬA: Validate và Clamp page
$rawPage = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
$page = ($rawPage === false || $rawPage < 1) ? 1 : min($rawPage, $totalPages);

// SỬA: Tính offset đúng chuẩn (page - 1)
$offset = ($page - 1) * $limit;

// SỬA: bindValue với PDO::PARAM_INT
$stmt = $pdo->prepare("SELECT * FROM articles ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll();
```
