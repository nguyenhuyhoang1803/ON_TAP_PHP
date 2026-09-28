# ĐÁP ÁN & LỜI GIẢI MẪU - MOCK 01

---

### Câu 1: `c1_add_book.php`
```php
<?php
$errors = [];
$successMsg = '';
$title = trim($_POST['title'] ?? '');
$price = trim($_POST['price'] ?? '');
$categoryId = trim($_POST['category_id'] ?? '');

$allowedCategories = ['1', '2', '3'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($title === '') {
        $errors['title'] = 'Vui lòng nhập tên sách.';
    }

    if ($price === '' || !is_numeric($price) || (float)$price < 0) {
        $errors['price'] = 'Giá sách phải là một số không âm (>= 0).';
    }

    if (!in_array($categoryId, $allowedCategories, true)) {
        $errors['category_id'] = 'Danh mục sách đã chọn không hợp lệ.';
    }

    if (empty($errors)) {
        $formattedPrice = number_format((float)$price, 0, ',', '.') . ' đ';
        $successMsg = "Thêm thành công sách " . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . " - Giá: " . $formattedPrice;
        // Reset form sau khi thành công
        $title = '';
        $price = '';
        $categoryId = '';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Thêm Sách Mới</title></head>
<body>
    <h2>Thêm Sách Mới</h2>
    <?php if ($successMsg): ?>
        <p style="color: green;"><?= $successMsg ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div>
            <label>Tên sách:</label><br>
            <input type="text" name="title" value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
            <?php if (isset($errors['title'])): ?>
                <span style="color: red;"><?= $errors['title'] ?></span>
            <?php endif; ?>
        </div>
        <div>
            <label>Giá sách (VNĐ):</label><br>
            <input type="text" name="price" value="<?= htmlspecialchars($price, ENT_QUOTES, 'UTF-8') ?>">
            <?php if (isset($errors['price'])): ?>
                <span style="color: red;"><?= $errors['price'] ?></span>
            <?php endif; ?>
        </div>
        <div>
            <label>Danh mục:</label><br>
            <select name="category_id">
                <option value="">-- Chọn danh mục --</option>
                <option value="1" <?= $categoryId === '1' ? 'selected' : '' ?>>Công nghệ</option>
                <option value="2" <?= $categoryId === '2' ? 'selected' : '' ?>>Kinh tế</option>
                <option value="3" <?= $categoryId === '3' ? 'selected' : '' ?>>Văn học</option>
            </select>
            <?php if (isset($errors['category_id'])): ?>
                <span style="color: red;"><?= $errors['category_id'] ?></span>
            <?php endif; ?>
        </div>
        <br>
        <button type="submit">Lưu Sách</button>
    </form>
</body>
</html>
```

---

### Câu 2: `c2_login.php` & `c2_dashboard.php`

**File `c2_login.php`:**
```php
<?php
session_start();

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['librarian']);
    session_destroy();
    header('Location: c2_login.php');
    exit;
}

if (isset($_SESSION['librarian'])) {
    header('Location: c2_dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === 'admin' && $password === 'lib123') {
        $_SESSION['librarian'] = [
            'user' => $username,
            'login_at' => time()
        ];
        header('Location: c2_dashboard.php');
        exit;
    } else {
        $error = 'Sai tên đăng nhập hoặc mật khẩu!';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Đăng Nhập Thủ Thư</title></head>
<body>
    <h2>Đăng Nhập Thủ Thư</h2>
    <?php if ($error): ?><p style="color: red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit">Đăng nhập</button>
    </form>
</body>
</html>
```

**File `c2_dashboard.php`:**
```php
<?php
session_start();

if (!isset($_SESSION['librarian'])) {
    header('Location: c2_login.php');
    exit;
}

$librarian = $_SESSION['librarian'];
$loginTime = date('H:i:s d/m/Y', $librarian['login_at']);
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Bảng Điều Khiển Thủ Thư</title></head>
<body>
    <h2>Xin chào <?= htmlspecialchars($librarian['user']) ?>!</h2>
    <p>Đăng nhập lúc: <strong><?= $loginTime ?></strong></p>
    <a href="c2_login.php?action=logout">Đăng xuất</a>
</body>
</html>
```

---

### Câu 3: `c3_upload_cover.php`
```php
<?php
$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cover_image'])) {
    $file = $_FILES['cover_image'];
    $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
    $maxSize = (int)(1.5 * 1024 * 1024); // 1.5 MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $err = 'Lỗi khi upload file (Code: ' . $file['error'] . ')';
    } else {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts, true)) {
            $err = 'Định dạng file không được phép. Chỉ nhận JPG, PNG, WEBP.';
        } elseif ($file['size'] > $maxSize) {
            $err = 'Dung lượng file vượt quá giới hạn 1.5MB.';
        } else {
            $uploadDir = __DIR__ . '/covers/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $newFileName = uniqid('cover_', true) . '.' . $ext;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $logLine = sprintf("[%s] - File: %s - Size: %d bytes\n", date('Y-m-d H:i:s'), $newFileName, $file['size']);
                file_put_contents(__DIR__ . '/upload_history.log', $logLine, FILE_APPEND | LOCK_EX);
                $msg = 'Upload thành công: ' . htmlspecialchars($newFileName);
            } else {
                $err = 'Không thể lưu file vào máy chủ.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Upload Bìa Sách</title></head>
<body>
    <h2>Upload Bìa Sách</h2>
    <?php if ($msg): ?><p style="color: green;"><?= $msg ?></p><?php endif; ?>
    <?php if ($err): ?><p style="color: red;"><?= $err ?></p><?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="file" name="cover_image" required><br><br>
        <button type="submit">Tải lên</button>
    </form>
</body>
</html>
```

