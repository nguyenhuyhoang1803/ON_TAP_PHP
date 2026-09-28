# ĐÁP ÁN & LỜI GIẢI MẪU - MOCK 03 (GHÉP KỸ NĂNG)

---

### Câu 1: `c1_doctor_upload.php`
```php
<?php
session_start();

// Giả lập phiên đăng nhập nếu chưa có để dễ test
if (!isset($_SESSION['doctor'])) {
    $_SESSION['doctor'] = [
        'id' => 101,
        'name' => 'BS. Nguyễn Văn An'
    ];
}

$doctor = $_SESSION['doctor'];
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['record_file'])) {
    $f = $_FILES['record_file'];
    $allowed = ['pdf', 'png', 'jpg', 'jpeg'];
    $maxSize = 3 * 1024 * 1024; // 3MB

    if ($f['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Lỗi upload file (Mã lỗi: ' . $f['error'] . ')';
    } else {
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            $errors[] = 'Chỉ chấp nhận file định dạng PDF, PNG hoặc JPG.';
        } elseif ($f['size'] > $maxSize) {
            $errors[] = 'Dung lượng file vượt quá giới hạn 3MB.';
        } else {
            $dir = __DIR__ . '/patient_records/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            $newName = sprintf("record_%d_%d_%s.%s", $doctor['id'], time(), bin2hex(random_bytes(4)), $ext);
            $destination = $dir . $newName;

            if (move_uploaded_file($f['tmp_name'], $destination)) {
                $log = sprintf("[%s] - Bác sĩ: %s (ID %d) - File: %s\n", date('Y-m-d H:i:s'), $doctor['name'], $doctor['id'], $newName);
                file_put_contents(__DIR__ . '/records.log', $log, FILE_APPEND | LOCK_EX);
                $success = 'Tải lên hồ sơ bệnh án thành công: ' . htmlspecialchars($newName);
            } else {
                $errors[] = 'Không thể di chuyển file vào thư mục lưu trữ.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Upload Bệnh Án</title></head>
<body>
    <h2>Upload Hồ Sơ Bệnh Án - Bác sĩ: <?= htmlspecialchars($doctor['name']) ?></h2>
    <?php if ($success): ?><p style="color: green;"><?= $success ?></p><?php endif; ?>
    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        Chọn file: <input type="file" name="record_file" required><br><br>
        <button type="submit">Lưu Hồ Sơ</button>
    </form>
</body>
</html>
```

---

### Câu 2: `src/Medical/Staff.php`, `src/Medical/Doctor.php`, `c2_autoload_test.php`

**File `src/Medical/Staff.php`:**
```php
<?php
namespace App\Medical;

abstract class Staff {
    protected int $id;
    protected string $name;
    protected float $baseSalary;

    public function __construct(int $id, string $name, float $baseSalary) {
        $this->id = $id;
        $this->name = $name;
        $this->baseSalary = $baseSalary;
    }

    abstract public function calculateMonthlySalary(): float;

    public function getName(): string {
        return $this->name;
    }
}
```

**File `src/Medical/Doctor.php`:**
```php
<?php
namespace App\Medical;

class Doctor extends Staff {
    private float $specialtyBonus;

    public function __construct(int $id, string $name, float $baseSalary, float $specialtyBonus) {
        parent::__construct($id, $name, $baseSalary);
        $this->specialtyBonus = $specialtyBonus;
    }

    public function calculateMonthlySalary(): float {
        return $this->baseSalary + $this->specialtyBonus;
    }
}
```

**File `c2_autoload_test.php`:**
```php
<?php
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/src/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Medical\Doctor;

$doctor = new Doctor(101, "BS. CKII Trần Quốc Toản", 15000000, 8000000);
echo "<h3>Thông Tin Bác Sĩ & Lương Tháng</h3>";
echo "Bác sĩ: " . htmlspecialchars($doctor->getName()) . "<br>";
echo "Lương thực lĩnh: <strong>" . number_format($doctor->calculateMonthlySalary(), 0, ',', '.') . " đ</strong>";
```

---

### Câu 3: `c3_api_patients.php` & `c3_live_search.html`

