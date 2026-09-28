# ĐÁP ÁN & LỜI GIẢI MẪU - MOCK 05 (MÔ PHỎNG THI THẬT)

---

### Yêu Cầu 1: `trip_estimator.php`
```php
<?php
$km = trim($_POST['km'] ?? '');
$hour = trim($_POST['start_hour'] ?? '');
$type = trim($_POST['type'] ?? '4');

$errors = [];
$invoice = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($km === '' || !is_numeric($km) || (float)$km <= 0) {
        $errors['km'] = 'Số km di chuyển phải là một số lớn hơn 0.';
    }

    if ($hour === '' || !ctype_digit($hour) || (int)$hour < 0 || (int)$hour > 23) {
        $errors['hour'] = 'Giờ xuất phát phải từ 0 đến 23.';
    }

    if (!in_array($type, ['4', '7'], true)) {
        $errors['type'] = 'Loại xe không hợp lệ.';
    }

    if (empty($errors)) {
        $d = (float)$km;
        $h = (int)$hour;
        $baseFare = 0;

        if ($type === '4') {
            if ($d <= 1) {
                $baseFare = 15000;
            } elseif ($d <= 30) {
                $baseFare = 15000 + ($d - 1) * 12000;
            } else {
                $baseFare = 15000 + (29 * 12000) + ($d - 30) * 10000;
            }
        } else { // 7 chỗ
            if ($d <= 1) {
                $baseFare = 18000;
            } elseif ($d <= 30) {
                $baseFare = 18000 + ($d - 1) * 14000;
            } else {
                $baseFare = 18000 + (29 * 14000) + ($d - 30) * 11500;
            }
        }

        // Kiểm tra ban đêm (22h đến 5h sáng: 22, 23, 0, 1, 2, 3, 4, 5)
        $isNight = ($h >= 22 || $h <= 5);
        $surcharge = $isNight ? ($baseFare * 0.15) : 0;
        $total = $baseFare + $surcharge;

        $invoice = [
            'base' => $baseFare,
            'is_night' => $isNight,
            'surcharge' => $surcharge,
            'total' => $total
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Ước Tính Cước Xe</title></head>
<body>
    <h2>Ước Tính Cước Chuyến Đi</h2>
    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST">
        Số km: <input type="number" step="0.1" name="km" value="<?= htmlspecialchars($km) ?>"><br><br>
        Giờ xuất phát (0 - 23): <input type="number" name="start_hour" value="<?= htmlspecialchars($hour) ?>"><br><br>
        Loại xe:
        <select name="type">
            <option value="4" <?= $type === '4' ? 'selected' : '' ?>>Xe 4 chỗ</option>
            <option value="7" <?= $type === '7' ? 'selected' : '' ?>>Xe 7 chỗ</option>
        </select><br><br>
        <button type="submit">Tính Tiền Cước</button>
    </form>

    <?php if ($invoice): ?>
        <h3>Chi Phí Tạm Tính</h3>
        <p>Cước gốc: <?= number_format($invoice['base'], 0, ',', '.') ?> đ</p>
        <?php if ($invoice['is_night']): ?>
            <p>Phụ phí ban đêm (15%): +<?= number_format($invoice['surcharge'], 0, ',', '.') ?> đ</p>
        <?php endif; ?>
        <h4>Tổng thanh toán: <?= number_format($invoice['total'], 0, ',', '.') ?> đ</h4>
    <?php endif; ?>
</body>
</html>
```

---

### Yêu Cầu 2: `dispatcher_login.php` & `dispatcher_panel.php`

**File `dispatcher_login.php`:**
```php
<?php
session_start();

$remembered = $_COOKIE['remember_dispatcher'] ?? '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = trim($_POST['password'] ?? '');
    $remember = isset($_POST['remember']);

    if ($u === 'dieuhanh' && $p === 'xe@2026') {
        $_SESSION['dispatcher'] = [
            'username' => $u,
            'login_time' => time()
        ];

        if ($remember) {
            setcookie('remember_dispatcher', $u, time() + (3 * 86400), '/');
        } else {
            setcookie('remember_dispatcher', '', time() - 3600, '/');
        }

        header('Location: dispatcher_panel.php');
        exit;
    } else {
        $error = 'Thông tin đăng nhập không chính xác!';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Đăng Nhập Điều Vận</title></head>
<body>
    <h2>Đăng Nhập Trung Tâm Điều Vận</h2>
    <?php if ($error): ?><p style="color: red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="POST">
        Tài khoản: <input type="text" name="username" value="<?= htmlspecialchars($remembered) ?>"><br><br>
        Mật khẩu: <input type="password" name="password"><br><br>
        <label><input type="checkbox" name="remember" <?= $remembered ? 'checked' : '' ?>> Lưu đăng nhập trong 3 ngày</label><br><br>
        <button type="submit">Vào Hệ Thống</button>
    </form>
</body>
</html>
```

