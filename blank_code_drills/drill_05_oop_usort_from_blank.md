# BLANK-05: OOP ĐA HÌNH VÀ SẮP XẾP USORT TỪ FILE TRẮNG
> **Thời gian:** 8 phút | **Yêu cầu:** Tự định nghĩa Abstract Class, kế thừa 2 lớp con, tạo mảng và sắp xếp giảm dần.

---

## 1. YÊU CẦU BÀI TẬP
Tạo file `staff_payroll.php` từ file trống:
1. `abstract class Staff`:
   - Thuộc tính `$name`, `$baseSalary`.
   - Method abstract: `abstract public function calculateSalary(): float;`.
2. `class OfficeStaff extends Staff`:
   - Thuộc tính `$bonus`.
   - `calculateSalary()` = `$baseSalary + $bonus`.
3. `class ShiftStaff extends Staff`:
   - Thuộc tính `$shifts` (số ca), `$ratePerShift`.
   - `calculateSalary()` = `$baseSalary + ($shifts * $ratePerShift)`.
4. Tạo mảng gồm 3 nhân viên (2 Office, 1 Shift).
5. Dùng `usort` sắp xếp mảng theo lương giảm dần.
6. In ra danh sách tên và số tiền lương đã format tiền tệ.

---

## 2. LỜI GIẢI MẪU ĐỐI CHIẾU
```php
<?php
abstract class Staff {
    protected string $name;
    protected float $baseSalary;

    public function __construct(string $name, float $baseSalary) {
        $this->name = $name;
        $this->baseSalary = $baseSalary;
    }

    abstract public function calculateSalary(): float;

    public function getName(): string {
        return $this->name;
    }
}

class OfficeStaff extends Staff {
    private float $bonus;

    public function __construct(string $name, float $baseSalary, float $bonus) {
        parent::__construct($name, $baseSalary);
        $this->bonus = $bonus;
    }

    public function calculateSalary(): float {
        return $this->baseSalary + $this->bonus;
    }
}

class ShiftStaff extends Staff {
    private int $shifts;
    private float $ratePerShift;

    public function __construct(string $name, float $baseSalary, int $shifts, float $ratePerShift) {
        parent::__construct($name, $baseSalary);
        $this->shifts = $shifts;
        $this->ratePerShift = $ratePerShift;
    }

    public function calculateSalary(): float {
        return $this->baseSalary + ($this->shifts * $this->ratePerShift);
    }
}

$team = [
    new OfficeStaff("Nguyễn Văn A", 8000000, 3000000),      // 11.000.000
    new ShiftStaff("Trần Thị B", 5000000, 20, 200000),        // 5.000.000 + 4.000.000 = 9.000.000
    new OfficeStaff("Lê Văn C", 10000000, 5000000),         // 15.000.000
];

usort($team, fn($a, $b) => $b->calculateSalary() <=> $a->calculateSalary());

echo "<h3>BẢNG LƯƠNG NHÂN VIÊN:</h3><ul>";
foreach ($team as $member) {
    echo "<li>" . htmlspecialchars($member->getName()) . ": <strong>" . number_format($member->calculateSalary(), 0, ',', '.') . " đ</strong></li>";
}
echo "</ul>";
```
