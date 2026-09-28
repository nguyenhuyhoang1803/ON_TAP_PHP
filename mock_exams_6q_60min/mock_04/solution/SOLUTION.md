# ĐÁP ÁN & LỜI GIẢI MẪU - MOCK 04 (EDGE CASES & SECURITY)

---

### Câu 1: `c1_course_enroll.php`
```php
<?php
$name = trim($_POST['student_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$originalPrice = 1200000; // Giá gốc 1.200.000 đ
$code = strtoupper(trim($_POST['discount_code'] ?? ''));

$errors = [];
$invoice = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($name === '') {
        $errors['name'] = 'Vui lòng nhập họ và tên.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Địa chỉ email không đúng định dạng.';
    }

    if (empty($errors)) {
        $discountAmount = 0;
        if ($code === 'PHPPRO') {
            $discountAmount = $originalPrice * 0.2; // 20%
        } elseif ($code === 'FREESHIP') {
            $discountAmount = 50000;
        } elseif ($code === 'FULLFREE') {
            $discountAmount = $originalPrice; // 100%
        } elseif ($code !== '') {
            $errors['code'] = 'Mã giảm giá không tồn tại hoặc đã hết hạn.';
        }

        if (empty($errors)) {
            // Chống tràn số âm
            $finalPrice = max(0, $originalPrice - $discountAmount);
            $invoice = [
                'name' => $name,
                'email' => $email,
                'original' => $originalPrice,
                'discount' => $discountAmount,
                'final' => $finalPrice
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Đăng Ký Khóa Học</title></head>
<body>
    <h2>Đăng Ký Khóa Học Trực Tuyến</h2>
    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST">
        Họ tên: <input type="text" name="student_name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"><br><br>
        Email: <input type="text" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"><br><br>
        Giá gốc: <strong><?= number_format($originalPrice, 0, ',', '.') ?> đ</strong><br><br>
        Mã giảm giá: <input type="text" name="discount_code" value="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>" placeholder="Nhập PHPPRO hoặc FULLFREE"><br><br>
        <button type="submit">Xác Nhận Đăng Ký</button>
    </form>

    <?php if ($invoice): ?>
        <h3>Hóa Đơn Khóa Học</h3>
        <p>Học viên: <strong><?= htmlspecialchars($invoice['name'], ENT_QUOTES, 'UTF-8') ?></strong></p>
        <p>Email: <?= htmlspecialchars($invoice['email'], ENT_QUOTES, 'UTF-8') ?></p>
        <p>Giá gốc: <?= number_format($invoice['original'], 0, ',', '.') ?> đ</p>
        <p>Giảm giá: -<?= number_format($invoice['discount'], 0, ',', '.') ?> đ</p>
        <h4>Thực trả: <?= number_format($invoice['final'], 0, ',', '.') ?> đ</h4>
    <?php endif; ?>
</body>
</html>
```

---

### Câu 2: `c2_upload_avatar.php`
```php
<?php
$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $f = $_FILES['avatar'];

    if ($f['error'] !== UPLOAD_ERR_OK) {
        $err = 'Lỗi upload: ' . $f['error'];
    } elseif ($f['size'] < 10240) { // < 10KB
        $err = 'File quá nhỏ hoặc rỗng (Tối thiểu 10KB).';
    } elseif ($f['size'] > 2 * 1024 * 1024) { // > 2MB
        $err = 'File vượt quá kích thước cho phép 2MB.';
    } else {
        // Kiểm tra phần mở rộng
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];

        // Kiểm tra MIME thực tế
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $f['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($ext, $allowedExts, true) || !in_array($mime, $allowedMimes, true)) {
            $err = 'File không phải là định dạng hình ảnh hợp lệ!';
        } else {
            $targetDir = __DIR__ . '/avatars/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

            // Sinh tên hoàn toàn ngẫu nhiên bằng sha256
            $safeName = hash('sha256', uniqid((string)mt_rand(), true)) . '.' . $ext;
            $dest = $targetDir . $safeName;

            if (move_uploaded_file($f['tmp_name'], $dest)) {
                $msg = 'Tải lên avatar thành công: ' . $safeName;
            } else {
                $err = 'Không thể lưu file vào máy chủ.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Upload Avatar Giảng Viên</title></head>
<body>
    <h2>Upload Avatar Giảng Viên (An Toàn Cao)</h2>
    <?php if ($msg): ?><p style="color: green;"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
    <?php if ($err): ?><p style="color: red;"><?= htmlspecialchars($err) ?></p><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="avatar" required><br><br>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
```

