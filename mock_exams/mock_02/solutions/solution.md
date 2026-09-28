# ĐÁP ÁN HOÀN CHỈNH: MOCK EXAM 02

### 1. `db.php`
```php
<?php
$host = '127.0.0.1';
$db   = 'mock02_educenter';
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

### 2. `login.php` & `logout.php`
```php
<?php
// login.php
session_start();
if (!empty($_SESSION['admin_user'])) {
    header('Location: students.php');
    exit;
}

$error = '';
$savedUser = $_COOKIE['remember_admin'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = trim($_POST['password'] ?? '');
    $remember = isset($_POST['remember_me']);

    if ($u === 'admin' && $p === 'admin@2026') {
        $_SESSION['admin_user'] = $u;
        $_SESSION['flash'] = "Đăng nhập thành công! Chào mừng Quản trị viên.";

        if ($remember) {
            setcookie('remember_admin', $u, time() + 7 * 86400, '/');
        } else {
            setcookie('remember_admin', '', time() - 3600, '/');
        }

        header('Location: students.php');
        exit;
    } else {
        $error = "Tài khoản hoặc mật khẩu không chính xác!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Đăng Nhập EduCenter</title></head>
<body>
    <h2>ĐĂNG NHẬP HỆ THỐNG QUẢN TRỊ</h2>
    <?php if ($error): ?><p style="color:red"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <?php if (($_GET['err'] ?? '') === 'unauthorized'): ?>
        <p style="color:red">Vui lòng đăng nhập trước khi truy cập!</p>
    <?php endif; ?>

    <form method="POST" action="">
        <div>
            Tài khoản: <input type="text" name="username" value="<?= htmlspecialchars($savedUser) ?>">
        </div><br>
        <div>
            Mật khẩu: <input type="password" name="password">
        </div><br>
        <div>
            <label><input type="checkbox" name="remember_me" <?= $savedUser ? 'checked' : '' ?>> Ghi nhớ tài khoản (7 ngày)</label>
        </div><br>
        <button type="submit">Đăng Nhập</button>
    </form>
</body>
</html>
```

```php
<?php
// logout.php
session_start();
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain']);
}
session_destroy();
header('Location: login.php?msg=logged_out');
exit;
```

---

### 3. `students.php` (Auth Guard, Search, Whitelist Sort, Pagination)
```php
<?php
session_start();
require_once 'db.php';

// 1. AUTH GUARD
if (empty($_SESSION['admin_user'])) {
    header('Location: login.php?err=unauthorized');
    exit;
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// 2. PARAMS & SANITIZE
$kw    = trim($_GET['kw'] ?? '');
$sort  = $_GET['sort'] ?? 'id';
$dir   = strtoupper($_GET['dir'] ?? 'DESC');
$page  = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT) ?: 1;
$limit = 5;

// Escape LIKE
$escapedKw = strtr($kw, ['%' => '\%', '_' => '\_']);

// 3. WHERE DYNAMIC
$where = ["1=1"];
$params = [];
if ($escapedKw !== '') {
    $where[] = "(s.fullname LIKE :kw OR s.email LIKE :kw)";
    $params['kw'] = "%$escapedKw%";
}
$whereSql = implode(' AND ', $where);

// 4. TOTAL ROWS & PAGES
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM students s WHERE $whereSql");
$countStmt->execute($params);
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalRows / $limit));

// Clamp page
$page = max(1, min($page, $totalPages));
$offset = ($page - 1) * $limit;

// 5. WHITELIST SORT
$allowedCols = ['id' => 's.id', 'fullname' => 's.fullname', 'created_at' => 's.created_at'];
$sortCol = $allowedCols[$sort] ?? 's.id';
$sortDir = ($dir === 'ASC') ? 'ASC' : 'DESC';