**File `dispatcher_panel.php`:**
```php
<?php
session_start();

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['dispatcher']);
    session_destroy();
    header('Location: dispatcher_login.php');
    exit;
}

if (!isset($_SESSION['dispatcher'])) {
    header('Location: dispatcher_login.php');
    exit;
}

$user = $_SESSION['dispatcher'];
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Bảng Điều Vận</title></head>
<body>
    <h2>Khu Vực Làm Việc Của Điều Vận Viên</h2>
    <p>Xin chào: <strong><?= htmlspecialchars($user['username']) ?></strong></p>
    <p>Đăng nhập lúc: <?= date('H:i:s d/m/Y', $user['login_time']) ?></p>
    <p><a href="?action=logout">Đăng xuất</a></p>
</body>
</html>
```

---

### Yêu Cầu 3: `driver_license_upload.php`
```php
<?php
$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['license_photo'])) {
    $file = $_FILES['license_photo'];
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $maxSize = 2 * 1024 * 1024;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $err = 'Lỗi trong quá trình upload: ' . $file['error'];
    } else {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            $err = 'Chỉ chấp nhận định dạng JPG, PNG hoặc WEBP.';
        } elseif ($file['size'] > $maxSize) {
            $err = 'Dung lượng file vượt quá giới hạn 2MB.';
        } else {
            $folder = __DIR__ . '/driver_licenses/';
            if (!is_dir($folder)) mkdir($folder, 0755, true);

            $newName = uniqid('gplx_', true) . '.' . $ext;
            $dest = $folder . $newName;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $logLine = sprintf("[%s] | %s | %d bytes\n", date('Y-m-d H:i:s'), $newName, $file['size']);
                file_put_contents(__DIR__ . '/upload_log.txt', $logLine, FILE_APPEND | LOCK_EX);
                $msg = 'Tiếp nhận Giấy phép lái xe thành công!';
            } else {
                $err = 'Không thể lưu file vào máy chủ.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Cập Nhật Bằng Lái Xe</title></head>
<body>
    <h2>Cập Nhật Giấy Phép Lái Xe</h2>
    <?php if ($msg): ?><p style="color: green;"><?= $msg ?></p><?php endif; ?>
    <?php if ($err): ?><p style="color: red;"><?= $err ?></p><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        Chọn ảnh bằng lái: <input type="file" name="license_photo" required><br><br>
        <button type="submit">Gửi Ảnh Xác Minh</button>
    </form>
</body>
</html>
```

---

### Yêu Cầu 4: `fleet_management.php`
```php
<?php
abstract class Vehicle {
    protected string $licensePlate;
    protected float $baseFuelRate; // lít / 100km

    public function __construct(string $licensePlate, float $baseFuelRate) {
        $this->licensePlate = $licensePlate;
        $this->baseFuelRate = $baseFuelRate;
    }

    abstract public function calculateFuelCost(float $km, float $fuelPrice): float;

    public function getPlate(): string {
        return $this->licensePlate;
    }
}

class Truck extends Vehicle {
    private float $cargoWeight; // tấn

    public function __construct(string $licensePlate, float $baseFuelRate, float $cargoWeight) {
        parent::__construct($licensePlate, $baseFuelRate);
        $this->cargoWeight = $cargoWeight;
    }

    public function calculateFuelCost(float $km, float $fuelPrice): float {
        // Tăng 5% mỗi tấn
        $effectiveRate = $this->baseFuelRate * (1 + ($this->cargoWeight * 0.05));
        $liters = ($km / 100) * $effectiveRate;
        return $liters * $fuelPrice;
    }
}

class Bus extends Vehicle {
    public function calculateFuelCost(float $km, float $fuelPrice): float {
        $liters = ($km / 100) * $this->baseFuelRate;
        $fuelCost = $liters * $fuelPrice;
        $acFee = 50000; // Phụ phí điều hòa cố định
        return $fuelCost + $acFee;
    }
}

$fleet = [
    new Truck("29C-123.45", 14.0, 5.0), // 14 * (1 + 0.25) = 17.5 lít/100km -> 200km = 35 lít -> 35 * 23500 = 822,500
    new Truck("51D-999.88", 12.0, 2.0), // 12 * (1 + 0.10) = 13.2 lít/100km -> 200km = 26.4 lít -> 620,400
    new Bus("43B-777.22", 18.0)         // 18 lít/100km -> 200km = 36 lít -> 36 * 23500 + 50000 = 896,000
];

$km = 200.0;
$fuelPrice = 23500.0;

// Sắp xếp tăng dần theo chi phí nhiên liệu
usort($fleet, fn($a, $b) => $a->calculateFuelCost($km, $fuelPrice) <=> $b->calculateFuelCost($km, $fuelPrice));

echo "<h3>BẢNG CHI PHÍ NHIÊN LIỆU ĐỘI XE CHO CHUYẾN ĐI 200KM:</h3><ul>";
foreach ($fleet as $v) {
    $cost = $v->calculateFuelCost($km, $fuelPrice);
    echo "<li>Biển số: <strong>" . htmlspecialchars($v->getPlate()) . "</strong> - Chi phí: " 
         . number_format($cost, 0, ',', '.') . " đ</li>";
}
echo "</ul>";
```

