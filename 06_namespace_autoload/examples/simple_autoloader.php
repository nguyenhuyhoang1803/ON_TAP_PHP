<?php
// 06_namespace_autoload/examples/simple_autoloader.php

// Định nghĩa autoloader thuần túy
spl_autoload_register(function ($className) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/src/';
    $len = strlen($prefix);

    if (strncmp($prefix, $className, $len) !== 0) {
        return;
    }

    $relativeClass = substr($className, $len);
    $filePath = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($filePath)) {
        require_once $filePath;
    }
});

echo "Hàm Autoloader đã được đăng ký thành công!\n";
echo "Khi bạn gọi `new App\\Services\\Cart()`, PHP sẽ tự động tìm nạp file `src/Services/Cart.php`.\n";
