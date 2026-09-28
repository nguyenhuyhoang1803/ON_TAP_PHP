# ĐÁP ÁN HOÀN CHỈNH: MOCK EXAM 03

### 1. `db.php`
```php
<?php
$host = '127.0.0.1';
$db   = 'mock03_restaurant';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL!");
}

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
```

---

### 2. `booking.php` (Câu 1: Form Đặt Bàn, Validation Ngày, PRG, Flash)
```php
<?php
session_start();
require_once 'db.php';

$errors = [];
$name   = '';
$phone  = '';
$guests = '';
$date   = date('Y-m-d');
$time   = '19:00';
$note   = '';

$flash = $_SESSION['flash_booking'] ?? null;
unset($_SESSION['flash_booking']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($_POST['customer_name'] ?? '');
    $phone  = trim($_POST['phone'] ?? '');
    $guests = trim($_POST['guest_count'] ?? '');
    $date   = trim($_POST['booking_date'] ?? '');
    $time   = trim($_POST['booking_time'] ?? '');
    $note   = trim($_POST['note'] ?? '');

    // 1. Validate tên
    if ($name === '') {
        $errors['customer_name'] = 'Họ tên không được để trống.';
    } elseif (mb_strlen($name) < 3) {
        $errors['customer_name'] = 'Họ tên phải có từ 3 ký tự trở lên.';
    }

    // 2. Validate SĐT
    if ($phone === '') {
        $errors['phone'] = 'Số điện thoại không được để trống.';
    } elseif (!preg_match('/^0[0-9]{9}$/', $phone)) {
        $errors['phone'] = 'Số điện thoại phải gồm đúng 10 chữ số bắt đầu bằng số 0.';
    }

    // 3. Validate số người
    if ($guests === '') {
        $errors['guest_count'] = 'Số người không được để trống.';
    } elseif (!filter_var($guests, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 30]])) {
        $errors['guest_count'] = 'Số lượng khách phải từ 1 đến 30 người.';
    }

    // 4. Validate ngày đặt (Không được là ngày quá khứ)
    $today = date('Y-m-d');
    if ($date === '') {
        $errors['booking_date'] = 'Ngày đặt bàn không được để trống.';
    } elseif ($date < $today) {
        $errors['booking_date'] = 'Ngày đặt bàn không thể là ngày trong quá khứ.';
    }

    if ($time === '') {
        $errors['booking_time'] = 'Giờ đến không được để trống.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO reservations (customer_name, phone, guest_count, booking_date, booking_time, note) 
                                   VALUES (:name, :phone, :guests, :bdate, :btime, :note)");
            $stmt->execute([
                'name'   => $name,
                'phone'  => $phone,
                'guests' => (int)$guests,
                'bdate'  => $date,
                'btime'  => $time,
                'note'   => $note !== '' ? $note : null,
            ]);
            $newId = $pdo->lastInsertId();

            // PRG Pattern
            $_SESSION['flash_booking'] = "Đặt bàn thành công! Mã số đặt chỗ của bạn là: #" . $newId;
            header('Location: booking.php');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $errors['general'] = "Có lỗi xảy ra khi lưu đặt bàn. Vui lòng thử lại!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Đặt Bàn Nhà Hàng</title></head>
<body>
    <h2>ĐẶT BÀN TRỰC TUYẾN - NHÀ HÀNG GOLDEN SPOON</h2>
    <p><a href="find_table.html">🔍 Tra cứu bàn trống realtime</a> | <a href="table_report.php">📊 Báo cáo doanh thu</a></p>

    <?php if ($flash): ?>
        <div style="background:#e6ffed; border:1px solid #34d058; padding:10px; margin-bottom:15px; width:500px;">
            <strong><?= e($flash) ?></strong>
        </div>
    <?php endif; ?>

    <?php if (isset($errors['general'])): ?><p style="color:red;"><?= e($errors['general']) ?></p><?php endif; ?>

    <form method="POST" action="">
        <div>
            Họ và tên khách (*):<br>
            <input type="text" name="customer_name" value="<?= e($name) ?>" style="width:300px;">
            <?php if (isset($errors['customer_name'])): ?><div style="color:red;"><?= e($errors['customer_name']) ?></div><?php endif; ?>
        </div><br>

        <div>
            Số điện thoại (*):<br>
            <input type="text" name="phone" value="<?= e($phone) ?>" placeholder="0901234567">
            <?php if (isset($errors['phone'])): ?><div style="color:red;"><?= e($errors['phone']) ?></div><?php endif; ?>
        </div><br>

        <div>
            Số lượng người (*):<br>
            <input type="number" name="guest_count" value="<?= e($guests) ?>" min="1" max="30">
            <?php if (isset($errors['guest_count'])): ?><div style="color:red;"><?= e($errors['guest_count']) ?></div><?php endif; ?>
        </div><br>

        <div>
            Ngày đặt bàn (*):<br>
            <input type="date" name="booking_date" value="<?= e($date) ?>">
            <?php if (isset($errors['booking_date'])): ?><div style="color:red;"><?= e($errors['booking_date']) ?></div><?php endif; ?>
        </div><br>

        <div>
            Giờ đến (*):<br>
            <input type="time" name="booking_time" value="<?= e($time) ?>">
            <?php if (isset($errors['booking_time'])): ?><div style="color:red;"><?= e($errors['booking_time']) ?></div><?php endif; ?>
        </div><br>

        <div>
            Ghi chú thêm:<br>
            <textarea name="note" rows="3" style="width:300px;"><?= e($note) ?></textarea>
        </div><br>

        <button type="submit">Xác Nhận Đặt Bàn</button>
    </form>
</body>
</html>
```