---

### Câu 4: `c4_oop_publication.php`
```php
<?php
abstract class Publication {
    protected string $title;
    protected float $basePrice;

    public function __construct(string $title, float $basePrice) {
        $this->title = $title;
        $this->basePrice = $basePrice;
    }

    abstract public function calculateRentalFee(): float;

    public function getTitle(): string {
        return $this->title;
    }
}

class PhysicalBook extends Publication {
    private float $shippingFee;

    public function __construct(string $title, float $basePrice, float $shippingFee) {
        parent::__construct($title, $basePrice);
        $this->shippingFee = $shippingFee;
    }

    public function calculateRentalFee(): float {
        return ($this->basePrice * 0.1) + $this->shippingFee;
    }
}

class EBook extends Publication {
    public function calculateRentalFee(): float {
        return $this->basePrice * 0.05;
    }
}

// Khởi tạo danh sách
$publications = [
    new PhysicalBook("Lập Trình Web PHP 8", 120000, 15000), // 12000 + 15000 = 27000
    new EBook("Khoa Học Dữ Liệu Cơ Bản", 200000),           // 200000 * 0.05 = 10000
    new PhysicalBook("Cấu Trúc Dữ Liệu", 250000, 20000)     // 25000 + 20000 = 45000
];

// Sắp xếp giảm dần theo phí thuê (b <=> a)
usort($publications, fn($a, $b) => $b->calculateRentalFee() <=> $a->calculateRentalFee());

// Xuất kết quả
echo "<h3>DANH SÁCH SÁCH THEO PHÍ THUÊ GIẢM DẦN:</h3><ul>";
foreach ($publications as $pub) {
    echo "<li><strong>" . htmlspecialchars($pub->getTitle()) . "</strong> - Phí thuê: " 
         . number_format($pub->calculateRentalFee(), 0, ',', '.') . " đ</li>";
}
echo "</ul>";
```

---

### Câu 5: `c5_search_api.php` & `c5_search_view.html`

**File `c5_search_api.php`:**
```php
<?php
header('Content-Type: application/json; charset=utf-8');

$books = [
    ['id' => 1, 'title' => 'Lập trình PHP chuyên sâu', 'author' => 'Nguyễn Văn A', 'price' => 150000],
    ['id' => 2, 'title' => 'Học MySQL trong 21 ngày', 'author' => 'Trần Thị B', 'price' => 120000],
    ['id' => 3, 'title' => 'JavaScript Hiện Đại ES6+', 'author' => 'Lê Văn C', 'price' => 180000],
    ['id' => 4, 'title' => 'Clean Code Nghệ Thuật Viết Code', 'author' => 'Robert Martin', 'price' => 220000],
    ['id' => 5, 'title' => 'Kiến Trúc Microservices', 'author' => 'Hoàng Minh D', 'price' => 250000],
];

$keyword = trim($_GET['keyword'] ?? '');

if ($keyword === '') {
    echo json_encode($books, JSON_UNESCAPED_UNICODE);
    exit;
}

$results = array_filter($books, function($b) use ($keyword) {
    return mb_stripos($b['title'], $keyword) !== false;
});

echo json_encode(array_values($results), JSON_UNESCAPED_UNICODE);
exit;
```

**File `c5_search_view.html`:**
```html
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tìm Kiếm Sách Realtime</title>
</head>
<body>
    <h2>Tìm Kiếm Sách Trực Tuyến</h2>
    <input type="text" id="search-box" placeholder="Nhập tên sách cần tìm..." style="width: 300px; padding: 6px;">
    <ul id="results-list" style="margin-top: 15px;"></ul>

    <script>
        let timer = null;
        const searchBox = document.getElementById('search-box');
        const resultsList = document.getElementById('results-list');

        function fetchBooks(kw) {
            fetch('c5_search_api.php?keyword=' + encodeURIComponent(kw))
                .then(res => res.json())
                .then(data => {
                    resultsList.innerHTML = '';
                    if (data.length === 0) {
                        const li = document.createElement('li');
                        li.textContent = 'Không tìm thấy sách nào phù hợp.';
                        resultsList.appendChild(li);
                        return;
                    }
                    data.forEach(book => {
                        const li = document.createElement('li');
                        li.textContent = `${book.title} - Tác giả: ${book.author} (${Number(book.price).toLocaleString('vi-VN')} đ)`;
                        resultsList.appendChild(li);
                    });
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                });
        }

        // Tải toàn bộ lúc đầu
        fetchBooks('');

        // Lắng nghe sự kiện input với Debounce 300ms
        searchBox.addEventListener('input', function() {
            clearTimeout(timer);
            const val = this.value.trim();
            timer = setTimeout(() => {
                fetchBooks(val);
            }, 300);
        });
    </script>
</body>
</html>
```

---

### Câu 6: `c6_query.sql`
```sql
-- 1. Thống kê số lượng và giá trung bình theo từng danh mục (kể cả danh mục chưa có sách)
SELECT 
    c.id, 
    c.category_name, 
    COALESCE(SUM(b.quantity), 0) AS total_quantity,
    COALESCE(AVG(b.price), 0) AS avg_price
FROM categories c
LEFT JOIN books b ON c.id = b.category_id
GROUP BY c.id, c.category_name
ORDER BY total_quantity DESC;

-- 2. Lấy các danh mục có tổng số sách >= 50 và giá trung bình > 80,000 đ
SELECT 
    c.id, 
    c.category_name, 
    SUM(b.quantity) AS total_quantity,
    AVG(b.price) AS avg_price
FROM categories c
JOIN books b ON c.id = b.category_id
GROUP BY c.id, c.category_name
HAVING total_quantity >= 50 AND avg_price > 80000;
```