---

### Câu 3: `c3_search_courses.php`
```php
<?php
$pdo = new PDO('mysql:host=localhost;dbname=elearning_db;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$keyword = trim($_GET['keyword'] ?? '');
$courses = [];
$searched = false;

if ($keyword !== '') {
    $searched = true;
    // Escape wildcard % và _ bằng addcslashes
    $escaped = addcslashes($keyword, '%_');
    $kwParam = '%' . $escaped . '%';

    $sql = "SELECT id, course_code, course_name, price 
            FROM courses 
            WHERE course_code LIKE :kw ESCAPE '\\\\' OR course_name LIKE :kw ESCAPE '\\\\' 
            ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':kw' => $kwParam]);
    $courses = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Tìm Kiếm Khóa Học</title></head>
<body>
    <h2>Tìm Kiếm Khóa Học (Kháng Wildcard Injection)</h2>
    <form method="GET">
        <input type="text" name="keyword" value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>" placeholder="Ví dụ: 100% PHP, _Special...">
        <button type="submit">Tìm Kiếm</button>
    </form>

    <?php if ($searched): ?>
        <h3>Kết quả cho từ khóa: "<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>"</h3>
        <?php if (empty($courses)): ?>
            <p>Không tìm thấy khóa học phù hợp với từ khóa trên.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($courses as $c): ?>
                    <li>[<?= htmlspecialchars($c['course_code']) ?>] <?= htmlspecialchars($c['course_name']) ?> - <?= number_format($c['price'], 0, ',', '.') ?> đ</li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
```

---

### Câu 4: `c4_courses_paging.php`
```php
<?php
$pdo = new PDO('mysql:host=localhost;dbname=elearning_db;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
]);

// 1. Whitelist Sort & Direction
$sortWhitelist = [
    'id' => 'id',
    'price' => 'price',
    'created_at' => 'created_at'
];
$sortInput = $_GET['sort'] ?? 'id';
$sortColumn = $sortWhitelist[$sortInput] ?? 'id';

$dirInput = strtoupper($_GET['dir'] ?? 'DESC');
$dir = ($dirInput === 'ASC') ? 'ASC' : 'DESC';

// 2. Tính toán phân trang và kẹp dải (Clamp)
$limit = 5;
$countStmt = $pdo->query("SELECT COUNT(*) FROM courses");
$totalRecords = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalRecords / $limit));

$pageRaw = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
$page = ($pageRaw === false || $pageRaw < 1) ? 1 : min($pageRaw, $totalPages);

$offset = ($page - 1) * $limit;

// 3. Thực thi truy vấn an toàn
$sql = "SELECT id, course_code, course_name, price, created_at 
        FROM courses 
        ORDER BY $sortColumn $dir 
        LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Phân Trang An Toàn</title></head>
<body>
    <h2>Danh Sách Khóa Học (Trang <?= $page ?> / <?= $totalPages ?>)</h2>
    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th><a href="?page=<?= $page ?>&sort=id&dir=<?= $dir === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
                <th>Mã</th>
                <th>Tên Khóa Học</th>
                <th><a href="?page=<?= $page ?>&sort=price&dir=<?= $dir === 'ASC' ? 'DESC' : 'ASC' ?>">Giá</a></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= $item['id'] ?></td>
                    <td><?= htmlspecialchars($item['course_code']) ?></td>
                    <td><?= htmlspecialchars($item['course_name']) ?></td>
                    <td><?= number_format($item['price'], 0, ',', '.') ?> đ</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
```

