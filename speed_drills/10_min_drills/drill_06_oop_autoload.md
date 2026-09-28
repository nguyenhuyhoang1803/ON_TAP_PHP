# SPEED DRILL 10M-06: OOP CLASS HIERARCHY & SPL_AUTOLOAD
> **Thời gian tối đa:** 10 phút | **Mục tiêu:** Viết cấu trúc lớp hướng đối tượng hoàn chỉnh và tự xây dựng cơ chế nạp tự động không cần Composer.

---

## 1. ĐỀ BÀI
1. Tạo thư mục `classes/` chứa file `PaymentGateway.php`:
   - Namespace `App\Payments;`
   - `interface PaymentGateway` có phương thức `public function process(float $amount): bool;`.
2. Tạo file `MomoPayment.php` trong `classes/`:
   - Namespace `App\Payments;`
   - `class MomoPayment implements PaymentGateway` có thuộc tính `$phoneNumber`.
   - Triển khai `process(float $amount)` in ra: `"Thanh toán thành công [amount] đ qua MoMo số [phone]"`.
3. Tạo file `index.php`:
   - Đăng ký hàm nạp tự động bằng `spl_autoload_register` ánh xạ `App\Payments\` vào thư mục `classes/`.
   - `use App\Payments\MomoPayment;`
   - Khởi tạo đối tượng và gọi `process(250000)`.

---

## 2. LỜI GIẢI MẪU (SOLUTION)

**File `classes/PaymentGateway.php`:**
```php
<?php
namespace App\Payments;

interface PaymentGateway {
    public function process(float $amount): bool;
}
```

**File `classes/MomoPayment.php`:**
```php
<?php
namespace App\Payments;

class MomoPayment implements PaymentGateway {
    private string $phoneNumber;

    public function __construct(string $phoneNumber) {
        $this->phoneNumber = $phoneNumber;
    }

    public function process(float $amount): bool {
        echo "Thanh toán thành công " . number_format($amount, 0, ',', '.') . " đ qua MoMo số " . htmlspecialchars($this->phoneNumber) . "<br>";
        return true;
    }
}
```

**File `index.php`:**
```php
<?php
spl_autoload_register(function ($class) {
    $prefix = 'App\\Payments\\';
    $baseDir = __DIR__ . '/classes/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Payments\MomoPayment;

$payment = new MomoPayment('0987654321');
$payment->process(250000);
```
