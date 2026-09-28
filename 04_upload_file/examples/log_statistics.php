<?php
// 04_upload_file/examples/log_statistics.php
session_start();

function e(?string $val): string {
    return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Chuyển đổi dung lượng bytes sang định dạng người đọc (B, KB, MB, GB)
 */
function formatBytes(float|int $bytes, int $precision = 2): string {
    if ($bytes < 0) return '0 B';
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

$logFile = __DIR__ . '/upload_records.log';

// Nếu chưa có file log, tự tạo dữ liệu mẫu thực tế để kiểm thử (kèm trường hợp đồng hạng Max)
if (!file_exists($logFile)) {
    $sampleLogs = [
        "2026-09-28 08:00:15 | user:hoangnam | file:cv_01.pdf | size:1540200",
        "2026-09-28 08:05:22 | user:thuthao  | file:avatar.png | size:450120",
        "2026-09-28 08:12:40 | user:hoangnam | file:report.pdf | size:2400500",
        "2026-09-28 08:20:11 | user:minhtuan | file:photo.jpg  | size:890300",
        "2026-09-28 08:25:33 | user:thuthao  | file:doc_02.pdf | size:1200300",
        "DÒNG_LỖI_FORMAT_KHÔNG_HỢP_LỆ_PHẢI_BỊ_BỎ_QUA",
        "2026-09-28 08:30:00 | user:quocbao  | file:data.csv   | size:51200",
    ];
    file_put_contents($logFile, implode("\n", $sampleLogs) . "\n", LOCK_EX);
}

// Giả lập user đang đăng nhập hiện tại
$currentUser = trim($_GET['user'] ?? 'hoangnam');

// Khởi tạo các biến thống kê
$totalFiles = 0;
$totalBytes = 0;
$userFileCounts = [];
$userByteCounts = [];
$corruptedLines = 0;
$logEntries = [];

if (file_exists($logFile) && filesize($logFile) > 0) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $lineNum => $line) {
        $line = trim($line);
        if ($line === '') continue;

        // Chuẩn định dạng: Timestamp | user:username | file:filename | size:bytes
        $parts = array_map('trim', explode('|', $line));

        if (count($parts) < 4) {
            $corruptedLines++;
            continue;
        }

        $timestamp = $parts[0];
        $userPart  = $parts[1]; // user:hoangnam
        $filePart  = $parts[2]; // file:cv_01.pdf
        $sizePart  = $parts[3]; // size:1540200

        // Parse key-value an toàn
        $username = '';
        if (str_starts_with($userPart, 'user:')) {
            $username = trim(substr($userPart, 5));
        }

        $fileName = '';
        if (str_starts_with($filePart, 'file:')) {
            $fileName = trim(substr($filePart, 5));
        }

        $fileSize = 0;
        if (str_starts_with($sizePart, 'size:')) {
            $fileSize = (float)trim(substr($sizePart, 5));
        }

        // Kiểm tra hợp lệ dữ liệu
        if ($username === '' || $fileName === '' || $fileSize < 0) {
            $corruptedLines++;
            continue;
        }

        // Tích lũy số liệu
        $totalFiles++;
        $totalBytes += $fileSize;
        $userFileCounts[$username] = ($userFileCounts[$username] ?? 0) + 1;
        $userByteCounts[$username] = ($userByteCounts[$username] ?? 0) + $fileSize;

        $logEntries[] = [
            'time' => $timestamp,
            'user' => $username,
            'file' => $fileName,
            'size' => $fileSize
        ];
    }
}

// Xử lý bài toán Top Uploader & Đồng Hạng (Ties)
$maxUploads = 0;
$topUsers = [];

if (!empty($userFileCounts)) {
    $maxUploads = max($userFileCounts);
    foreach ($userFileCounts as $u => $cnt) {
        if ($cnt === $maxUploads) {
            $topUsers[] = [
                'user'  => $u,
                'count' => $cnt,
                'bytes' => $userByteCounts[$u] ?? 0
            ];
        }
    }
}

// Số file của user hiện tại
$currentUserUploads = $userFileCounts[$currentUser] ?? 0;
$currentUserBytes   = $userByteCounts[$currentUser] ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thống Kê Nhật Ký Upload File</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.5; padding: 20px; }
        .card { border: 1px solid #ddd; border-radius: 6px; padding: 15px; margin-bottom: 15px; }
        .badge { background: #007bff; color: white; padding: 3px 8px; border-radius: 4px; font-size: 13px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>BÁO CÁO THỐNG KÊ FILE UPLOAD</h2>

    <div class="card">
        <h3>1. Tổng Quan Toàn Hệ Thống</h3>
        <ul>
            <li>Tổng số file đã upload hợp lệ: <strong><?= $totalFiles ?></strong> files</li>
            <li>Tổng dung lượng lưu trữ: <strong><?= formatBytes($totalBytes) ?></strong> (<?= number_format($totalBytes) ?> bytes)</li>
            <li>Số dòng log lỗi/không đúng format đã bỏ qua: <strong><?= $corruptedLines ?></strong> dòng</li>
        </ul>
    </div>

    <div class="card">
        <h3>2. Thống Kê Người Dùng Hiện Tại (User: <code><?= e($currentUser) ?></code>)</h3>
        <p>Số file của bạn: <strong><?= $currentUserUploads ?></strong> files | Tổng dung lượng: <strong><?= formatBytes($currentUserBytes) ?></strong></p>
        <form method="GET">
            Đổi user kiểm tra: 
            <input type="text" name="user" value="<?= e($currentUser) ?>">
            <button type="submit">Xem</button>
        </form>
    </div>

    <div class="card">
        <h3>3. Người Dùng Upload Nhiều Nhất (Có Xử Lý Đồng Hạng - Ties)</h3>
        <?php if (empty($topUsers)): ?>
            <p>Chưa có dữ liệu upload.</p>
        <?php else: ?>
            <p>Mức upload cao nhất: <strong><?= $maxUploads ?></strong> files</p>
            <ul>
                <?php foreach ($topUsers as $top): ?>
                    <li>
                        User: <strong><?= e($top['user']) ?></strong> 
                        <span class="badge">ĐỒNG HẠNG TOP</span>
                        - Đã tải <?= $top['count'] ?> files (<?= formatBytes($top['bytes']) ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>4. Thống Kê Chi Tiết Từng User</h3>
        <table>
            <thead>
                <tr><th>Username</th><th>Số lượng files</th><th>Tổng dung lượng</th></tr>
            </thead>
            <tbody>
                <?php foreach ($userFileCounts as $usr => $cnt): ?>
                    <tr>
                        <td><?= e($usr) ?></td>
                        <td><?= $cnt ?></td>
                        <td><?= formatBytes($userByteCounts[$usr] ?? 0) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
