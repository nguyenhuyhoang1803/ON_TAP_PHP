# ĐÁP ÁN HOÀN CHỈNH: MOCK EXAM 04 (NÂNG CAO)

### 1. `db.php` (Kết Nối & Ghi Log Lỗi An Toàn)
```php
<?php
$host = '127.0.0.1';
$db   = 'mock04_hospital';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // Không in lỗi CSDL ra màn hình, ghi log nội bộ
    error_log("[" . date('Y-m-d H:i:s') . "] PDO Conn Error: " . $e->getMessage() . "\n", 3, __DIR__ . '/db_error.log');
    die("Hệ thống bệnh viện đang bảo trì cơ sở dữ liệu. Vui lòng thử lại sau!");
}

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
```

---

### 2. `register_patient.php` (Câu 1: Form Tiếp Nhận & Upload File PDF)
```php
<?php
require_once 'db.php';

$errors = [];
$idCard   = '';
$fullname = '';
$dob      = '';
$gender   = 'Nam';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idCard   = trim($_POST['id_card'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $dob      = trim($_POST['dob'] ?? '');
    $gender   = trim($_POST['gender'] ?? 'Nam');

    // 1. Validate CCCD
    if ($idCard === '') {
        $errors['id_card'] = 'Mã CCCD/BHYT không được để trống.';
    } elseif (!preg_match('/^[0-9]{10,12}$/', $idCard)) {
        $errors['id_card'] = 'Mã CCCD/BHYT phải gồm từ 10 đến 12 chữ số.';
    }

    // 2. Validate Họ tên
    if ($fullname === '') {
        $errors['fullname'] = 'Họ và tên không được để trống.';
    } elseif (mb_strlen($fullname) < 3) {
        $errors['fullname'] = 'Họ và tên phải có ít nhất 3 ký tự.';
    }

    if ($dob === '') {
        $errors['dob'] = 'Vui lòng chọn ngày sinh.';
    }

    // 3. Xử lý Upload file bệnh án
    $savedFilename = null;
    $uploadedPath = null;

    if (isset($_FILES['record_file']) && $_FILES['record_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $f = $_FILES['record_file'];
        if ($f['error'] !== UPLOAD_ERR_OK) {
            $errors['record_file'] = 'Lỗi upload file (Mã lỗi: ' . $f['error'] . ')';
        } elseif ($f['size'] > 2 * 1024 * 1024) {
            $errors['record_file'] = 'Dung lượng file PDF tối đa là 2MB.';
        } else {
            $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
            if ($ext !== 'pdf') {
                $errors['record_file'] = 'Chỉ chấp nhận hồ sơ bệnh án định dạng PDF!';
            } else {
                $targetDir = __DIR__ . '/storage/medical_records/';
                if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

                $savedFilename = uniqid('record_', true) . '.pdf';
                $uploadedPath = $targetDir . $savedFilename;
                if (!move_uploaded_file($f['tmp_name'], $uploadedPath)) {
                    $errors['record_file'] = 'Không thể lưu trữ file vào thư mục bảo mật.';
                }
            }
        }
    }

    // 4. Lưu vào CSDL
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO patients (id_card, fullname, dob, gender, record_file) 
                                   VALUES (:id_card, :fullname, :dob, :gender, :record_file)");
            $stmt->execute([
                'id_card'     => $idCard,
                'fullname'    => $fullname,
                'dob'         => $dob,
                'gender'      => $gender,
                'record_file' => $savedFilename,
            ]);

            header('Location: patient_list.php?msg=success');
            exit;
        } catch (PDOException $ex) {
            // Xóa file rác nếu insert DB thất bại
            if ($uploadedPath && file_exists($uploadedPath)) {
                unlink($uploadedPath);
            }

            if ($ex->getCode() == 23000) {
                $errors['general'] = "Mã định danh CCCD/BHYT [{$idCard}] đã tồn tại trong hồ sơ bệnh viện!";
            } else {
                error_log("Patient Insert Error: " . $ex->getMessage());
                $errors['general'] = "Lỗi khi lưu trữ hồ sơ bệnh án.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Tiếp Nhận Bệnh Nhân</title></head>
<body>
    <h2>TIẾP NHẬN HỒ SƠ BỆNH NHÂN</h2>
    <p><a href="patient_list.php">📋 Xem danh sách bệnh nhân</a> | <a href="doctor_stats.php">📊 Thống kê bác sĩ</a></p>

    <?php if (isset($errors['general'])): ?><p style="color:red; font-weight:bold;"><?= e($errors['general']) ?></p><?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div>
            Mã CCCD/BHYT (*):<br>
            <input type="text" name="id_card" value="<?= e($idCard) ?>" placeholder="001099000001">
            <?php if (isset($errors['id_card'])): ?><div style="color:red"><?= e($errors['id_card']) ?></div><?php endif; ?>
        </div><br>

        <div>
            Họ và tên (*):<br>
            <input type="text" name="fullname" value="<?= e($fullname) ?>" style="width:300px;">
            <?php if (isset($errors['fullname'])): ?><div style="color:red"><?= e($errors['fullname']) ?></div><?php endif; ?>
        </div><br>

        <div>
            Ngày sinh (*):<br>
            <input type="date" name="dob" value="<?= e($dob) ?>">
            <?php if (isset($errors['dob'])): ?><div style="color:red"><?= e($errors['dob']) ?></div><?php endif; ?>
        </div><br>

        <div>
            Giới tính:
            <label><input type="radio" name="gender" value="Nam" <?= $gender === 'Nam' ? 'checked' : '' ?>> Nam</label>
            <label><input type="radio" name="gender" value="Nữ" <?= $gender === 'Nữ' ? 'checked' : '' ?>> Nữ</label>
        </div><br>

        <div>
            Hồ sơ bệnh án cũ (PDF, tối đa 2MB):<br>
            <input type="file" name="record_file" accept=".pdf">
            <?php if (isset($errors['record_file'])): ?><div style="color:red"><?= e($errors['record_file']) ?></div><?php endif; ?>
        </div><br>

        <button type="submit">Lưu Hồ Sơ Bệnh Nhân</button>
    </form>
</body>
</html>
```

