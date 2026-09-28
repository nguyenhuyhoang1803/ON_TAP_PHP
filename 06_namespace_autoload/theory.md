# BÀI 06: NAMESPACE, AUTOLOAD & COMPOSER PSR-4

## A. Mục tiêu
Sau bài này, bạn có thể:
1. Hiểu bản chất `namespace` và từ khóa `use` để giải quyết xung đột tên class.
2. Tự viết hàm `spl_autoload_register()` chuyển đổi namespace thành đường dẫn file trên ổ cứng.
3. Cấu hình file `composer.json` chuẩn PSR-4 cho cấu trúc thư mục dự án thi.
4. Sử dụng thành thạo lệnh `composer dump-autoload` và nhúng `vendor/autoload.php`.
5. Hiểu nguyên lý Dependency Injection (truyền kết nối PDO vào Model qua Constructor).

---

## B. Bản chất
- **Tại sao cần Namespace?** Nếu bạn viết class `User` và cài thêm thư viện bên ngoài cũng có class `User`, PHP sẽ báo lỗi `Fatal Error: Cannot declare class User because the name is already in use`. Namespace giống như việc đặt các file cùng tên vào các thư mục khác nhau.
- **Autoload giải quyết vấn đề gì?** Nếu dự án có 50 class, bạn không thể viết 50 dòng `require_once 'models/User.php';`, `require_once 'services/Order.php';`... Hàm Autoload sẽ tự động được gọi khi bạn gõ `new User()`, tự tìm file và require vào bộ nhớ.
- **PSR-4 là gì?** Là chuẩn quy ước quốc tế: Tên namespace chính là cấu trúc thư mục trên ổ cứng. Ví dụ: `App\Models\User` tương ứng với file `src/Models/User.php`.

---

## C. Cú pháp cốt lõi

### 1. Khai báo Namespace & Import bằng `use`
```php
// File: src/Models/User.php
namespace App\Models;

class User {
    public function getRole(): string { return 'Admin'; }
}

// File: index.php
use App\Models\User;

$user = new User();
```

### 2. Tự viết Autoload với `spl_autoload_register`
```php
spl_autoload_register(function (string $className) {
    // Map namespace prefix "App\" vào thư mục "src/"
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/src/';
    $len = strlen($prefix);

    if (strncmp($prefix, $className, $len) !== 0) {
        return; // Không phải class thuộc namespace App\
    }

    $relativeClass = substr($className, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
```

### 3. File `composer.json` chuẩn PSR-4
```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```
*Lệnh tạo nạp classmap:*
```bash
composer dump-autoload
```

### 4. Dependency Injection qua Constructor
```php
namespace App\Repositories;
use PDO;

class UserRepository {
    public function __construct(private PDO $pdo) {}

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
```

---

## D. Ví dụ tối giản

```php
<?php
// Cấu trúc:
// src/Services/Math.php -> namespace App\Services; class Math { ... }
// index.php

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/' . str_replace(['App\\', '\\'], ['src/', '/'], $class) . '.php';
    if (file_exists($file)) require_once $file;
});

// Khi gọi:
// $m = new App\Services\Math(); -> PHP tự tìm và include src/Services/Math.php
```

---

## E. Luồng tư duy
```text
Gọi lệnh: new \App\Controllers\HomeController();
                  ↓
PHP nhận thấy class chưa được nạp vào RAM
                  ↓
Kích hoạt hàm spl_autoload_register() (hoặc Composer autoload)
                  ↓
Chuyển đổi dấu "\" trong namespace thành dấu "/"
Map "App\" -> "src/"
Đường dẫn file: "src/Controllers/HomeController.php"
                  ↓
file_exists() trả về true ?
   ├─ CÓ: require_once file đó và tiếp tục khởi tạo đối tượng
   └─ KHÔNG: Báo Fatal Error: Class not found
```

---

## F. Những lỗi hay gặp
1. **Quên lệnh `composer dump-autoload`:** Sau khi tạo thêm class mới hoặc sửa file `composer.json`, Composer chưa cập nhật danh bạ file.
2. **Sai chữ hoa/thường:** Namespace là `App\Models` nhưng folder trên máy lại là `src/models` (chữ m thường). Chạy trên Windows có thể được nhưng nộp lên máy chấm Linux sẽ crash 0 điểm.
3. **Quên dấu gạch chéo ngược `\`:** Khi gọi class toàn cục của PHP trong một file có namespace (ví dụ: `PDO`, `Exception`), phải viết `\PDO` hoặc khai báo `use PDO;` ở đầu file.

---

## G. Cách nhận dạng câu hỏi
- Đề có câu: *"Tổ chức dự án theo chuẩn PSR-4, sử dụng Composer để tự động nạp các class trong thư mục `src/`"* -> **Viết file `composer.json`, chạy `composer dump-autoload`, trong `index.php` nhúng `require_once 'vendor/autoload.php';`**.
- Đề có câu: *"Không dùng Composer, hãy tự viết hàm nạp tự động nạp các class thuộc namespace `MyProject\`"* -> **Dùng `spl_autoload_register()` kết hợp `str_replace('\\', '/', ...)`**.

---

## H. Mini challenge
> **Đề bài:** Tự xây dựng cơ chế Autoload không dùng Composer:
> 1. Tạo cấu trúc: Thư mục `src/Core/Database.php` và `src/Models/Product.php`.
> 2. Class `Database` (namespace `App\Core`) có hàm tĩnh `getConnection()` trả về đối tượng PDO giả lập.
> 3. Class `Product` (namespace `App\Models`) nhận `Database` để in ra danh sách sản phẩm.
> 4. Trong `index.php`: Viết `spl_autoload_register()`, `use App\Models\Product;` và gọi chạy thử thành công mà không có bất kỳ dòng `require` thủ công nào tới các class.  
> *(Lời giải có trong thư mục `solutions/mini_challenge_solution.php`). Tự làm trước khi xem!*
