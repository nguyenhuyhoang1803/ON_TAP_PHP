<?php
/**
 * SCRIPT TỰ ĐỘNG KIỂM TRA MÔI TRƯỜNG PHP
 * Môn: Lập trình mã nguồn mở
 */

echo "========================================================\n";
echo "   KIỂM TRA CẤU HÌNH PHP PHÒNG THI (PHP ENVIRONMENT CHECK)\n";
echo "========================================================\n\n";

$allPassed = true;

// 1. Kiểm tra phiên bản PHP
$phpVersion = PHP_VERSION;
echo "[1] Phiên bản PHP hiện tại: $phpVersion\n";
if (version_compare($phpVersion, '8.0.0', '>=')) {
    echo "    ==> [PASS] Phiên bản PHP >= 8.0, hỗ trợ đầy đủ tính năng hiện đại.\n";
} else {
    echo "    ==> [FAIL] Phiên bản PHP quá cũ (< 8.0). Cần cập nhật PHP trên Laragon!\n";
    $allPassed = false;
}

// 2. Kiểm tra các Extensions bắt buộc
echo "\n[2] Kiểm tra các PHP Extensions bắt buộc:\n";
$requiredExtensions = [
    'pdo'        => 'Thư viện PDO kết nối CSDL',
    'pdo_mysql'  => 'Driver PDO cho MySQL',
    'mbstring'   => 'Xử lý chuỗi Unicode tiếng Việt',
    'session'    => 'Quản lý phiên làm việc & Auth Guard',
    'json'       => 'Xử lý JSON cho Fetch API / AJAX',
    'fileinfo'   => 'Kiểm tra MIME type an toàn khi upload file'
];

foreach ($requiredExtensions as $ext => $desc) {
    if (extension_loaded($ext)) {
        echo "    - Extension '$ext' ($desc): [PASS]\n";
    } else {
        echo "    - Extension '$ext' ($desc): [FAIL] -> Thiếu extension này trong php.ini!\n";
        $allPassed = false;
    }
}

// 3. Kiểm tra cấu hình Upload File
echo "\n[3] Kiểm tra cấu hình Upload File:\n";
$uploadMax = ini_get('upload_max_filesize');
$postMax   = ini_get('post_max_size');
echo "    - upload_max_filesize: $uploadMax\n";
echo "    - post_max_size: $postMax\n";
echo "    ==> [INFO] Đảm bảo upload_max_filesize <= post_max_size.\n";

// 4. Kiểm tra Composer
echo "\n[4] Kiểm tra trình quản lý Composer:\n";
$composerPaths = [
    'C:\\laragon\\bin\\composer\\composer.phar',
    'C:\\laragon\\bin\\composer\\composer.bat',
];
$foundComposer = false;
foreach ($composerPaths as $cp) {
    if (file_exists($cp)) {
        echo "    ==> [PASS] Đã phát hiện Composer tại: $cp\n";
        $foundComposer = true;
        break;
    }
}
if (!$foundComposer) {
    echo "    ==> [WARN] Không tìm thấy Composer mặc định của Laragon. Bạn có thể cần chạy qua phar hoặc add vào PATH.\n";
}

echo "\n========================================================\n";
if ($allPassed) {
    echo " KẾT LUẬN: MÔI TRƯỜNG PHP HOÀN TOÀN ĐẠT CHUẨN ĐỂ LÀM BÀI THI!\n";
} else {
    echo " KẾT LUẬN: CÓ MỘT SỐ VẤN ĐỀ CẦN CẤU HÌNH LẠI TRÊN LARAGON!\n";
}
echo "========================================================\n";