---

### 3. `patient_list.php` (Câu 2: Tìm Kiếm Ký Tự Đặc Biệt & Phân Trang Kẹp)
```php
<?php
require_once 'db.php';

$kw    = trim($_GET['kw'] ?? '');
$sort  = $_GET['sort'] ?? 'id';
$dir   = strtoupper($_GET['dir'] ?? 'DESC');
$page  = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT) ?: 1;
$limit = 5;

// Escape wildcard LIKE
$escapedKw = strtr($kw, ['%' => '\%', '_' => '\_']);

$where = ["1=1"];
$params = [];
if ($escapedKw !== '') {
    $where[] = "(fullname LIKE :kw OR id_card LIKE :kw)";
    $params['kw'] = "%$escapedKw%";
}
$whereSql = implode(' AND ', $where);

// Tính tổng số dòng
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE $whereSql");
$countStmt->execute($params);
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalRows / $limit));

// Kẹp trang chuẩn: [1, totalPages]
$page = max(1, min($page, $totalPages));
$offset = ($page - 1) * $limit;

// Whitelist Sort
$allowedSort = ['id' => 'id', 'fullname' => 'fullname', 'dob' => 'dob', 'created_at' => 'created_at'];
$sortCol = $allowedSort[$sort] ?? 'id';
$sortDir = ($dir === 'ASC') ? 'ASC' : 'DESC';

// Lấy dữ liệu
$sql = "SELECT * FROM patients WHERE $whereSql ORDER BY $sortCol $sortDir LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue(":$k", $v);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$patients = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Danh Sách Bệnh Nhân</title></head>
<body>
    <h2>DANH SÁCH BỆNH NHÂN TIẾP NHẬN</h2>
    <p><a href="register_patient.php">➕ Tiếp nhận bệnh nhân mới</a> | <a href="doctor_stats.php">📊 Thống kê bác sĩ</a></p>

    <?php if (($_GET['msg'] ?? '') === 'success'): ?>
        <p style="color:green; font-weight:bold;">Tiếp nhận hồ sơ bệnh nhân thành công!</p>
    <?php endif; ?>

    <form method="GET" action="">
        Tìm kiếm: <input type="text" name="kw" value="<?= e($kw) ?>" placeholder="Tên hoặc CCCD...">
        Sắp xếp theo:
        <select name="sort">
            <option value="id" <?= $sort === 'id' ? 'selected' : '' ?>>Mã ID</option>
            <option value="fullname" <?= $sort === 'fullname' ? 'selected' : '' ?>>Họ và tên</option>
            <option value="dob" <?= $sort === 'dob' ? 'selected' : '' ?>>Ngày sinh</option>
            <option value="created_at" <?= $sort === 'created_at' ? 'selected' : '' ?>>Ngày tiếp nhận</option>
        </select>
        <select name="dir">
            <option value="DESC" <?= $sortDir === 'DESC' ? 'selected' : '' ?>>Giảm dần</option>
            <option value="ASC" <?= $sortDir === 'ASC' ? 'selected' : '' ?>>Tăng dần</option>
        </select>
        <button type="submit">Lọc</button>
        <?php if ($kw !== ''): ?><a href="patient_list.php">Bỏ lọc</a><?php endif; ?>
    </form>
    <br>

    <p>Hiển thị <?= count($patients) ?> / <?= $totalRows ?> bệnh nhân (Trang <?= $page ?> / <?= $totalPages ?>)</p>

    <table border="1" cellpadding="6" style="border-collapse: collapse; width: 850px;">
        <tr bgcolor="#eee">
            <th>ID</th>
            <th>Mã CCCD/BHYT</th>
            <th>Họ và Tên</th>
            <th>Ngày sinh</th>
            <th>Giới tính</th>
            <th>Bệnh án PDF</th>
            <th>Ngày tiếp nhận</th>
        </tr>
        <?php if (empty($patients)): ?>
            <tr><td colspan="7" align="center">Không tìm thấy bệnh nhân nào!</td></tr>
        <?php else: ?>
            <?php foreach ($patients as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= e($p['id_card']) ?></td>
                    <td><?= e($p['fullname']) ?></td>
                    <td><?= date('d/m/Y', strtotime($p['dob'])) ?></td>
                    <td align="center"><?= e($p['gender']) ?></td>
                    <td align="center">
                        <?php if ($p['record_file']): ?>
                            <a href="storage/medical_records/<?= urlencode($p['record_file']) ?>" target="_blank">📄 Tải PDF</a>
                        <?php else: ?>
                            <span style="color:gray;">Không có</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $p['created_at'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <div style="margin-top: 15px;">
        Trang:
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php $link = '?' . http_build_query(array_merge($_GET, ['page' => $i])); ?>
            <a href="<?= e($link) ?>" style="margin-right: 6px; <?= $i === $page ? 'font-weight:bold; color:red;' : '' ?>">[<?= $i ?>]</a>
        <?php endfor; ?>
    </div>
</body>
</html>
```

