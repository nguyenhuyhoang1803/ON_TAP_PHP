# SPEED DRILL 7M-05: OOP ABSTRACT, INTERFACE & USORT
> **Thời gian tối đa:** 7 phút | **Mục tiêu:** Định nghĩa đúng cú pháp abstract class, interface, kế thừa và sắp xếp đối tượng qua spaceship operator.

---

## 1. ĐỀ BÀI
Tạo file `oop_quick.php`:
- Tạo `interface Discountable` có method `public function getDiscountAmount(): float;`.
- Tạo `abstract class Ticket`: thuộc tính `$code`, `$basePrice`, constructor, và method abstract `abstract public function calculateFinalPrice(): float;`.
- Tạo `class StudentTicket extends Ticket implements Discountable`:
  - `getDiscountAmount()` = `$basePrice * 0.3` (giảm 30%).
  - `calculateFinalPrice()` = `$basePrice - $this->getDiscountAmount()`.
- Tạo `class StandardTicket extends Ticket`:
  - `calculateFinalPrice()` = `$basePrice`.
- Tạo mảng gồm 3 vé (2 StudentTicket, 1 StandardTicket).
- Dùng `usort` sắp xếp theo `calculateFinalPrice()` giảm dần và in ra màn hình.

---

## 2. LỜI GIẢI MẪU (SOLUTION)
```php
<?php
interface Discountable {
    public function getDiscountAmount(): float;
}

abstract class Ticket {
    protected string $code;
    protected float $basePrice;

    public function __construct(string $code, float $basePrice) {
        $this->code = $code;
        $this->basePrice = $basePrice;
    }

    abstract public function calculateFinalPrice(): float;

    public function getCode(): string {
        return $this->code;
    }
}

class StudentTicket extends Ticket implements Discountable {
    public function getDiscountAmount(): float {
        return $this->basePrice * 0.3;
    }

    public function calculateFinalPrice(): float {
        return $this->basePrice - $this->getDiscountAmount();
    }
}

class StandardTicket extends Ticket {
    public function calculateFinalPrice(): float {
        return $this->basePrice;
    }
}

$tickets = [
    new StudentTicket('STU_01', 100000),  // 70.000
    new StandardTicket('STD_01', 80000),  // 80.000
    new StudentTicket('STU_02', 150000),  // 105.000
];

usort($tickets, fn($a, $b) => $b->calculateFinalPrice() <=> $a->calculateFinalPrice());

foreach ($tickets as $t) {
    echo $t->getCode() . " - " . number_format($t->calculateFinalPrice(), 0, ',', '.') . " đ<br>";
}
```
