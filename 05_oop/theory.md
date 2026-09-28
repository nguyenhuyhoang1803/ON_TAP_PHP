# BÀI 05: LẬP TRÌNH HƯỚNG ĐỐI TƯỢNG (OOP PHP)

## A. Mục tiêu
Sau bài này, bạn có thể:
1. Thiết kế Class, khởi tạo Đối tượng (Object), sử dụng Constructor Promotion (PHP 8).
2. Kiểm soát phạm vi truy cập: `public`, `protected`, `private`, viết Getter/Setter chuẩn.
3. Kế thừa (`extends`), ghi đè phương thức (`override`) và gọi hàm cha qua `parent::`.
4. Phân biệt chính xác Abstract Class và Interface; hiện thực hóa đa hình (Polymorphism).
5. Sử dụng toán tử kiểm tra kiểu `instanceof`, thuộc tính và phương thức tĩnh `static`.
6. Quản lý mảng các đối tượng, sắp xếp danh sách đối tượng bằng `usort()`.

---

## B. Bản chất
- **Abstract Class vs Interface:**
  - **Interface (Hợp đồng hành vi):** Chỉ khai báo tên hàm mà không có phần thân (`{...}`). Dùng khi các class không cùng họ hàng nhưng cần có chung hành động (ví dụ: `Bird` và `Airplane` đều implement `Flyable`). Dùng từ khóa `implements`.
  - **Abstract Class (Bản thiết kế dở dang):** Có thể chứa biến, hàm đã viết sẵn code, và cả hàm abstract bắt con phải tự viết. Dùng khi các class cùng họ hàng bản chất (ví dụ: `Dog` và `Cat` đều kế thừa `Animal`). Dùng từ khóa `extends`.
- **Đa hình (Polymorphism) giải quyết vấn đề gì?** Giúp bạn viết code tổng quát: Một danh sách chứa nhiều loại hình học (`Circle`, `Square`), bạn chỉ cần lặp qua và gọi `$shape->getArea()` mà không cần viết hàng loạt `if ($type == 'circle')...`.

---

## C. Cú pháp cốt lõi

### 1. Abstract Class & Kế thừa
```php
abstract class Employee {
    // Constructor Promotion (PHP 8)
    public function __construct(
        protected string $id,
        protected string $name,
        protected float $baseSalary
    ) {}

    // Phương thức abstract bắt buộc lớp con phải định nghĩa
    abstract public function calculateSalary(): float;

    public function getName(): string {
        return $this->name;
    }
}
```

### 2. Interface & Implements
```php
interface Taxable {
    public function calculateTax(): float;
}

class FullTimeEmployee extends Employee implements Taxable {
    public function __construct(
        string $id,
        string $name,
        float $baseSalary,
        private float $bonus
    ) {
        parent::__construct($id, $name, $baseSalary);
    }

    public function calculateSalary(): float {
        return $this->baseSalary + $this->bonus;
    }

    public function calculateTax(): float {
        return $this->calculateSalary() * 0.10; // Thuế 10%
    }
}
```

### 3. Sắp xếp mảng đối tượng với `usort()`
```php
// Sắp xếp danh sách nhân viên giảm dần theo lương thực lãnh
usort($employees, function (Employee $a, Employee $b) {
    return $b->calculateSalary() <=> $a->calculateSalary(); // Toán tử phi thuyền <=>
});
```

### 4. Static Property & Method
```php
class Counter {
    private static int $count = 0;
    public function __construct() { self::$count++; }
    public static function getCount(): int { return self::$count; }
}
```

---

## D. Ví dụ tối giản

```php
<?php
// oop_demo.php
abstract class Vehicle {
    public function __construct(protected string $brand) {}
    abstract public function getMaxSpeed(): int;
    public function getBrand(): string { return $this->brand; }
}

class Car extends Vehicle {
    public function getMaxSpeed(): int { return 180; }
}

class Bike extends Vehicle {
    public function getMaxSpeed(): int { return 40; }
}

$vehicles = [new Car('Toyota'), new Bike('Giant')];
foreach ($vehicles as $v) {
    echo $v->getBrand() . " - Tốc độ: " . $v->getMaxSpeed() . " km/h\n";
}
```

---

## E. Luồng tư duy
```text
Phân tích yêu cầu đề bài
       ↓
Có cần bắt buộc một nhóm hành vi chung không?
   ├─ CÓ và các class không cùng huyết thống: Dùng INTERFACE (implements)
   └─ CÓ và các class cùng bản chất một nhóm thực thể: Dùng ABSTRACT CLASS (extends)
       ↓
Tạo các Lớp con (Concrete Classes)
       ↓
Khởi tạo mảng các đối tượng (Array of Objects)
       ↓
Thực hiện tính toán / Lọc / Sắp xếp (usort với toán tử <=>)
       ↓
In kết quả (Gọi các phương thức đa hình $obj->method())
```

---

## F. Những lỗi hay gặp
1. **Viết thân hàm bên trong `interface`:** Interface chỉ được chứa chữ ký hàm `public function run(): void;`, không bao giờ có `{ ... }`.
2. **Quên gọi `parent::__construct(...)` trong lớp con:** Khiến các thuộc tính khởi tạo ở lớp cha bị `uninitialized`.
3. **Dùng nhầm `this->` cho biến tĩnh `static`:** Thuộc tính/phương thức tĩnh phải gọi qua `self::$bien` hoặc `TênLớp::$bien`, không dùng `$this->bien`.
4. **Nhầm chiều sắp xếp trong `usort()`:** `$a <=> $b` là TĂNG DẦN; `$b <=> $a` là GIẢM DẦN.

---

## G. Cách nhận dạng câu hỏi
- Đề có câu: *"Định nghĩa lớp trừu tượng Sản phẩm gồm mã, tên, giá; hai lớp con Sách và Điện tử có công thức tính thuế khác nhau"* -> **`abstract class Product` + `abstract public function getTax(): float;`**.
- Đề có câu: *"Sắp xếp danh sách đối tượng giảm dần theo thuộc tính X"* -> **Dùng hàm `usort($list, fn($a, $b) => $b->getX() <=> $a->getX());`**.

---

## H. Mini challenge
> **Đề bài:** Thiết kế hệ thống tính lương:
> 1. Interface `BonusCalculable`: Có phương thức `public function getBonus(): float;`.
> 2. Abstract class `Person`: Thuộc tính `name` (string). Có constructor và hàm `getName(): string`.
> 3. Lớp con `Manager` kế thừa `Person` và implement `BonusCalculable`:
>    - Thuộc tính riêng: `baseSalary` (float), `kpiScore` (float từ 0 đến 1.0).
>    - `getBonus()`: Nếu `kpiScore >= 0.8`, thưởng bằng `baseSalary * 0.3`, ngược lại thưởng `0`.
>    - Phương thức `getTotalIncome()` = `baseSalary + getBonus()`.
> 4. Tạo mảng gồm 3 Manager, dùng `usort` sắp xếp giảm dần theo tổng thu nhập `getTotalIncome()` và in ra màn hình.  
> *(Lời giải có trong thư mục `solutions/mini_challenge_solution.php`). Tự làm trước khi xem!*