---

### 3. `api_tables.php` & `find_table.html` (Câu 2: AJAX Fetch & Debounce)
```php
<?php
// api_tables.php
require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

$capacity = filter_var($_GET['capacity'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
$type     = trim($_GET['type'] ?? 'all');

$sql = "SELECT id, code, name, type, capacity, status FROM tables WHERE status = 'available'";
$params = [];

if ($capacity > 0) {
    $sql .= " AND capacity >= :cap";
    $params['cap'] = $capacity;
}

if ($type !== 'all' && in_array($type, ['indoor', 'outdoor', 'vip'], true)) {
    $sql .= " AND type = :type";
    $params['type'] = $type;
}

$sql .= " ORDER BY capacity ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tables = $stmt->fetchAll();

echo json_encode($tables);
exit;
```

```html
<!-- find_table.html -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tra Cứu Bàn Trống Realtime</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .controls { background: #f8f9fa; padding: 15px; border-radius: 5px; width: 600px; margin-bottom: 15px; }
        table { width: 630px; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #eee; }
        #loading { display: none; color: blue; font-style: italic; }
    </style>
</head>
<body>
    <h2>TRA CỨU BÀN ĂN CÒN TRỐNG (REALTIME)</h2>
    <p><a href="booking.php">⬅ Quay lại form đặt bàn</a></p>

    <div class="controls">
        <label>Sức chứa tối thiểu:
            <select id="capacitySelect">
                <option value="0">Tất cả sức chứa</option>
                <option value="2">Từ 2 người trở lên</option>
                <option value="4">Từ 4 người trở lên</option>
                <option value="8">Từ 8 người trở lên</option>
                <option value="10">Phòng tiệc (10+ người)</option>
            </select>
        </label>
        &nbsp;&nbsp;&nbsp;
        <label>Khu vực:
            <select id="typeSelect">
                <option value="all">Tất cả khu vực</option>
                <option value="indoor">Trong nhà (Indoor)</option>
                <option value="outdoor">Sân vườn (Outdoor)</option>
                <option value="vip">Phòng VIP</option>
            </select>
        </label>
        <span id="loading"> ⏳ Đang tra cứu...</span>
    </div>

    <table>
        <thead>
            <tr><th>Mã bàn</th><th>Tên bàn</th><th>Khu vực</th><th>Sức chứa</th><th>Trạng thái</th></tr>
        </thead>
        <tbody id="tableResults">
            <tr><td colspan="5">Đang nạp dữ liệu...</td></tr>
        </tbody>
    </table>

    <script>
        function debounce(fn, delay = 300) {
            let t;
            return (...args) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...args), delay);
            };
        }

        const capEl = document.getElementById('capacitySelect');
        const typeEl = document.getElementById('typeSelect');
        const tbodyEl = document.getElementById('tableResults');
        const loadingEl = document.getElementById('loading');

        async function fetchTables() {
            loadingEl.style.display = 'inline';
            const cap = capEl.value;
            const type = typeEl.value;

            try {
                const res = await fetch(`api_tables.php?capacity=${encodeURIComponent(cap)}&type=${encodeURIComponent(type)}`);
                if (!res.ok) throw new Error('Lỗi máy chủ');
                const data = await res.json();

                tbodyEl.textContent = ''; // Clear an toàn
                if (data.length === 0) {
                    tbodyEl.innerHTML = '<tr><td colspan="5" align="center" style="color:red;">Không có bàn nào còn trống phù hợp tiêu chí!</td></tr>';
                    return;
                }

                data.forEach(item => {
                    const tr = document.createElement('tr');

                    const tdCode = document.createElement('td');
                    tdCode.textContent = item.code;

                    const tdName = document.createElement('td');
                    tdName.textContent = item.name;

                    const tdType = document.createElement('td');
                    tdType.textContent = (item.type === 'vip') ? 'Phòng VIP' : (item.type === 'outdoor' ? 'Sân vườn' : 'Trong nhà');

                    const tdCap = document.createElement('td');
                    tdCap.textContent = item.capacity + ' người';

                    const tdStatus = document.createElement('td');
                    tdStatus.textContent = '✅ Đang trống';
                    tdStatus.style.color = 'green';

                    tr.appendChild(tdCode);
                    tr.appendChild(tdName);
                    tr.appendChild(tdType);
                    tr.appendChild(tdCap);
                    tr.appendChild(tdStatus);
                    tbodyEl.appendChild(tr);
                });
            } catch (err) {
                tbodyEl.innerHTML = `<tr><td colspan="5" style="color:red;">Lỗi: ${err.message}</td></tr>`;
            } finally {
                loadingEl.style.display = 'none';
            }
        }

        // Tải ban đầu
        fetchTables();

        // Lắng nghe sự kiện qua Debounce
        const debouncedSearch = debounce(fetchTables, 300);
        capEl.addEventListener('change', debouncedSearch);
        typeEl.addEventListener('change', debouncedSearch);
    </script>
</body>
</html>
```

