# ĐÁP ÁN & LỜI GIẢI MẪU - MOCK 02

---

### Câu 1: `c1_calc_booking.php`
```php
<?php
$errors = [];
$invoice = null;

$guestName = trim($_POST['guest_name'] ?? '');
$checkIn = trim($_POST['check_in'] ?? '');
$checkOut = trim($_POST['check_out'] ?? '');
$roomRate = trim($_POST['room_rate'] ?? '');
$childrenCount = trim($_POST['children_count'] ?? '0');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (mb_strlen($guestName) < 3) {
        $errors['guest_name'] = 'Tên khách hàng phải từ 3 ký tự trở lên.';
    }

    $dIn = DateTime::createFromFormat('Y-m-d', $checkIn);
    $dOut = DateTime::createFromFormat('Y-m-d', $checkOut);

    if (!$dIn || !$dOut || $dIn->format('Y-m-d') !== $checkIn || $dOut->format('Y-m-d') !== $checkOut) {
        $errors['dates'] = 'Định dạng ngày không hợp lệ (chuẩn Y-m-d).';
    } elseif ($dOut <= $dIn) {
        $errors['dates'] = 'Ngày trả phòng (Check-out) phải sau ngày nhận phòng (Check-in) ít nhất 1 ngày.';
    }

    if ($roomRate === '' || !is_numeric($roomRate) || (float)$roomRate < 200000) {
        $errors['room_rate'] = 'Giá phòng phải từ 200.000 đ trở lên.';
    }

    if ($childrenCount === '' || !ctype_digit($childrenCount) || (int)$childrenCount < 0) {
        $errors['children_count'] = 'Số trẻ em phải là số nguyên không âm.';
    }

    if (empty($errors)) {
        $nights = $dIn->diff($dOut)->days;
        $childSurcharge = (int)$childrenCount * 100000 * $nights;
        $roomCost = (float)$roomRate * $nights;
        $total = $roomCost + $childSurcharge;

        $invoice = [
            'guest' => $guestName,
            'nights' => $nights,
            'room_cost' => $roomCost,
            'child_surcharge' => $childSurcharge,
            'total' => $total
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Đặt Phòng Khách Sạn</title></head>
<body>
    <h2>Tính Tiền Đặt Phòng</h2>
    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="">
        Tên khách: <input type="text" name="guest_name" value="<?= htmlspecialchars($guestName) ?>"><br><br>
        Check-in: <input type="date" name="check_in" value="<?= htmlspecialchars($checkIn) ?>"><br><br>
        Check-out: <input type="date" name="check_out" value="<?= htmlspecialchars($checkOut) ?>"><br><br>
        Giá phòng/đêm: <input type="number" name="room_rate" value="<?= htmlspecialchars($roomRate) ?>"><br><br>
        Số trẻ em: <input type="number" name="children_count" value="<?= htmlspecialchars($childrenCount) ?>"><br><br>
        <button type="submit">Tính Tiền</button>
    </form>

    <?php if ($invoice): ?>
        <h3>Hóa Đơn Tạm Tính</h3>
        <p>Khách: <strong><?= htmlspecialchars($invoice['guest']) ?></strong></p>
        <p>Số đêm: <?= $invoice['nights'] ?></p>
        <p>Tiền phòng: <?= number_format($invoice['room_cost'], 0, ',', '.') ?> đ</p>
        <p>Phụ thu trẻ em: <?= number_format($invoice['child_surcharge'], 0, ',', '.') ?> đ</p>
        <h4>Tổng cộng: <?= number_format($invoice['total'], 0, ',', '.') ?> đ</h4>
    <?php endif; ?>
</body>
</html>
```

---

### Câu 2: `c2_login.php` & `c2_admin.php`

