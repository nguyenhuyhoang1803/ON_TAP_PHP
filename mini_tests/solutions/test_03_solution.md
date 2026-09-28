# LỜI GIẢI MINI TEST 03: OOP PHP & ĐA HÌNH

```php
<?php
// mini_tests/solutions/test_03_solution.php

interface Discountable {
    public function getDiscountAmount(): float;
}

abstract class Product {
    public function __construct(
        protected string $id,
        protected string $name,
        protected float $basePrice
    ) {}

    abstract public function calculateFinalPrice(): float;

    public function getId(): string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getBasePrice(): float { return $this->basePrice; }
}

class ElectronicProduct extends Product {
    public function __construct(
        string $id,
        string $name,
        float $basePrice,
        private int $warrantyMonths
    ) {
        parent::__construct($id, $name, $basePrice);
    }

    public function calculateFinalPrice(): float {
        $vat = $this->basePrice * 0.10;
        $warrantyFee = $this->warrantyMonths * 20000;
        return $this->basePrice + $vat + $warrantyFee;
    }

    public function getWarrantyMonths(): int { return $this->warrantyMonths; }
}

class FoodProduct extends Product implements Discountable {
    public function __construct(
        string $id,
        string $name,
        float $basePrice,
        private int $daysToExpiry
    ) {
        parent::__construct($id, $name, $basePrice);
    }

    public function getDiscountAmount(): float {
        if ($this->daysToExpiry <= 3) {
            return $this->basePrice * 0.30; // Giảm 30%
        } elseif ($this->daysToExpiry <= 7) {
            return $this->basePrice * 0.10; // Giảm 10%
        }
        return 0.0;
    }

    public function calculateFinalPrice(): float {
        $discount = $this->getDiscountAmount();
        $priceAfterDiscount = $this->basePrice - $discount;
        $vat = $this->basePrice * 0.05;
        return $priceAfterDiscount + $vat;
    }

    public function getDaysToExpiry(): int { return $this->daysToExpiry; }
}

// KHỞI TẠO DỮ LIỆU THỬ NGHIỆM
$catalog = [
    new ElectronicProduct('E01', 'Chuột Logitech G102', 400000, 12),
    new FoodProduct('F01', 'Sữa chua Vinamilk lốc 4 hộp', 35000, 2),    // Cận date (giảm 30%)
    new ElectronicProduct('E02', 'Bàn phím cơ DareU', 850000, 24),
    new FoodProduct('F02', 'Bánh mì sandwich ngũ cốc', 45000, 5),      // Giảm 10%
    new FoodProduct('F03', 'Gạo lứt đỏ 5kg', 180000, 60),              // Không giảm
];

// 1. Kiểm tra instanceof
$discountableCount = 0;
foreach ($catalog as $item) {
    if ($item instanceof Discountable) {
        $discountableCount++;
    }
}

// 2. Sắp xếp giảm dần theo giá bán cuối cùng
usort($catalog, fn(Product $a, Product $b) => $b->calculateFinalPrice() <=> $a->calculateFinalPrice());

// 3. In kết quả
echo "========================================================================================\n";
echo "                         DANH SÁCH SẢN PHẨM & GIÁ BÁN CUỐI CÙNG\n";
echo "========================================================================================\n";
printf("%-6s | %-32s | %-12s | %-12s | %-15s\n", "MÃ", "TÊN SẢN PHẨM", "GIÁ GỐC", "GIẢM GIÁ", "GIÁ CUỐI");
echo "----------------------------------------------------------------------------------------\n";

foreach ($catalog as $p) {
    $discountText = ($p instanceof Discountable && $p->getDiscountAmount() > 0)
        ? number_format($p->getDiscountAmount(), 0, ',', '.') . ' đ'
        : '--';

    printf(
        "%-6s | %-32s | %10s đ | %10s | %13s đ\n",
        $p->getId(),
        $p->getName(),
        number_format($p->getBasePrice(), 0, ',', '.'),
        $discountText,
        number_format($p->calculateFinalPrice(), 0, ',', '.')
    );
}

echo "========================================================================================\n";
echo "Thống kê: Có {$discountableCount} sản phẩm có chính sách chiết khấu (Discountable).\n";
```