---

### 4. `table_report.php` (Câu 3: SQL Thống Kê & Xử Lý Đồng Hạng)
```php
<?php
require_once 'db.php';

// Câu lệnh thống kê tất cả các bàn kèm doanh thu (LEFT JOIN để giữ bàn chưa có hóa đơn)
$sql = "SELECT 
            t.id,
            t.code,
            t.name,
            t.type,
            COUNT(b.id) AS total_servings,
            COALESCE(SUM(b.total_amount), 0) AS total_revenue
        FROM tables t
        LEFT JOIN bills b ON t.id = b.table_id
        GROUP BY t.id, t.code, t.name, t.type
        ORDER BY total_revenue DESC";

$report = $pdo->query($sql)->fetchAll();

// Câu truy vấn phụ tìm danh sách các bàn đạt doanh thu MAX (Xử lý đồng hạng)
$sqlMax = "SELECT t.id 
           FROM tables t 
           LEFT JOIN bills b ON t.id = b.table_id 
           GROUP BY t.id 
           HAVING COALESCE(SUM(b.total_amount), 0) = (
               SELECT COALESCE(SUM(b2.total_amount), 0)
               FROM tables t2
               LEFT JOIN bills b2 ON t2.id = b2.table_id
               GROUP BY t2.id
               ORDER BY COALESCE(SUM(b2.total_amount), 0) DESC
               LIMIT 1
           )";
$maxTableIds = $pdo->query($sqlMax)->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Báo Cáo Doanh Thu Bàn Ăn</title></head>
<body>
    <h2>BÁO CÁO HIỆU QUẢ KINH DOANH THEO BÀN ĂN</h2>
    <p><a href="booking.php">⬅ Đặt bàn</a> | <a href="find_table.html">🔍 Tra cứu bàn</a></p>

    <table border="1" cellpadding="6" style="border-collapse: collapse; width: 750px;">
        <tr bgcolor="#eee">
            <th>Mã bàn</th>
            <th>Tên bàn</th>
            <th>Khu vực</th>
            <th>Lượt phục vụ</th>
            <th>Tổng doanh thu</th>
            <th>Đánh giá</th>
        </tr>
        <?php foreach ($report as $r): ?>
            <tr>
                <td><?= e($r['code']) ?></td>
                <td><?= e($r['name']) ?></td>
                <td><?= e($r['type']) ?></td>
                <td align="center"><?= (int)$r['total_servings'] ?></td>
                <td align="right"><?= number_format($r['total_revenue'], 0, ',', '.') ?> đ</td>
                <td>
                    <?php if (in_array($r['id'], $maxTableIds) && $r['total_revenue'] > 0): ?>
                        <span style="color: red; font-weight: bold;">🏆 Doanh thu cao nhất (Đồng hạng)</span>
                    <?php elseif ($r['total_servings'] == 0): ?>
                        <span style="color: gray;">Chưa có khách</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
```

---

### 5. `DiscountService.php` (Câu 4: OOP Interface & Đa Hình)
```php
<?php
// DiscountService.php

interface DiscountPolicy {
    public function applyDiscount(float $billAmount): float;
}

class StandardMember implements DiscountPolicy {
    public function applyDiscount(float $billAmount): float {
        return 0.0; // Không giảm giá
    }
}

class VipMember implements DiscountPolicy {
    public function applyDiscount(float $billAmount): float {
        return $billAmount * 0.15; // Giảm 15%
    }
}

class BirthdayMember implements DiscountPolicy {
    public function applyDiscount(float $billAmount): float {
        if ($billAmount >= 500000) {
            return $billAmount * 0.20; // Giảm 20%
        }
        return $billAmount * 0.10; // Giảm 10%
    }
}

// CHẠY THỬ NGHIỆM ĐA HÌNH
$bill = 1200000.0;
$policies = [
    'Khách Thường (Standard)' => new StandardMember(),
    'Khách VIP (VipMember)'   => new VipMember(),
    'Khách Sinh Nhật'         => new BirthdayMember(),
];

echo "=== TÍNH TIỀN HÓA ĐƠN GỐC: " . number_format($bill, 0, ',', '.') . " VNĐ ===\n";
foreach ($policies as $title => $policy) {
    $discount = $policy->applyDiscount($bill);
    $pay = $bill - $discount;
    echo "- {$title}: Giảm " . number_format($discount, 0, ',', '.') . " đ | Cần thanh toán: " . number_format($pay, 0, ',', '.') . " VNĐ\n";
}
```
