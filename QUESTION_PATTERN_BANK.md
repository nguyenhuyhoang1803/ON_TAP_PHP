# NGÂN HÀNG MẪU CÂU HỎI THI THỰC HÀNH (QUESTION PATTERN BANK)
> **Mục tiêu:** Nhận diện ngay dạng bài, kích hoạt khung code trong đầu và hoàn thành mỗi câu trong 5 – 12 phút.

---

## Mục lục 10 Nhóm Dạng Bài

- [Nhóm A – PHP / Form / Mảng & Thuật Toán](#nhóm-a--php--form--mảng--thuật-toán) *(5 – 8 phút)*
- [Nhóm B – Session / Cookie / Phân Quyền](#nhóm-b--session--cookie--phân-quyền) *(6 – 9 phút)*
- [Nhóm C – Upload / File I/O & Log](#nhóm-c--upload--file-io--log) *(7 – 10 phút)*
- [Nhóm D – OOP (Kế thừa, Interface, Đa hình)](#nhóm-d--oop-kế-thừa-interface-đa-hình) *(8 – 12 phút)*
- [Nhóm E – Namespace / Autoload / PSR-4](#nhóm-e--namespace--autoload--psr-4) *(5 – 8 phút)*
- [Nhóm F – JavaScript / AJAX / Fetch & Debounce](#nhóm-f--javascript--ajax--fetch--debounce) *(7 – 10 phút)*
- [Nhóm G – PDO CRUD (Prepared Statement & PRG)](#nhóm-g--pdo-crud-prepared-statement--prg) *(8 – 12 phút)*
- [Nhóm H – SQL Core (JOIN, GROUP BY, Aggregate)](#nhóm-h--sql-core-join-group-by-aggregate) *(5 – 10 phút)*
- [Nhóm I – Search, Whitelist Sort & Pagination](#nhóm-i--search-whitelist-sort--pagination) *(8 – 12 phút)*
- [Nhóm J – Reporting / Báo Cáo Đa Bảng & Thống Kê](#nhóm-j--reporting--báo-cáo-đa-bảng--thống-kê) *(8 – 12 phút)*

---

## Nhóm A – PHP / Form / Mảng & Thuật Toán
- **Mục tiêu thời gian:** 5 – 8 phút.
- **Kỹ năng cốt lõi:**
  - Nhận request GET / POST: `$_SERVER['REQUEST_METHOD'] === 'POST'`
  - Validate dữ liệu: `trim()`, `filter_var(..., FILTER_VALIDATE_EMAIL)`, kiểm tra số dương, khoảng giá trị.
  - Sticky form: Giữ lại giá trị người dùng đã nhập khi có lỗi.
  - Xử lý mảng (filter, map, reduce, sort): `array_filter()`, `usort()`, `in_array()`.
  - Định dạng hiển thị: `htmlspecialchars()`, `number_format($price, 0, ',', '.') . ' đ'`.
- **Boilerplate phản xạ:**
```php
$errors = [];
$name = trim($_POST['name'] ?? '');
$price = trim($_POST['price'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($name === '') {
        $errors['name'] = 'Tên không được để trống';
    }
    if ($price === '' || !is_numeric($price) || (float)$price < 0) {
        $errors['price'] = 'Giá phải là số dương';
    }
    if (empty($errors)) {
        // Xử lý tính toán / lưu trữ
        $finalPrice = (float)$price * 1.1; // VAT
    }
}
```
- **Bẫy phòng thi:**
  - Dùng `empty($_POST['score'])`: Khi người dùng nhập `0`, hàm `empty()` trả về `true` $\rightarrow$ Validate sai! Phải dùng `$_POST['score'] === ''`.
  - Quên `htmlspecialchars($name)` trong `value=""` của `<input>` $\rightarrow$ Dính lỗ hổng XSS.

---

## Nhóm B – Session / Cookie / Phân Quyền
- **Mục tiêu thời gian:** 6 – 9 phút.
- **Kỹ năng cốt lõi:**
  - Login / Logout: `session_start()`, `$_SESSION['user'] = ...`, `session_destroy()`.
  - Guard (bảo vệ trang): Kiểm tra nếu chưa login thì redirect.
  - Cookie: Ghi nhớ tài khoản (`setcookie('remember_user', $val, time() + 86400 * 7, '/')`).
  - Flash message: Thông báo 1 lần sau khi redirect.
- **Boilerplate phản xạ:**
```php
session_start();

// Guard kiểm tra quyền
if (!isset($_SESSION['user'])) {
    $_SESSION['flash_error'] = 'Vui lòng đăng nhập trước!';
    header('Location: login.php');
    exit; // BẮT BUỘC CÓ EXIT
}

// Xử lý Flash message
$msg = $_SESSION['flash_msg'] ?? null;
unset($_SESSION['flash_msg']);
```
- **Bẫy phòng thi:**
  - Quên `exit;` sau `header('Location: ...');` $\rightarrow$ Mã bên dưới vẫn chạy tiếp, nguy cơ bypass bảo mật.
  - Gọi `session_start();` sau khi đã `echo` hoặc có khoảng trắng trước thẻ `<?php` $\rightarrow$ Lỗi `Headers already sent`.

---

## Nhóm C – Upload / File I/O & Log
- **Mục tiêu thời gian:** 7 – 10 phút.
- **Kỹ năng cốt lõi:**
  - Bắt lỗi upload: `$_FILES['file']['error'] === UPLOAD_ERR_OK`.
  - Validate dung lượng (`size <= 2 * 1024 * 1024`) và đuôi mở rộng (`in_array($ext, ['jpg', 'png', 'pdf'])`).
  - Đổi tên file ngẫu nhiên tránh trùng: `uniqid('doc_', true) . '.' . $ext`.
  - `move_uploaded_file($tmpPath, $targetPath)`.
  - Ghi log khóa file: `file_put_contents('log.txt', $entry, FILE_APPEND | LOCK_EX)`.
- **Boilerplate phản xạ:**
```php
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    
    $fileInfo = pathinfo($_FILES['avatar']['name']);
    $ext = strtolower($fileInfo['extension'] ?? '');
    
    if (!in_array($ext, $allowed, true) || $_FILES['avatar']['size'] > $maxSize) {
        $errors['avatar'] = 'File không hợp lệ hoặc vượt quá 2MB';
    } else {
        $newName = uniqid('img_', true) . '.' . $ext;
        $dest = __DIR__ . '/uploads/' . $newName;
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
            $log = sprintf("[%s] Upload: %s (%d bytes)\n", date('Y-m-d H:i:s'), $newName, $_FILES['avatar']['size']);
            file_put_contents(__DIR__ . '/uploads.log', $log, FILE_APPEND | LOCK_EX);
        }
    }
}
```
- **Bẫy phòng thi:**
  - Quên `enctype="multipart/form-data"` trên thẻ `<form>` $\rightarrow$ `$_FILES` hoàn toàn trống rỗng!
  - Lấy trực tiếp tên file gốc `$_FILES['avatar']['name']` để lưu $\rightarrow$ Gặp nguy cơ ghi đè file hoặc path traversal (`../../shell.php`).

---

## Nhóm D – OOP (Kế thừa, Interface, Đa hình)
- **Mục tiêu thời gian:** 8 – 12 phút.
- **Kỹ năng cốt lõi:**
  - `abstract class` chứa phương thức trừu tượng (`abstract public function calculateSalary();`).
  - `interface` định nghĩa hành vi chung (`interface Exportable { public function toArray(): array; }`).
  - Kế thừa (`extends`) và cài đặt (`implements`).
  - Đa hình (Polymorphism) qua vòng lặp duyệt mảng các đối tượng cha/interface.
  - Sắp xếp mảng đối tượng: `usort($list, fn($a, $b) => $b->getScore() <=> $a->getScore())`.
- **Boilerplate phản xạ:**
```php
abstract class Employee {
    protected string $id;
    protected string $name;
    protected float $baseRate;

    public function __construct(string $id, string $name, float $baseRate) {
        $this->id = $id;
        $this->name = $name;
        $this->baseRate = $baseRate;
    }
    abstract public function calculateIncome(): float;
    public function getName(): string { return $this->name; }
}

class FullTimeEmployee extends Employee {
    private float $bonus;
    public function __construct(string $id, string $name, float $baseRate, float $bonus) {
        parent::__construct($id, $name, $baseRate);
        $this->bonus = $bonus;
    }
    public function calculateIncome(): float {
        return $this->baseRate + $this->bonus;
    }
}

// Sắp xếp giảm dần theo thu nhập
usort($employees, fn($a, $b) => $b->calculateIncome() <=> $a->calculateIncome());
```
- **Bẫy phòng thi:**
  - Khai báo body `{}` cho abstract method trong abstract class $\rightarrow$ Fatal error!
  - Dùng sai toán tử spaceship: Muốn giảm dần thì `$b <=> $a`, muốn tăng dần thì `$a <=> $b`.

---

## Nhóm E – Namespace / Autoload / PSR-4
- **Mục tiêu thời gian:** 5 – 8 phút.
- **Kỹ năng cốt lõi:**
  - Khai báo `namespace App\Models;` ở dòng đầu tiên của file class.
  - Nhập class bằng `use App\Models\Student;`.
  - Tự động nạp qua `spl_autoload_register()` hoặc `composer.json` (PSR-4: `"App\\": "src/"`).
- **Boilerplate phản xạ:**
```php
// Cách 1: spl_autoload_register thuần không cần Composer
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/src/';
    
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) return;
    
    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Cách 2: Sử dụng Composer
// composer.json: "autoload": { "psr-4": { "App\\": "src/" } }
// Chạy terminal: composer dump-autoload
// Trong index.php: require_once __DIR__ . '/vendor/autoload.php';
```

---

## Nhóm F – JavaScript / AJAX / Fetch & Debounce
- **Mục tiêu thời gian:** 7 – 10 phút.
- **Kỹ năng cốt lõi:**
  - Bắt sự kiện `input` trên ô tìm kiếm.
  - Debounce 300ms bằng `clearTimeout` và `setTimeout`.
  - Gửi `fetch()` với tham số đã qua `encodeURIComponent()`.
  - Endpoint PHP trả về JSON: `header('Content-Type: application/json; charset=utf-8'); echo json_encode($data); exit;`.
  - Render an toàn vào DOM: Dùng `textContent` hoặc tạo phần tử `document.createElement()`, không dùng `innerHTML` với biến chưa escape.
- **Boilerplate phản xạ:**
```javascript
let debounceTimer = null;
const searchInput = document.getElementById('search-input');
const resultList = document.getElementById('result-list');

searchInput.addEventListener('input', function() {
    clearTimeout(debounceTimer);
    const keyword = this.value.trim();
    
    debounceTimer = setTimeout(() => {
        fetch('api_search.php?keyword=' + encodeURIComponent(keyword))
            .then(res => {
                if (!res.ok) throw new Error('Network error');
                return res.json();
            })
            .then(data => {
                resultList.innerHTML = '';
                if (data.length === 0) {
                    resultList.innerHTML = '<li>Không tìm thấy kết quả</li>';
                    return;
                }
                data.forEach(item => {
                    const li = document.createElement('li');
                    li.textContent = `${item.code} - ${item.name} (${item.price} đ)`;
                    resultList.appendChild(li);
                });
            })
            .catch(err => console.error(err));
    }, 300);
});
```

---

## Nhóm G – PDO CRUD (Prepared Statement & PRG)
- **Mục tiêu thời gian:** 8 – 12 phút.
- **Kỹ năng cốt lõi:**
  - PDO Connection với `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`, `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC`.
  - Chuẩn bị truy vấn `prepare()`, bind tham số `bindValue()`, thực thi `execute()`.
  - Bắt lỗi trùng khóa (Duplicate Entry) bằng `catch (PDOException $e)` mã lỗi `23000`.
  - Mô hình PRG (Post / Redirect / Get) sau khi Insert/Update/Delete.
- **Boilerplate phản xạ:**
```php
// config/database.php
$pdo = new PDO(
    'mysql:host=localhost;dbname=exam_db;charset=utf8mb4',
    'root',
    '',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
);

// Thêm mới có bắt trùng mã
try {
    $stmt = $pdo->prepare("INSERT INTO products (code, name, price) VALUES (:code, :name, :price)");
    $stmt->execute([
        ':code' => $code,
        ':name' => $name,
        ':price' => $price
    ]);
    header('Location: list.php?msg=created');
    exit;
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        $errors['code'] = 'Mã sản phẩm đã tồn tại trong hệ thống!';
    } else {
        $errors['system'] = 'Lỗi hệ thống cơ sở dữ liệu!';
    }
}
```

---

## Nhóm H – SQL Core (JOIN, GROUP BY, Aggregate)
- **Mục tiêu thời gian:** 5 – 10 phút.
- **Kỹ năng cốt lõi:**
  - `INNER JOIN` vs `LEFT JOIN` (hiển thị cả những bản ghi chưa có dữ liệu quan hệ).
  - `GROUP BY` theo khóa chính của bảng bên trái.
  - Xử lý NULL khi JOIN: `COALESCE(SUM(od.quantity), 0)` hoặc `IFNULL()`.
  - Điều kiện sau gom nhóm dùng `HAVING` (ví dụ: `HAVING total_spent >= 10000000`).
  - Xử lý bản ghi chưa từng phát sinh: `WHERE foreign_key IS NULL` hoặc `NOT EXISTS (...)`.
- **Query mẫu chuẩn:**
```sql
-- Lấy danh sách khách hàng và tổng tiền đã chi (kể cả khách chưa mua đơn nào)
SELECT 
    c.id, 
    c.fullname, 
    c.email,
    COUNT(o.id) AS total_orders,
    COALESCE(SUM(o.total_amount), 0) AS total_spent
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id
GROUP BY c.id, c.fullname, c.email
HAVING total_spent >= :min_spent
ORDER BY total_spent DESC;
```

---

## Nhóm I – Search, Whitelist Sort & Pagination
- **Mục tiêu thời gian:** 8 – 12 phút.
- **Kỹ năng cốt lõi:**
  - Tìm kiếm an toàn: Escape wildcard `%` và `_` trong từ khóa nếu cần.
  - Whitelist sắp xếp: Kiểm tra nghiêm ngặt `$_GET['sort']` và `$_GET['dir']`.
  - Phân trang: Đếm tổng dòng (`COUNT(*)`), tính `totalPages = ceil($total / $limit)`, clamp `$page = max(1, min($page, $totalPages))`, tính `offset = ($page - 1) * $limit`.
  - Bind đúng kiểu `PDO::PARAM_INT` cho `LIMIT` và `OFFSET`.
- **Boilerplate phản xạ:**
```php
// 1. Nhận và lọc tham số
$keyword = trim($_GET['kw'] ?? '');
$sort = in_array($_GET['sort'] ?? '', ['id', 'price', 'created_at'], true) ? $_GET['sort'] : 'id';
$dir = (strtoupper($_GET['dir'] ?? '') === 'DESC') ? 'DESC' : 'ASC';
$limit = 10;
$page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;

// 2. Điều kiện WHERE dùng chung
$where = 'WHERE 1=1';
$params = [];
if ($keyword !== '') {
    $where .= ' AND (name LIKE :kw OR code LIKE :kw)';
    $params[':kw'] = '%' . $keyword . '%';
}

// 3. Đếm tổng
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM products $where");
$countStmt->execute($params);
$totalRecords = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalRecords / $limit));
$page = min($page, $totalPages);
$offset = ($page - 1) * $limit;

// 4. Lấy dữ liệu trang
$sql = "SELECT * FROM products $where ORDER BY $sort $dir LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$items = $stmt->fetchAll();
```

---

## Nhóm J – Reporting / Báo Cáo Đa Bảng & Thống Kê
- **Mục tiêu thời gian:** 8 – 12 phút.
- **Kỹ năng cốt lõi:**
  - Thống kê doanh thu / số lượng qua 3 bảng: `categories` $\rightarrow$ `products` $\rightarrow$ `order_items`.
  - Xử lý đồng hạng cao nhất (Ties for Max): Dùng Subquery `HAVING total = (SELECT MAX(total_t) FROM (...))`.
  - Grand Total: Tính tổng toàn bộ tập dữ liệu đã lọc, không tính trên trang hiện tại.
  - Lọc theo khoảng ngày (Date Range): `o.created_at BETWEEN :start_date AND :end_date`.
- **Query mẫu xử lý đồng hạng (Không dùng LIMIT 1 mù quáng):**
```sql
SELECT 
    s.id, 
    s.student_code, 
    s.fullname, 
    AVG(g.score) AS avg_score
FROM students s
JOIN grades g ON s.id = g.student_id
GROUP BY s.id, s.student_code, s.fullname
HAVING avg_score = (
    -- Điểm trung bình cao nhất trường
    SELECT MAX(sub.avg_val)
    FROM (
        SELECT AVG(score) AS avg_val
        FROM grades
        GROUP BY student_id
    ) sub
);
```
