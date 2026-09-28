<?php
// 05_oop/examples/polymorphism_demo.php

interface Billable {
    public function getFinalAmount(): float;
}

abstract class Booking {
    public function __construct(
        protected string $bookingCode,
        protected string $customerName,
        protected float $unitPrice
    ) {}

    abstract public function calculateBase(): float;

    public function getCode(): string { return $this->bookingCode; }
    public function getCustomer(): string { return $this->customerName; }
}

class RoomBooking extends Booking implements Billable {
    public function __construct(
        string $code,
        string $customer,
        float $pricePerNight,
        private int $nights
    ) {
        parent::__construct($code, $customer, $pricePerNight);
    }

    public function calculateBase(): float {
        return $this->unitPrice * $this->nights;
    }

    public function getFinalAmount(): float {
        // Thuế VAT 10%
        return $this->calculateBase() * 1.10;
    }
}

class TourBooking extends Booking implements Billable {
    public function __construct(
        string $code,
        string $customer,
        float $pricePerPerson,
        private int $peopleCount,
        private float $discountPercent = 0.05
    ) {
        parent::__construct($code, $customer, $pricePerPerson);
    }

    public function calculateBase(): float {
        return $this->unitPrice * $this->peopleCount;
    }

    public function getFinalAmount(): float {
        return $this->calculateBase() * (1 - $this->discountPercent);
    }
}

// KHỞI TẠO VÀ SẮP XẾP ĐA HÌNH
$bookings = [
    new RoomBooking('BK01', 'Nguyễn Văn A', 800000, 3),
    new TourBooking('BK02', 'Trần Thị B', 1500000, 2),
    new RoomBooking('BK03', 'Lê Văn C', 1200000, 1),
];

// Sắp xếp giảm dần theo số tiền thanh toán cuối cùng
usort($bookings, fn(Billable $a, Billable $b) => $b->getFinalAmount() <=> $a->getFinalAmount());

echo "DANH SÁCH ĐƠN ĐẶT CHỖ (GIẢM DẦN THEO TỔNG TIỀN):\n";
foreach ($bookings as $b) {
    echo "- Mã: {$b->getCode()} | Khách: {$b->getCustomer()} | Thành tiền: "
         . number_format($b->getFinalAmount(), 0, ',', '.') . " VNĐ\n";
}