// 6. QUERY DATA
$sql = "SELECT s.*, c.title AS course_title 
        FROM students s 
        LEFT JOIN courses c ON s.course_id = c.id 
        WHERE $whereSql 
        ORDER BY $sortCol $sortDir 
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue(":$k", $v);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$students = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Quản Lý Học Viên</title></head>
<body>
    <?php if ($flash): ?><p style="color:green; font-weight:bold;"><?= e($flash) ?></p><?php endif; ?>
    <?php if (($_GET['msg'] ?? '') === 'added'): ?><p style="color:green; font-weight:bold;">Thêm học viên mới thành công!</p><?php endif; ?>

    <h2>DANH SÁCH HỌC VIÊN (EduCenter)</h2>
    <p>
        Xin chào: <strong><?= e($_SESSION['admin_user']) ?></strong> | 
        <a href="add_student.php">➕ Thêm học viên</a> | 
        <a href="report.php">📊 Báo cáo khóa học</a> | 
        <a href="logout.php">Đăng xuất</a>
    </p>

    <!-- FORM TÌM KIẾM & SẮP XẾP -->
    <form method="GET" action="">
        Tìm kiếm: <input type="text" name="kw" value="<?= e($kw) ?>" placeholder="Tên hoặc email...">
        Sắp xếp theo:
        <select name="sort">
            <option value="id" <?= $sort === 'id' ? 'selected' : '' ?>>Mã ID</option>
            <option value="fullname" <?= $sort === 'fullname' ? 'selected' : '' ?>>Họ và tên</option>
            <option value="created_at" <?= $sort === 'created_at' ? 'selected' : '' ?>>Ngày đăng ký</option>
        </select>
        <select name="dir">
            <option value="DESC" <?= $sortDir === 'DESC' ? 'selected' : '' ?>>Mới nhất / Z-A</option>
            <option value="ASC" <?= $sortDir === 'ASC' ? 'selected' : '' ?>>Cũ nhất / A-Z</option>
        </select>
        <button type="submit">Lọc</button>
        <?php if ($kw !== ''): ?><a href="students.php">Bỏ lọc</a><?php endif; ?>
    </form>
    <br>

    <p>Tổng số: <strong><?= $totalRows ?></strong> học viên (Trang <strong><?= $page ?></strong> / <strong><?= $totalPages ?></strong>)</p>

    <table border="1" cellpadding="6" style="border-collapse: collapse; width: 850px;">
        <tr bgcolor="#eee">
            <th>ID</th>
            <th>Họ và Tên</th>
            <th>Email</th>
            <th>Số điện thoại</th>
            <th>Khóa học</th>
            <th>Ngày tạo</th>
        </tr>
        <?php if (empty($students)): ?>
            <tr><td colspan="6" align="center">Không tìm thấy học viên nào!</td></tr>
        <?php else: ?>
            <?php foreach ($students as $st): ?>
                <tr>
                    <td><?= $st['id'] ?></td>
                    <td><?= e($st['fullname']) ?></td>
                    <td><?= e($st['email']) ?></td>
                    <td><?= e($st['phone']) ?></td>
                    <td><?= e($st['course_title'] ?? 'Chưa phân lớp') ?></td>
                    <td><?= $st['created_at'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <!-- PHÂN TRANG GIỮ NGUYÊN QUERY PARAMETERS -->
    <div style="margin-top: 15px;">
        Trang:
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <?php $link = '?' . http_build_query(array_merge($_GET, ['page' => $p])); ?>
            <a href="<?= e($link) ?>" style="margin-right: 6px; <?= $p === $page ? 'font-weight:bold; color:red;' : '' ?>">[<?= $p ?>]</a>
        <?php endfor; ?>
    </div>
</body>
</html>
```

---

### 4. `add_student.php` (Validation, Bắt trùng email/SĐT, PRG)
```php
<?php
session_start();
require_once 'db.php';

if (empty($_SESSION['admin_user'])) {
    header('Location: login.php?err=unauthorized');
    exit;
}

$courses = $pdo->query("SELECT * FROM courses ORDER BY title ASC")->fetchAll();

$errors   = [];
$fullname = '';
$email    = '';
$phone    = '';
$courseId = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $courseId = trim($_POST['course_id'] ?? '');

    if ($fullname === '') {
        $errors['fullname'] = 'Họ và tên không được để trống.';
    }

    if ($email === '') {
        $errors['email'] = 'Email không được để trống.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email không đúng định dạng.';
    }

    if ($phone === '') {
        $errors['phone'] = 'Số điện thoại không được để trống.';
    } elseif (!preg_match('/^0[0-9]{9}$/', $phone)) {
        $errors['phone'] = 'Số điện thoại phải gồm đúng 10 chữ số và bắt đầu bằng số 0.';
    }

    if ($courseId === '') {
        $errors['course_id'] = 'Vui lòng chọn khóa học.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO students (fullname, email, phone, course_id) 
                                   VALUES (:fn, :e, :p, :cid)");
            $stmt->execute([
                'fn'  => $fullname,
                'e'   => $email,
                'p'   => $phone,
                'cid' => (int)$courseId,
            ]);

            header('Location: students.php?msg=added');
            exit;
        } catch (PDOException $ex) {
            if ($ex->getCode() == 23000) {
                $errors['general'] = "Email [{$email}] hoặc Số điện thoại [{$phone}] đã tồn tại trong hệ thống!";
            } else {
                error_log($ex->getMessage());
                $errors['general'] = "Lỗi khi lưu dữ liệu vào CSDL.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Thêm Học Viên Mới</title></head>
<body>
    <h2>THÊM HỌC VIÊN MỚI</h2>
    <?php if (isset($errors['general'])): ?><p style="color:red; font-weight:bold;"><?= e($errors['general']) ?></p><?php endif; ?>

    <form method="POST" action="">
        <div>
            Họ và tên (*):<br>
            <input type="text" name="fullname" value="<?= e($fullname) ?>">
            <?php if (isset($errors['fullname'])): ?><span style="color:red"><?= e($errors['fullname']) ?></span><?php endif; ?>
        </div><br>

        <div>
            Email (*):<br>
            <input type="text" name="email" value="<?= e($email) ?>">
            <?php if (isset($errors['email'])): ?><span style="color:red"><?= e($errors['email']) ?></span><?php endif; ?>
        </div><br>

        <div>
            Số điện thoại (*):<br>
            <input type="text" name="phone" value="<?= e($phone) ?>" placeholder="0901234567">
            <?php if (isset($errors['phone'])): ?><span style="color:red"><?= e($errors['phone']) ?></span><?php endif; ?>
        </div><br>

        <div>
            Khóa học đăng ký (*):<br>
            <select name="course_id">
                <option value="">-- Chọn khóa học --</option>
                <?php foreach ($courses as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($courseId == $c['id']) ? 'selected' : '' ?>>
                        <?= e($c['title']) ?> (<?= number_format($c['tuition_fee'], 0, ',', '.') ?> đ)
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['course_id'])): ?><span style="color:red"><?= e($errors['course_id']) ?></span><?php endif; ?>
        </div><br>

        <button type="submit">Lưu Học Viên</button>
        <a href="students.php">Quay lại danh sách</a>
    </form>
</body>
</html>
```

---

### 5. `report.php` (Báo Cáo SQL & LEFT JOIN)
```php
<?php
session_start();
require_once 'db.php';

if (empty($_SESSION['admin_user'])) {
    header('Location: login.php?err=unauthorized');
    exit;
}

// Câu truy vấn thống kê tất cả các khóa học, kể cả khóa chưa có học viên nào
$sql = "SELECT 
            c.code AS course_code,
            c.title AS course_title,
            c.tuition_fee,
            COUNT(s.id) AS total_students,
            COALESCE(SUM(c.tuition_fee), 0) AS estimated_revenue
        FROM courses c
        LEFT JOIN students s ON c.id = s.course_id
        GROUP BY c.id, c.code, c.title, c.tuition_fee
        ORDER BY total_students DESC";

$reportData = $pdo->query($sql)->fetchAll();

// Tìm khóa học đông nhất
$maxStudents = !empty($reportData) ? $reportData[0]['total_students'] : 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Báo Cáo Khóa Học</title></head>
<body>
    <h2>BÁO CÁO THỐNG KÊ KHÓA HỌC</h2>
    <p><a href="students.php">⬅ Quay lại danh sách học viên</a></p>

    <table border="1" cellpadding="6" style="border-collapse: collapse; width: 750px;">
        <tr bgcolor="#eee">
            <th>Mã khóa</th>
            <th>Tên khóa học</th>
            <th>Học phí</th>
            <th>Số học viên</th>
            <th>Ghi chú</th>
        </tr>
        <?php foreach ($reportData as $row): ?>
            <tr>
                <td><?= e($row['course_code']) ?></td>
                <td><?= e($row['course_title']) ?></td>
                <td align="right"><?= number_format($row['tuition_fee'], 0, ',', '.') ?> đ</td>
                <td align="center"><strong><?= (int)$row['total_students'] ?></strong></td>
                <td>
                    <?php if ($row['total_students'] == $maxStudents && $maxStudents > 0): ?>
                        <span style="color: green; font-weight: bold;">⭐ Đông nhất</span>
                    <?php elseif ($row['total_students'] == 0): ?>
                        <span style="color: red;">Chưa có học viên</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
```