**File `c3_api_patients.php`:**
```php
<?php
header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = new PDO('mysql:host=localhost;dbname=clinic_db;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $q = trim($_GET['q'] ?? '');
    if ($q === '') {
        $stmt = $pdo->query("SELECT id, patient_code, fullname, phone FROM patients ORDER BY id DESC LIMIT 10");
        echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
        exit;
    }

    $sql = "SELECT id, patient_code, fullname, phone 
            FROM patients 
            WHERE patient_code LIKE :kw OR fullname LIKE :kw OR phone LIKE :kw 
            LIMIT 15";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':kw' => "%$q%"]);
    echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
exit;
```

**File `c3_live_search.html`:**
```html
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Tra Cứu Bệnh Nhân</title></head>
<body>
    <h2>Tra Cứu Bệnh Nhân Nhanh (Debounce AJAX)</h2>
    <input type="text" id="patient-search" placeholder="Nhập tên, mã BN hoặc số điện thoại..." style="width: 320px; padding: 6px;">
    <br><br>
    <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; min-width: 500px;">
        <thead>
            <tr style="background: #f2f2f2;"><th>Mã BN</th><th>Họ Tên</th><th>Số Điện Thoại</th></tr>
        </thead>
        <tbody id="patient-body"></tbody>
    </table>

    <script>
        let timer = null;
        const input = document.getElementById('patient-search');
        const tbody = document.getElementById('patient-body');

        function search(q) {
            fetch('c3_api_patients.php?q=' + encodeURIComponent(q))
                .then(r => r.json())
                .then(data => {
                    tbody.innerHTML = '';
                    if (!data || data.length === 0) {
                        const tr = document.createElement('tr');
                        tr.innerHTML = '<td colspan="3" style="text-align: center; color: gray;">Không tìm thấy bệnh nhân</td>';
                        tbody.appendChild(tr);
                        return;
                    }
                    data.forEach(p => {
                        const tr = document.createElement('tr');
                        const tdCode = document.createElement('td');
                        tdCode.textContent = p.patient_code;
                        const tdName = document.createElement('td');
                        tdName.textContent = p.fullname;
                        const tdPhone = document.createElement('td');
                        tdPhone.textContent = p.phone;

                        tr.append(tdCode, tdName, tdPhone);
                        tbody.appendChild(tr);
                    });
                })
                .catch(err => console.error(err));
        }

        search('');

        input.addEventListener('input', function() {
            clearTimeout(timer);
            const val = this.value.trim();
            timer = setTimeout(() => search(val), 300);
        });
    </script>
</body>
</html>
```

---

### Câu 4: `c4_prescriptions_pagination.php`
```php
<?php
$pdo = new PDO('mysql:host=localhost;dbname=clinic_db;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$limit = 5;
$page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;

// Whitelist sort & direction
$allowedSort = ['total_cost', 'created_at', 'prescription_code'];
$sort = in_array($_GET['sort'] ?? '', $allowedSort, true) ? $_GET['sort'] : 'created_at';
$dir = (strtoupper($_GET['dir'] ?? '') === 'ASC') ? 'ASC' : 'DESC';

// Đếm tổng số bản ghi
$countStmt = $pdo->query("SELECT COUNT(*) FROM prescriptions");
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalRows / $limit));

$page = min($page, $totalPages);
$offset = ($page - 1) * $limit;

// Truy vấn dữ liệu trang
$sql = "SELECT id, prescription_code, patient_name, total_cost, created_at 
        FROM prescriptions 
        ORDER BY $sort $dir 
        LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$prescriptions = $stmt->fetchAll();

function buildUrl(int $p, string $sort, string $dir): string {
    return '?' . http_build_query(['page' => $p, 'sort' => $sort, 'dir' => $dir]);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Danh Sách Toa Thuốc</title></head>
<body>
    <h2>Danh Sách Toa Thuốc (Trang <?= $page ?> / <?= $totalPages ?>)</h2>
    <table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th>Mã Toa</th>
                <th>Tên Bệnh Nhân</th>
                <th><a href="<?= buildUrl($page, 'total_cost', $dir === 'ASC' ? 'DESC' : 'ASC') ?>">Tổng Tiền</a></th>
                <th><a href="<?= buildUrl($page, 'created_at', $dir === 'ASC' ? 'DESC' : 'ASC') ?>">Ngày Kê</a></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($prescriptions as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['prescription_code']) ?></td>
                    <td><?= htmlspecialchars($p['patient_name']) ?></td>
                    <td><?= number_format((float)$p['total_cost'], 0, ',', '.') ?> đ</td>
                    <td><?= htmlspecialchars($p['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        <?php if ($page > 1): ?>
            <a href="<?= buildUrl($page - 1, $sort, $dir) ?>">« Trang trước</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="<?= buildUrl($i, $sort, $dir) ?>" style="<?= $i === $page ? 'font-weight: bold; text-decoration: underline;' : '' ?>">
                [<?= $i ?>]
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="<?= buildUrl($page + 1, $sort, $dir) ?>">Trang sau »</a>
        <?php endif; ?>
    </div>
</body>
</html>
```

