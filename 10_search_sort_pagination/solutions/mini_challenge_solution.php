<?php
// 10_search_sort_pagination/solutions/mini_challenge_solution.php

if (!function_exists('e')) {
    function e(?string $v): string {
        return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// 1. KẾT NỐI PDO (Hỗ trợ MySQL máy chủ thi, tự động fallback SQLite nếu máy chưa bật DB)
$pdo = null;
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=test;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (Throwable $e) {
    // Fallback SQLite in-memory để chạy kiểm thử tức thì không phụ thuộc trạng thái service MySQL
    $pdo = new PDO("sqlite::memory:", null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

// Khởi tạo bảng và dữ liệu mẫu nếu chưa có
$pdo->exec("CREATE TABLE IF NOT EXISTS staff (
    id INTEGER PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    dept_id INTEGER NOT NULL,
    salary DECIMAL(12,2) NOT NULL
)");

$checkCount = (int)$pdo->query("SELECT COUNT(*) FROM staff")->fetchColumn();
if ($checkCount === 0) {
    $insertStmt = $pdo->prepare("INSERT INTO staff (id, name, phone, dept_id, salary) VALUES (:id, :name, :phone, :dept_id, :salary)");
    $seed = [
        [1, 'Nguyễn Văn An', '0901234567', 1, 12000000],
        [2, 'Trần Thị Bình', '0912345678', 2, 15000000],
        [3, 'Lê Quốc Cường', '0988776655', 1, 9500000],
        [4, 'Phạm Thùy Dung', '0933445566', 3, 22000000],
        [5, 'Vũ Hoàng Giang', '0977112233', 2, 18000000],
        [6, 'Đỗ Minh Hải', '0966998877', 1, 11000000],
        [7, 'Bùi Khánh Linh', '0944556677', 3, 14000000],
        [8, 'Patrick O\'Connor', '0911223344', 1, 25000000],
        [9, 'Ngô Gia Huy_Special', '0922334455', 2, 13500000],
        [10, 'Hoàng Yến 100% PHP', '0933557799', 3, 17500000],
        [11, 'Đinh Trọng Nam', '0988112233', 1, 10500000],
        [12, 'Lương Bích Hữu', '0977441122', 2, 16000000],
    ];
    foreach ($seed as $row) {
        $insertStmt->execute([
            ':id'      => $row[0],
            ':name'    => $row[1],
            ':phone'   => $row[2],
            ':dept_id' => $row[3],
            ':salary'  => $row[4],
        ]);
    }
}

// 2. NHẬN VÀ LÀM SẠCH THAM SỐ (INPUT NORMALIZATION)
$keyword = trim($_GET['kw'] ?? '');
$deptId  = filter_var($_GET['dept_id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;

// Whitelist cột sắp xếp và chiều sắp xếp
$sortWhitelist = ['id' => 'id', 'name' => 'name', 'salary' => 'salary'];
$sort = $_GET['sort'] ?? 'id';
$sortCol = $sortWhitelist[$sort] ?? 'id';

$dirInput = strtoupper($_GET['dir'] ?? 'DESC');
$sortDir = ($dirInput === 'ASC') ? 'ASC' : 'DESC';

// Clamp limit trong khoảng 5 - 50
$rawLimit = filter_var($_GET['limit'] ?? 5, FILTER_VALIDATE_INT);
$limit = ($rawLimit === false) ? 5 : max(5, min(50, $rawLimit));

// 3. XÂY DỰNG MỆNH ĐỀ WHERE VÀ THAM SỐ DÙNG CHUNG
$whereClauses = ["1=1"];
$params = [];

if ($keyword !== '') {
    // Escape ký tự % và _ để tìm chính xác
    $escaped = addcslashes($keyword, '%_');
    $whereClauses[] = "(name LIKE :kw ESCAPE '\\' OR phone LIKE :kw ESCAPE '\\')";
    $params[':kw'] = '%' . $escaped . '%';
}

if ($deptId > 0) {
    $whereClauses[] = "dept_id = :dept_id";
    $params[':dept_id'] = $deptId;
}

$whereSql = "WHERE " . implode(" AND ", $whereClauses);

// 4. TRUY VẤN ĐẾM TỔNG SỐ BẢN GHI (COUNT QUERY)
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM staff $whereSql");
$countStmt->execute($params);
$totalRows = (int)$countStmt->fetchColumn();

// Tính tổng số trang (ít nhất là 1)
$totalPages = max(1, (int)ceil($totalRows / $limit));

// Validate và kẹp dải trang hợp lệ (Clamp page)
$rawPage = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
$page = ($rawPage === false || $rawPage < 1) ? 1 : min($rawPage, $totalPages);

// Tính OFFSET chuẩn xác
$offset = ($page - 1) * $limit;

// 5. TRUY VẤN DỮ LIỆU TRANG HIỆN TẠI VỚI LIMIT & OFFSET (DATA QUERY)
$sql = "SELECT id, name, phone, dept_id, salary 
        FROM staff 
        $whereSql 
        ORDER BY $sortCol $sortDir 
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hàm tạo URL giữ nguyên các query parameters khác và loại bỏ param rỗng
if (!function_exists('buildUrl')) {
    function buildUrl(array $overrides = []): string {
        $current = $_GET;
        foreach ($overrides as $k => $v) {
            $current[$k] = $v;
        }
        // Lọc bỏ param rỗng
        $filtered = array_filter($current, fn($val) => $val !== '' && $val !== null);
        return '?' . http_build_query($filtered);
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phân Trang PDO Chuẩn Phòng Thi</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.5; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
        .pagination { margin-top: 20px; display: flex; gap: 5px; align-items: center; }
        .pagination a, .pagination span { padding: 6px 12px; border: 1px solid #ddd; text-decoration: none; color: #333; }
        .pagination a:hover { background: #f0f0f0; }
        .pagination .active { background: #007bff; color: white; border-color: #007bff; font-weight: bold; }
    </style>
</head>
<body>
    <h2>DANH SÁCH NHÂN VIÊN (PHÂN TRANG PDO CHUẨN)</h2>

    <form method="GET" action="">
        Từ khóa: 
        <input type="text" name="kw" value="<?= e($keyword) ?>" placeholder="Tên hoặc SĐT...">
        
        Phòng ban:
        <select name="dept_id">
            <option value="0">-- Tất cả phòng ban --</option>
            <option value="1" <?= $deptId === 1 ? 'selected' : '' ?>>Kỹ thuật (1)</option>
            <option value="2" <?= $deptId === 2 ? 'selected' : '' ?>>Kinh doanh (2)</option>
            <option value="3" <?= $deptId === 3 ? 'selected' : '' ?>>Nhân sự (3)</option>
        </select>

        Số dòng/trang:
        <select name="limit">
            <option value="5" <?= $limit === 5 ? 'selected' : '' ?>>5</option>
            <option value="10" <?= $limit === 10 ? 'selected' : '' ?>>10</option>
            <option value="20" <?= $limit === 20 ? 'selected' : '' ?>>20</option>
        </select>

        <button type="submit">Tìm kiếm & Lọc</button>
        <a href="<?= $_SERVER['PHP_SELF'] ?>">Đặt lại</a>
    </form>

    <p style="margin-top: 15px;">
        Đang xem trang <strong><?= $page ?></strong> / <strong><?= $totalPages ?></strong> 
        (Tổng số: <strong><?= $totalRows ?></strong> nhân viên, hiển thị từ dòng <?= $totalRows > 0 ? $offset + 1 : 0 ?> đến <?= min($offset + $limit, $totalRows) ?>)
    </p>

    <table>
        <thead>
            <tr>
                <th><a href="<?= buildUrl(['sort' => 'id', 'dir' => ($sortCol === 'id' && $sortDir === 'ASC') ? 'DESC' : 'ASC']) ?>">ID</a></th>
                <th><a href="<?= buildUrl(['sort' => 'name', 'dir' => ($sortCol === 'name' && $sortDir === 'ASC') ? 'DESC' : 'ASC']) ?>">Họ và tên</a></th>
                <th>Số điện thoại</th>
                <th>Phòng ban</th>
                <th><a href="<?= buildUrl(['sort' => 'salary', 'dir' => ($sortCol === 'salary' && $sortDir === 'ASC') ? 'DESC' : 'ASC']) ?>">Mức lương</a></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr><td colspan="5" style="text-align: center; color: red;">Không tìm thấy nhân viên nào phù hợp!</td></tr>
            <?php else: ?>
                <?php foreach ($items as $row): ?>
                    <tr>
                        <td><?= (int)$row['id'] ?></td>
                        <td><?= e($row['name']) ?></td>
                        <td><?= e($row['phone']) ?></td>
                        <td>Phòng <?= (int)$row['dept_id'] ?></td>
                        <td><?= number_format((float)$row['salary'], 0, ',', '.') ?> VNĐ</td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="<?= buildUrl(['page' => $page - 1]) ?>">« Trước</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i === $page): ?>
                <span class="active"><?= $i ?></span>
            <?php else: ?>
                <a href="<?= buildUrl(['page' => $i]) ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="<?= buildUrl(['page' => $page + 1]) ?>">Sau »</a>
        <?php endif; ?>
    </div>
</body>
</html>
