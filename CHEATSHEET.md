# CHEATSHEET ÔN THI 10 PHÚT TRƯỚC GIỜ G
> Chỉ chứa các bộ khung mã nguồn (skeleton) tối giản nhất. Không giải thích dông dài. Đọc lướt để nạp phản xạ cú pháp vào não.

---

## 1. PHP Core, Form & Validation

### GET / POST & Escape Output
```php
// Lấy dữ liệu an toàn kèm giá trị mặc định
$keyword = trim($_GET['keyword'] ?? '');
$email   = trim($_POST['email'] ?? '');

// Bắt buộc escape khi render ra HTML
function e($val): string {
    return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
}
// Dùng trong HTML: <?= e($user['fullname']) ?>
```

### Validation Cốt lõi (Bẫy số 0)
```php
$errors = [];
// Kiểm tra rỗng chuẩn (không dùng empty() vì empty("0") là true!)
if ($username === '') {
    $errors['username'] = 'Tên đăng nhập không được để trống';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Email không đúng định dạng';
}
// Validate số nguyên >= 0
$quantity = $_POST['quantity'] ?? '';
if (!filter_var($quantity, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]])) {
    $errors['quantity'] = 'Số lượng phải là số nguyên không âm';
}
```

---

## 2. Session, Cookie & Auth Guard

### Login Guard & Redirect
```php
// Đầu file MỌI trang cần bảo vệ:
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit; // BẮT BUỘC có exit sau redirect
}
```

### Xử lý Đăng nhập & Đăng xuất
```php
// Login thành công:
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['flash_msg'] = 'Đăng nhập thành công!';
header('Location: index.php');
exit;

// Logout.php:
session_start();
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
```

### Cookie (Ghi nhớ tên đăng nhập 7 ngày)
```php
// Tạo cookie: setcookie(name, value, expire, path)
setcookie('remember_user', $username, time() + 7 * 24 * 3600, '/');

// Xóa cookie: đặt expire về quá khứ
setcookie('remember_user', '', time() - 3600, '/');

// Đọc cookie:
$rememberedUser = $_COOKIE['remember_user'] ?? '';
```

---

## 3. Upload File & Đọc Ghi File An Toàn

### File Upload Skeleton
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        die('Lỗi upload: ' . $file['error']);
    }
    
    // Kiểm tra kích thước (tối đa 2MB = 2 * 1024 * 1024)
    if ($file['size'] > 2097152) die('File vượt quá 2MB');
    
    // Whitelist đuôi mở rộng
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($ext, $allowed, true)) die('Chỉ chấp nhận ảnh');
    
    // Đổi tên file ngẫu nhiên chống trùng & Path Traversal
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    
    $newName = uniqid('img_', true) . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
        echo "Upload thành công: " . $newName;
    }
}
```

### Đọc & Ghi File có Khóa (LOCK_EX)
```php
$logPath = __DIR__ . '/activity.log';
// Ghi thêm dòng mới, an toàn khi có nhiều truy cập đồng thời
file_put_contents($logPath, date('Y-m-d H:i:s') . " - Action log\n", FILE_APPEND | LOCK_EX);

// Đọc file:
$content = file_exists($logPath) ? file_get_contents($logPath) : '';
```

---

## 4. OOP PHP & Đa hình (Polymorphism)

```php
abstract class Animal {
    public function __construct(protected string $name) {}
    abstract public function speak(): string;
    public function getName(): string { return $this->name; }
}

interface Runnable {
    public function run(): int;
}

class Dog extends Animal implements Runnable {
    private static int $count = 0;
    public function __construct(string $name, private int $speed) {
        parent::__construct($name);
        self::$count++;
    }
    public function speak(): string { return "Gâu gâu!"; }
    public function run(): int { return $this->speed; }
    public static function getCount(): int { return self::$count; }
}

// Sắp xếp mảng đối tượng:
usort($dogs, fn($a, $b) => $b->run() <=> $a->run()); // Giảm dần theo tốc độ
```

---

## 5. Namespace, Autoload & Composer PSR-4

### spl_autoload_register Tự chế
```php
spl_autoload_register(function ($class) {
    // Prefix 'App\' trỏ vào thư mục 'src/'
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require $file;
});
```

### composer.json (PSR-4)
```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```
*Lệnh CLI cần nhớ sau khi sửa namespace:*  
`composer dump-autoload`  
*Trong file `index.php`:*  
`require_once __DIR__ . '/vendor/autoload.php';`

---

## 6. JavaScript Fetch API & Debounce

```javascript
// Hàm Debounce chuẩn phòng thi
function debounce(fn, delay = 300) {
    let timerId;
    return (...args) => {
        clearTimeout(timerId);
        timerId = setTimeout(() => fn(...args), delay);
    };
}

const inputEl = document.getElementById('search-input');
const tbodyEl = document.getElementById('result-table-body');