---

### Câu 5: `c5_oop_course_review.php`
```php
<?php
class Review {
    private float $rating;
    private string $comment;

    public function __construct(float $rating, string $comment) {
        if ($rating < 1.0 || $rating > 5.0) {
            throw new InvalidArgumentException("Điểm đánh giá phải nằm trong khoảng từ 1.0 đến 5.0!");
        }
        $this->rating = $rating;
        $this->comment = $comment;
    }

    public function getRating(): float {
        return $this->rating;
    }
}

class Course {
    private string $name;
    private array $reviews = [];

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function addReview(Review $review): void {
        $this->reviews[] = $review;
    }

    public function getAverageRating(): float {
        // Bẫy chia cho 0: Kiểm tra mảng rỗng
        if (empty($this->reviews)) {
            return 0.0;
        }

        $sum = 0.0;
        foreach ($this->reviews as $r) {
            $sum += $r->getRating();
        }
        return round($sum / count($this->reviews), 1);
    }

    public function getRatingSummary(): string {
        $count = count($this->reviews);
        $avg = $this->getAverageRating();
        return sprintf("Khóa học: %s | Điểm TB: %.1f (tổng %d lượt đánh giá)", $this->name, $avg, $count);
    }
}

// Chạy thử kiểm tra edge case
$courseEmpty = new Course("DevOps từ con số 0");
echo $courseEmpty->getRatingSummary() . "<br>"; // Trả về 0.0 không lỗi

$courseActive = new Course("PHP Chuyên Nghiệp");
$courseActive->addReview(new Review(5.0, "Tuyệt vời"));
$courseActive->addReview(new Review(4.5, "Rất hay"));
$courseActive->addReview(new Review(4.0, "Khá ổn"));
echo $courseActive->getRatingSummary();
```

---

### Câu 6: `c6_advanced_reporting.sql`
```sql
-- 1. Thống kê doanh thu và hoàn tiền theo giảng viên
SELECT 
    i.id, 
    i.instructor_name,
    COUNT(CASE WHEN e.status = 'active' THEN e.id END) AS active_students,
    COALESCE(SUM(CASE WHEN e.status = 'active' THEN e.course_price ELSE 0 END), 0) AS total_revenue,
    COALESCE(SUM(CASE WHEN e.status = 'refunded' THEN e.course_price ELSE 0 END), 0) AS total_refunded
FROM instructors i
LEFT JOIN enrollments e ON i.id = e.instructor_id
GROUP BY i.id, i.instructor_name
ORDER BY total_revenue DESC;

-- 2. Giảng viên có doanh thu thuần (active - refunded) cao nhất (xử lý đồng hạng)
SELECT 
    i.id, 
    i.instructor_name,
    (
        COALESCE(SUM(CASE WHEN e.status = 'active' THEN e.course_price ELSE 0 END), 0) -
        COALESCE(SUM(CASE WHEN e.status = 'refunded' THEN e.course_price ELSE 0 END), 0)
    ) AS net_revenue
FROM instructors i
JOIN enrollments e ON i.id = e.instructor_id
GROUP BY i.id, i.instructor_name
HAVING COUNT(CASE WHEN e.status = 'active' THEN e.id END) >= 1
   AND net_revenue = (
        SELECT MAX(sub.net_val)
        FROM (
            SELECT 
                (
                    COALESCE(SUM(CASE WHEN e2.status = 'active' THEN e2.course_price ELSE 0 END), 0) -
                    COALESCE(SUM(CASE WHEN e2.status = 'refunded' THEN e2.course_price ELSE 0 END), 0)
                ) AS net_val
            FROM instructors i2
            JOIN enrollments e2 ON i2.id = e2.instructor_id
            GROUP BY i2.id
            HAVING COUNT(CASE WHEN e2.status = 'active' THEN e2.id END) >= 1
        ) sub
   );
```