---

### 4. `doctor_stats.php` (Câu 3: SQL Báo Cáo Tháng 9 + LEFT JOIN + Đồng Hạng)
```php
<?php
require_once 'db.php';

// Câu lệnh thống kê: Bắt buộc đặt điều kiện tháng 9 trong ON để giữ bác sĩ chưa kê đơn nào
$sql = "SELECT 
            d.id,
            d.code AS doctor_code,
            d.fullname AS doctor_name,
            d.department,
            COUNT(pr.id) AS total_prescriptions,
            COALESCE(SUM(pr.total_cost), 0) AS total_drug_cost
        FROM doctors d
        LEFT JOIN prescriptions pr ON d.id = pr.doctor_id 
            AND pr.prescribed_at >= '2026-09-01' 
            AND pr.prescribed_at <= '2026-09-30'
        GROUP BY d.id, d.code, d.fullname, d.department
        ORDER BY total_drug_cost DESC";

$report = $pdo->query($sql)->fetchAll();

// Subquery tìm các bác sĩ đạt MAX chi phí thuốc trong tháng 9 (Xử lý đồng hạng)
$sqlMax = "SELECT d.id
           FROM doctors d
           LEFT JOIN prescriptions pr ON d.id = pr.doctor_id 
               AND pr.prescribed_at >= '2026-09-01' 
               AND pr.prescribed_at <= '2026-09-30'
           GROUP BY d.id
           HAVING COALESCE(SUM(pr.total_cost), 0) = (
               SELECT COALESCE(SUM(pr2.total_cost), 0)
               FROM doctors d2
               LEFT JOIN prescriptions pr2 ON d2.id = pr2.doctor_id 
                   AND pr2.prescribed_at >= '2026-09-01' 
                   AND pr2.prescribed_at <= '2026-09-30'
               GROUP BY d2.id
               ORDER BY COALESCE(SUM(pr2.total_cost), 0) DESC
               LIMIT 1
           )";
$topDoctorIds = $pdo->query($sqlMax)->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Thống Kê Bác Sĩ</title></head>
<body>
    <h2>BÁO CÁO THỐNG KÊ CHI PHÍ KÊ ĐƠN THUỐC THÁNG 09/2026</h2>
    <p><a href="patient_list.php">📋 Danh sách bệnh nhân</a> | <a href="register_patient.php">➕ Tiếp nhận bệnh nhân</a></p>

    <table border="1" cellpadding="6" style="border-collapse: collapse; width: 800px;">
        <tr bgcolor="#eee">
            <th>Mã BS</th>
            <th>Họ và Tên Bác Sĩ</th>
            <th>Chuyên khoa</th>
            <th>Số toa đã kê</th>
            <th>Tổng tiền thuốc</th>
            <th>Ghi chú thi đua</th>
        </tr>
        <?php foreach ($report as $r): ?>
            <tr>
                <td><?= e($r['doctor_code']) ?></td>
                <td><?= e($r['doctor_name']) ?></td>
                <td><?= e($r['department']) ?></td>
                <td align="center"><?= (int)$r['total_prescriptions'] ?></td>
                <td align="right"><?= number_format($r['total_drug_cost'], 0, ',', '.') ?> đ</td>
                <td>
                    <?php if (in_array($r['id'], $topDoctorIds) && $r['total_drug_cost'] > 0): ?>
                        <span style="color: red; font-weight: bold;">⭐ Doanh thu thuốc cao nhất (Đồng hạng)</span>
                    <?php elseif ($r['total_prescriptions'] == 0): ?>
                        <span style="color: gray;">Chưa kê đơn nào</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
```
