# CHECKPOINT 06: NAMESPACE, AUTOLOAD & COMPOSER

> **Điều kiện vượt qua:** Đạt từ 75% trở lên (>= 7.5/10 điểm).

---

### PHẦN A – LÝ THUYẾT NHANH (5 câu x 0.6đ = 3.0đ)
1. Dòng khai báo `namespace` bắt buộc phải nằm ở vị trí nào trong file PHP?
2. Từ khóa `use App\Models\User as Member;` có tác dụng gì?
3. Trình bày cơ chế hoạt động của hàm `spl_autoload_register()`.
4. File `vendor/autoload.php` do công cụ nào tự động sinh ra và vai trò của nó trong file chạy chính là gì?
5. Tại sao khi tạo thêm một file class mới trong thư mục đã khai báo PSR-4 của Composer, ta nên chạy lại lệnh `composer dump-autoload`?

---

### PHẦN B – ĐỌC CODE & BẮT BUG (2 đoạn code x 1.0đ = 2.0đ)

#### Bug 1:
```php
<?php
namespace App\Controllers;

class UserController {
    public function create() {
        try {
            $pdo = new PDO("mysql:host=localhost", "root", "");
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
```
*Đoạn code trên bị lỗi Fatal Error gì khi chạy? Sửa lại thế nào?*

#### Bug 2:
```json
{
    "autoload": {
        "psr-4": {
            "App": "src"
        }
    }
}
```
*Chỉ ra 2 điểm sai cú pháp nghiêm trọng trong cấu hình PSR-4 của file `composer.json` trên.*

---

### PHẦN C – VIẾT CODE THỰC HÀNH (3.0đ)
Viết một đoạn script PHP hoàn chỉnh cài đặt `spl_autoload_register()` có khả năng:
- Ánh xạ prefix `Core\` vào thư mục `libs/Core/`.
- Ánh xạ prefix `App\` vào thư mục `src/`.
- Nếu file không tồn tại trên ổ cứng thì không báo lỗi require mà bỏ qua để các autoloader khác (nếu có) tiếp tục xử lý.

---

### PHẦN D – BIẾN THỂ ĐỀ THI (2.0đ)
Trong bài thi thực hành, nếu đề yêu cầu sử dụng mô hình MVC đơn giản kết hợp Dependency Injection: Controller nhận Repository qua constructor, Repository nhận đối tượng PDO qua constructor.  
Hãy viết đoạn code khởi tạo tại `index.php` để kết nối và chạy: Tạo PDO -> Truyền vào `ProductRepository` -> Truyền vào `ProductController` -> Gọi hàm `index()`.
