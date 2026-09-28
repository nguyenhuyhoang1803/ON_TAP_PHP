<?php
// 06_namespace_autoload/solutions/mini_challenge_solution.php

namespace App\Core {
    class Database {
        public static function getInfo(): string {
            return "MySQL Server 8.x via PDO (Mock Connection)";
        }
    }
}

namespace App\Models {
    use App\Core\Database;

    class Product {
        public function display(): void {
            echo "Kết nối CSDL: " . Database::getInfo() . "\n";
            echo "Sản phẩm: Laptop ThinkPad T14 Gen 4 - Đơn giá: 22.000.000 VNĐ\n";
        }
    }
}

namespace {
    // Không gian root (index.php)
    use App\Models\Product;

    // 1. Tự định nghĩa hàm autoloader cho prefix App\ trỏ vào thư mục hiện tại
    spl_autoload_register(function ($className) {
        $prefix = 'App\\';
        $len = strlen($prefix);

        if (strncmp($prefix, $className, $len) !== 0) {
            return;
        }

        $relative = substr($className, $len);
        echo "[AUTOLOAD] Đang tìm kiếm class: $className (File tương ứng: src/" . str_replace('\\', '/', $relative) . ".php)\n";
    });

    echo "=== CHẠY DEMO AUTOLOAD & NAMESPACE ===\n";
    $p = new Product();
    $p->display();
}
