<?php
// 10_search_sort_pagination/examples/pagination_demo.php
$allProducts = [];
for ($i = 1; $i <= 25; $i++) {
    $allProducts[] = [
        'id'    => $i,
        'code'  => sprintf('SP%03d', $i),
        'name'  => "Sản phẩm công nghệ mẫu số " . $i,
        'price' => rand(10, 200) * 100000,
    ];
}

$kw    = trim($_GET['kw'] ?? '');
$sort  = $_GET['sort'] ?? 'id';
$dir   = strtoupper($_GET['dir'] ?? 'ASC');
$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = 6;

$filtered = array_filter($allProducts, function ($item) use ($kw) {
    if ($kw === '') return true;
    return str_contains(mb_strtolower($item['name']), mb_strtolower($kw)) ||
           str_contains(mb_strtolower($item['code']), mb_strtolower($kw));
});

$allowedSort = ['id', 'price', 'name'];
$sortKey = in_array($sort, $allowedSort, true) ? $sort : 'id';
$isDesc = ($dir === 'DESC');

usort($filtered, function ($a, $b) use ($sortKey, $isDesc) {
    $res = $a[$sortKey] <=> $b[$sortKey];
    return $isDesc ? -$res : $res;
});

$totalRows  = count($filtered);
$totalPages = max(1, (int)ceil($totalRows / $limit));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $limit;
$pageItems  = array_slice($filtered, $offset, $limit);
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Phân Trang & Lọc</title></head>
<body>
    <h2>Danh Sách Sản Phẩm (Phân Trang & Sắp Xếp)</h2>
    <form method="GET" action="">
        Từ khóa: <input type="text" name="kw" value="<?= htmlspecialchars($kw) ?>">
        Sắp xếp theo:
        <select name="sort">
            <option value="id" <?= $sortKey === 'id' ? 'selected' : '' ?>>Mã ID</option>
            <option value="price" <?= $sortKey === 'price' ? 'selected' : '' ?>>Giá bán</option>
            <option value="name" <?= $sortKey === 'name' ? 'selected' : '' ?>>Tên</option>
        </select>
        Chiều:
        <select name="dir">
            <option value="ASC" <?= !$isDesc ? 'selected' : '' ?>>Tăng dần</option>
            <option value="DESC" <?= $isDesc ? 'selected' : '' ?>>Giảm dần</option>
        </select>
        <button type="submit">Lọc</button>
    </form>

    <p>Hiển thị <?= count($pageItems) ?> / <?= $totalRows ?> sản phẩm (Trang <?= $page ?> / <?= $totalPages ?>)</p>

    <table border="1" cellpadding="6" style="border-collapse: collapse; width: 600px;">
        <tr><th>ID</th><th>Mã</th><th>Tên sản phẩm</th><th>Giá bán</th></tr>
        <?php foreach ($pageItems as $row): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['code']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= number_format($row['price'], 0, ',', '.') ?> đ</td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div style="margin-top: 15px;">
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <?php $url = '?' . http_build_query(array_merge($_GET, ['page' => $p])); ?>
            <a href="<?= htmlspecialchars($url) ?>" style="margin-right: 8px; font-weight: <?= $p === $page ? 'bold' : 'normal' ?>;">[<?= $p ?>]</a>
        <?php endfor; ?>
    </div>
</body>
</html>