---

### Yêu Cầu 5: `api_vehicles.php` & `search_vehicles.html`

**File `api_vehicles.php`:**
```php
<?php
header('Content-Type: application/json; charset=utf-8');

$vehicles = [
    ['plate' => '29A-111.22', 'brand' => 'Toyota Vios', 'seats' => 4, 'daily_price' => 700000],
    ['plate' => '30F-333.44', 'brand' => 'Honda City', 'seats' => 4, 'daily_price' => 750000],
    ['plate' => '51G-555.66', 'brand' => 'Toyota Innova', 'seats' => 7, 'daily_price' => 1000000],
    ['plate' => '43H-777.88', 'brand' => 'Ford Everest', 'seats' => 7, 'daily_price' => 1200000],
    ['plate' => '60K-999.00', 'brand' => 'Hyundai Accent', 'seats' => 4, 'daily_price' => 650000],
];

$q = trim($_GET['q'] ?? '');

if ($q === '') {
    echo json_encode($vehicles, JSON_UNESCAPED_UNICODE);
    exit;
}

$filtered = array_filter($vehicles, function($v) use ($q) {
    return mb_stripos($v['plate'], $q) !== false || mb_stripos($v['brand'], $q) !== false;
});

echo json_encode(array_values($filtered), JSON_UNESCAPED_UNICODE);
exit;
```

**File `search_vehicles.html`:**
```html
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Tra Cứu Xe</title></head>
<body>
    <h2>Tra Cứu Xe Tự Lái</h2>
    <input type="text" id="kw" placeholder="Nhập biển số hoặc tên xe..." style="width: 280px; padding: 6px;">
    <br><br>
    <div id="display-area"></div>

    <script>
        let timer = null;
        const kwInput = document.getElementById('kw');
        const display = document.getElementById('display-area');

        function loadVehicles(query) {
            fetch('api_vehicles.php?q=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    display.innerHTML = '';
                    if (!data || data.length === 0) {
                        display.innerHTML = '<p style="color: gray;">Không tìm thấy phương tiện nào phù hợp.</p>';
                        return;
                    }
                    const ul = document.createElement('ul');
                    data.forEach(item => {
                        const li = document.createElement('li');
                        li.textContent = `${item.plate} - ${item.brand} (${item.seats} chỗ) - Giá: ${Number(item.daily_price).toLocaleString('vi-VN')} đ/ngày`;
                        ul.appendChild(li);
                    });
                    display.appendChild(ul);
                })
                .catch(err => console.error(err));
        }

        loadVehicles('');

        kwInput.addEventListener('input', function() {
            clearTimeout(timer);
            const val = this.value.trim();
            timer = setTimeout(() => loadVehicles(val), 300);
        });
    </script>
</body>
</html>
```

---

### Yêu Cầu 6: `fleet_reports.sql`
```sql
-- 1. Thống kê chuyến hoàn thành và doanh thu theo tài xế
SELECT 
    d.id, 
    d.driver_name, 
    d.phone,
    COUNT(CASE WHEN t.status = 'completed' THEN t.id END) AS total_completed_trips,
    COALESCE(SUM(CASE WHEN t.status = 'completed' THEN t.trip_cost ELSE 0 END), 0) AS total_revenue
FROM drivers d
LEFT JOIN trips t ON d.id = t.driver_id
GROUP BY d.id, d.driver_name, d.phone
ORDER BY total_revenue DESC;

-- 2. Tìm tài xế có tổng doanh thu hoàn thành cao nhất (xử lý đồng hạng, không dùng LIMIT 1)
SELECT 
    d.id, 
    d.driver_name, 
    COALESCE(SUM(t.trip_cost), 0) AS max_revenue
FROM drivers d
LEFT JOIN trips t ON d.id = t.driver_id AND t.status = 'completed'
GROUP BY d.id, d.driver_name
HAVING max_revenue = (
    SELECT MAX(sub.total_rev)
    FROM (
        SELECT COALESCE(SUM(t2.trip_cost), 0) AS total_rev
        FROM drivers d2
        LEFT JOIN trips t2 ON d2.id = t2.driver_id AND t2.status = 'completed'
        GROUP BY d2.id
    ) sub
);
```