**File `c2_login.php`:**
```php
<?php
session_start();

$savedUser = $_COOKIE['saved_user'] ?? '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = trim($_POST['password'] ?? '');
    $remember = isset($_POST['remember']);

    $users = [
        'receptionist' => ['pass' => '123456', 'role' => 'staff'],
        'manager'      => ['pass' => 'adminpass', 'role' => 'admin']
    ];

    if (isset($users[$u]) && $users[$u]['pass'] === $p) {
        $_SESSION['auth_user'] = [
            'username' => $u,
            'role' => $users[$u]['role']
        ];

        if ($remember) {
            setcookie('saved_user', $u, time() + 7 * 86400, '/');
        } else {
            setcookie('saved_user', '', time() - 3600, '/');
        }

        if ($users[$u]['role'] === 'admin') {
            header('Location: c2_admin.php');
            exit;
        } else {
            header('Location: c2_staff.php');
            exit;
        }
    } else {
        $error = 'Sai tên đăng nhập hoặc mật khẩu!';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Login Hotel</title></head>
<body>
    <h2>Đăng Nhập Quản Lý Khách Sạn</h2>
    <?php if ($error): ?><p style="color: red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="POST">
        Username: <input type="text" name="username" value="<?= htmlspecialchars($savedUser) ?>"><br><br>
        Password: <input type="password" name="password"><br><br>
        <label><input type="checkbox" name="remember" <?= $savedUser ? 'checked' : '' ?>> Ghi nhớ tài khoản</label><br><br>
        <button type="submit">Đăng nhập</button>
    </form>
</body>
</html>
```

**File `c2_admin.php`:**
```php
<?php
session_start();

if (!isset($_SESSION['auth_user'])) {
    header('Location: c2_login.php');
    exit;
}

if ($_SESSION['auth_user']['role'] !== 'admin') {
    http_response_code(403);
    echo "<h2>403 - Bạn không có quyền truy cập trang quản trị!</h2>";
    echo "<p><a href='c2_login.php'>Quay lại</a></p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Admin Portal</title></head>
<body>
    <h2>Chào mừng Quản lý (Admin Portal)</h2>
    <p>Người dùng: <?= htmlspecialchars($_SESSION['auth_user']['username']) ?></p>
</body>
</html>
```

---

### Câu 3: `c3_upload_id.php`
```php
<?php
$message = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['id_card'])) {
    $f = $_FILES['id_card'];
    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    if ($f['error'] !== UPLOAD_ERR_OK) {
        $err = 'Lỗi upload: mã lỗi ' . $f['error'];
    } else {
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            $err = 'Chỉ chấp nhận file định dạng JPG, PNG hoặc PDF.';
        } elseif ($f['size'] > $maxSize) {
            $err = 'Kích thước file không được vượt quá 2MB.';
        } else {
            $targetDir = __DIR__ . '/id_storage/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

            $storedName = 'guest_id_' . md5(uniqid((string)time(), true)) . '.' . $ext;
            $targetPath = $targetDir . $storedName;

            if (move_uploaded_file($f['tmp_name'], $targetPath)) {
                $csvFile = __DIR__ . '/guests_access.csv';
                $fp = fopen($csvFile, 'a');
                if ($fp && flock($fp, LOCK_EX)) {
                    fputcsv($fp, [
                        date('Y-m-d H:i:s'),
                        $f['name'],
                        $storedName,
                        $f['size']
                    ]);
                    flock($fp, LOCK_UN);
                    fclose($fp);
                }
                $message = 'Tải lên tài liệu CCCD thành công!';
            } else {
                $err = 'Lỗi hệ thống khi lưu trữ file.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Upload CCCD Khách</title></head>
<body>
    <h2>Upload Hộ Chiếu / CCCD</h2>
    <?php if ($message): ?><p style="color: green;"><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <?php if ($err): ?><p style="color: red;"><?= htmlspecialchars($err) ?></p><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="id_card" required><br><br>
        <button type="submit">Tải lên</button>
    </form>
</body>
</html>
```

---

### Câu 4: `c4_oop_hotel.php`
```php
<?php
interface VipService {
    public function getVipAmenities(): array;
}

abstract class Room {
    protected string $roomNumber;
    protected float $dailyRate;

    public function __construct(string $roomNumber, float $dailyRate) {
        $this->roomNumber = $roomNumber;
        $this->dailyRate = $dailyRate;
    }

    abstract public function calculateCost(int $days): float;

    public function getRoomNumber(): string {
        return $this->roomNumber;
    }
}

class StandardRoom extends Room {
    public function calculateCost(int $days): float {
        return $days * $this->dailyRate;
    }
}

class SuiteRoom extends Room implements VipService {
    private float $minibarFee;

    public function __construct(string $roomNumber, float $dailyRate, float $minibarFee) {
        parent::__construct($roomNumber, $dailyRate);
        $this->minibarFee = $minibarFee;
    }

    public function calculateCost(int $days): float {
        return ($days * $this->dailyRate * 1.2) + $this->minibarFee;
    }

    public function getVipAmenities(): array {
        return ['Đưa đón sân bay miễn phí', 'Buffet sáng tại phòng', 'Spa 60 phút'];
    }
}

function printInvoice(Room $room, int $days): void {
    $cost = $room->calculateCost($days);
    $type = ($room instanceof SuiteRoom) ? 'Suite (VIP)' : 'Standard';

    echo "<div style='border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;'>";
    echo "<h3>Phòng: " . htmlspecialchars($room->getRoomNumber()) . " - Loại: $type</h3>";
    echo "<p>Số ngày: $days ngày | Tổng tiền: <strong>" . number_format($cost, 0, ',', '.') . " đ</strong></p>";

    if ($room instanceof VipService) {
        echo "<p>Tiện ích VIP kèm theo:</p><ul>";
        foreach ($room->getVipAmenities() as $amenity) {
            echo "<li>" . htmlspecialchars($amenity) . "</li>";
        }
        echo "</ul>";
    }
    echo "</div>";
}

// Chạy thử
$rooms = [
    new StandardRoom("P101", 500000),
    new SuiteRoom("P505", 1500000, 300000)
];

foreach ($rooms as $r) {
    printInvoice($r, 3);
}
```