inputEl.addEventListener('input', debounce(async (e) => {
    const q = e.target.value.trim();
    try {
        const res = await fetch(`api_search.php?kw=${encodeURIComponent(q)}`);
        if (!res.ok) throw new Error(`HTTP Error ${res.status}`);
        const data = await res.json();
        
        // Render an toàn chống XSS
        tbodyEl.textContent = ''; // Xóa sạch dữ liệu cũ
        if (data.length === 0) {
            tbodyEl.innerHTML = '<tr><td colspan="3">Không có kết quả</td></tr>';
            return;
        }
        data.forEach(item => {
            const tr = document.createElement('tr');
            const td1 = document.createElement('td');
            const td2 = document.createElement('td');
            td1.textContent = item.id;
            td2.textContent = item.name; // textContent tự encode, an toàn 100%
            tr.appendChild(td1);
            tr.appendChild(td2);
            tbodyEl.appendChild(tr);
        });
    } catch (err) {
        console.error('Fetch error:', err);
    }
}, 300));
```

---

## 7. PDO Connection & CRUD Chuẩn Mực

### Kết nối PDO Chuẩn Phòng Thi
```php
$dsn = "mysql:host=localhost;dbname=exam_db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, 'root', '', $options);
```

### SELECT
```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE status = :st AND age >= :min_age");
$stmt->execute(['st' => 1, 'min_age' => 18]);
$users = $stmt->fetchAll(); // Mảng các dòng
$oneUser = $stmt->fetch();  // Lấy 1 dòng
```

### INSERT & Xử lý Trùng Khóa (Duplicate Key)
```php
try {
    $stmt = $pdo->prepare("INSERT INTO users (username, email) VALUES (:u, :e)");
    $stmt->execute(['u' => $username, 'e' => $email]);
    $newId = $pdo->lastInsertId();
    header("Location: list.php?msg=success");
    exit;
} catch (PDOException $ex) {
    if ($ex->getCode() == 23000) { // Lỗi trùng UNIQUE constraint
        $error = "Tên đăng nhập hoặc Email đã tồn tại!";
    } else {
        error_log($ex->getMessage()); // Không in lỗi DB ra giao diện!
        $error = "Lỗi hệ thống. Vui lòng thử lại sau.";
    }
}
```

### UPDATE & DELETE
```php
// Update
$stmt = $pdo->prepare("UPDATE users SET fullname = :fn WHERE id = :id");
$stmt->execute(['fn' => $fullname, 'id' => $id]);

// Delete
$stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
$stmt->execute(['id' => $id]);
```

---

## 8. Tìm Kiếm, Sắp Xếp Whitelist & Phân Trang

```php
// 1. Nhận và xử lý đầu vào
$keyword = trim($_GET['kw'] ?? '');
$sort    = $_GET['sort'] ?? 'id';
$dir     = strtoupper($_GET['dir'] ?? 'ASC');
$page    = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
$limit   = 10;

// 2. Escape ký tự đại diện của LIKE
$escapedKw = strtr($keyword, ['%' => '\%', '_' => '\_']);

// 3. Whitelist bắt buộc chống SQL Injection ở ORDER BY
$allowedSort = ['id', 'name', 'price', 'created_at'];
$sortColumn = in_array($sort, $allowedSort, true) ? $sort : 'id';
$sortDir    = in_array($dir, ['ASC', 'DESC'], true) ? $dir : 'ASC';

// 4. Đếm tổng số dòng
$countSql = "SELECT COUNT(*) FROM products WHERE name LIKE :kw";
$stmt = $pdo->prepare($countSql);
$stmt->execute(['kw' => "%$escapedKw%"]);
$totalRows = (int)$stmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalRows / $limit));

// Kẹp trang hợp lệ (clamp)
$page = min($page, $totalPages);
$offset = ($page - 1) * $limit;

// 5. Query lấy dữ liệu (Lưu ý bindValue cho LIMIT và OFFSET dạng PARAM_INT)
$sql = "SELECT * FROM products WHERE name LIKE :kw ORDER BY $sortColumn $sortDir LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':kw', "%$escapedKw%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$items = $stmt->fetchAll();

// 6. Giữ Query Parameter khi tạo link phân trang:
// <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next</a>
```

---

## 9. SQL Mẫu Phản Xạ Nhanh

### INNER JOIN (Chỉ lấy khi 2 bên có liên kết)
```sql
SELECT o.id, o.order_date, c.name AS customer_name
FROM orders o
INNER JOIN customers c ON o.customer_id = c.id;
```

### LEFT JOIN (Lấy TẤT CẢ bên trái, dù chưa có bên phải)
```sql
-- Lấy tất cả khách hàng, kèm tổng đơn hàng (Kể cả khách hàng chưa từng mua)
SELECT c.id, c.name, COUNT(o.id) AS total_orders, COALESCE(SUM(o.amount), 0) AS total_spent
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id
GROUP BY c.id, c.name;
```

### Tìm bản ghi KHÔNG TỒN TẠI (Khách chưa mua gì, Sản phẩm chưa bán được)
```sql
-- Cách 1: LEFT JOIN + IS NULL
SELECT c.id, c.name
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id
WHERE o.id IS NULL;

-- Cách 2: NOT EXISTS (Rất tối ưu)
SELECT c.id, c.name
FROM customers c
WHERE NOT EXISTS (SELECT 1 FROM orders o WHERE o.customer_id = c.id);
```

### GROUP BY + HAVING (Lọc sau khi gom nhóm)
```sql
-- Tìm các danh mục có từ 5 sản phẩm trở lên VÀ giá trung bình > 100k
SELECT category_id, COUNT(*) AS total_items, AVG(price) AS avg_price
FROM products
WHERE status = 1           -- WHERE: lọc từng dòng trước khi group
GROUP BY category_id
HAVING COUNT(*) >= 5 AND AVG(price) > 100000; -- HAVING: lọc nhóm kết quả
```