---

### Câu 5: `c5_doctor_report.sql`
```sql
SELECT 
    d.id, 
    d.doctor_name, 
    d.department,
    COUNT(DISTINCT p.id) AS total_prescriptions,
    COALESCE(SUM(pi.quantity * pi.unit_price), 0) AS total_medicine_cost
FROM doctors d
LEFT JOIN prescriptions p ON d.id = p.doctor_id
LEFT JOIN prescription_items pi ON p.id = pi.prescription_id
GROUP BY d.id, d.doctor_name, d.department
HAVING total_medicine_cost >= 15000000
ORDER BY total_medicine_cost DESC;
```

---

### Câu 6: `c6_insurance_calc.php`
```php
<?php
$examFee = trim($_POST['exam_fee'] ?? '');
$labFee = trim($_POST['lab_fee'] ?? '');
$medFee = trim($_POST['med_fee'] ?? '');
$rate = trim($_POST['bhyt_rate'] ?? '80');

$allowedRates = ['0', '80', '100'];
$errors = [];
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_numeric($examFee) || (float)$examFee < 0) $errors[] = 'Tiền khám không hợp lệ.';
    if (!is_numeric($labFee) || (float)$labFee < 0)   $errors[] = 'Tiền xét nghiệm không hợp lệ.';
    if (!is_numeric($medFee) || (float)$medFee < 0)   $errors[] = 'Tiền thuốc không hợp lệ.';
    if (!in_array($rate, $allowedRates, true))        $errors[] = 'Tỷ lệ BHYT không hợp lệ.';

    if (empty($errors)) {
        $totalOrigin = (float)$examFee + (float)$labFee + (float)$medFee;
        $covered = $totalOrigin * ((float)$rate / 100);
        $patientPay = $totalOrigin - $covered;

        $result = [
            'total' => $totalOrigin,
            'covered' => $covered,
            'patient_pay' => $patientPay
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Tính Viện Phí & BHYT</title></head>
<body>
    <h2>Tính Quyền Lợi BHYT</h2>
    <?php if (!empty($errors)): ?>
        <ul style="color: red;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
    <?php endif; ?>

    <form method="POST">
        Tiền khám: <input type="number" name="exam_fee" value="<?= htmlspecialchars($examFee) ?>"><br><br>
        Tiền xét nghiệm: <input type="number" name="lab_fee" value="<?= htmlspecialchars($labFee) ?>"><br><br>
        Tiền thuốc: <input type="number" name="med_fee" value="<?= htmlspecialchars($medFee) ?>"><br><br>
        Mức hưởng BHYT:
        <select name="bhyt_rate">
            <option value="0" <?= $rate === '0' ? 'selected' : '' ?>>0% (Không thẻ)</option>
            <option value="80" <?= $rate === '80' ? 'selected' : '' ?>>80% (Chuẩn)</option>
            <option value="100" <?= $rate === '100' ? 'selected' : '' ?>>100% (Ưu tiên)</option>
        </select><br><br>
        <button type="submit">Tính Viện Phí</button>
    </form>

    <?php if ($result): ?>
        <h3>Hóa Đơn Thanh Toán</h3>
        <p>Tổng chi phí gốc: <?= number_format($result['total'], 0, ',', '.') ?> đ</p>
        <p>BHYT chi trả: -<?= number_format($result['covered'], 0, ',', '.') ?> đ</p>
        <h4>Bệnh nhân thanh toán: <?= number_format($result['patient_pay'], 0, ',', '.') ?> đ</h4>
    <?php endif; ?>
</body>
</html>
```