---

### Câu 5: `c5_booking_create.php`
```php
<?php
$pdo = new PDO(
    'mysql:host=localhost;dbname=hotel_db;charset=utf8mb4',
    'root',
    '',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['booking_code'] ?? '');
    $guest = trim($_POST['guest_name'] ?? '');
    $roomId = (int)($_POST['room_id'] ?? 0);
    $total = (float)($_POST['total_price'] ?? 0);

    if ($code === '' || $guest === '' || $roomId <= 0 || $total <= 0) {
        $error = 'Vui lòng điền đầy đủ và chính xác tất cả thông tin.';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO bookings (booking_code, guest_name, room_id, total_price, created_at)
                VALUES (:code, :guest, :room_id, :total, NOW())
            ");
            $stmt->execute([
                ':code' => $code,
                ':guest' => $guest,
                ':room_id' => $roomId,
                ':total' => $total
            ]);

            // PRG Pattern
            header('Location: c5_booking_list.php?status=success');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'Mã đặt phòng "' . htmlspecialchars($code) . '" đã tồn tại!';
            } else {
                $error = 'Lỗi hệ thống cơ sở dữ liệu: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Tạo Đặt Phòng</title></head>
<body>
    <h2>Thêm Mới Đơn Đặt Phòng</h2>
    <?php if ($error): ?><p style="color: red;"><?= $error ?></p><?php endif; ?>
    <form method="POST">
        Mã đặt phòng: <input type="text" name="booking_code" required><br><br>
        Tên khách: <input type="text" name="guest_name" required><br><br>
        ID Phòng: <input type="number" name="room_id" required><br><br>
        Tổng tiền: <input type="number" name="total_price" required><br><br>
        <button type="submit">Lưu Đặt Phòng</button>
    </form>
</body>
</html>
```

---

### Câu 6: `c6_hotel_queries.sql`
```sql
-- 1. Thống kê số lượt đặt thành công và tổng doanh thu theo khách sạn (kể cả khách sạn chưa có lượt đặt nào)
SELECT 
    h.id, 
    h.hotel_name, 
    h.city,
    COUNT(CASE WHEN b.status = 'completed' THEN b.id END) AS completed_bookings,
    COALESCE(SUM(CASE WHEN b.status = 'completed' THEN b.total_price ELSE 0 END), 0) AS total_revenue
FROM hotels h
LEFT JOIN bookings b ON h.id = b.hotel_id
GROUP BY h.id, h.hotel_name, h.city
ORDER BY total_revenue DESC;

-- 2. Tìm khách sạn có tổng doanh thu cao nhất tại Đà Nẵng (xử lý đồng hạng, không dùng LIMIT 1)
SELECT 
    h.id, 
    h.hotel_name, 
    COALESCE(SUM(b.total_price), 0) AS max_revenue
FROM hotels h
LEFT JOIN bookings b ON h.id = b.hotel_id AND b.status = 'completed'
WHERE h.city = 'Đà Nẵng'
GROUP BY h.id, h.hotel_name
HAVING max_revenue = (
    SELECT MAX(sub.total_danang)
    FROM (
        SELECT COALESCE(SUM(b2.total_price), 0) AS total_danang
        FROM hotels h2
        LEFT JOIN bookings b2 ON h2.id = b2.hotel_id AND b2.status = 'completed'
        WHERE h2.city = 'Đà Nẵng'
        GROUP BY h2.id
    ) sub
);
```
