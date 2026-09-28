<?php
// 05_oop/solutions/mini_challenge_solution.php

interface BonusCalculable {
    public function getBonus(): float;
}

abstract class Person {
    public function __construct(protected string $name) {}
    public function getName(): string { return $this->name; }
}

class Manager extends Person implements BonusCalculable {
    public function __construct(
        string $name,
        private float $baseSalary,
        private float $kpiScore
    ) {
        parent::__construct($name);
    }

    public function getBonus(): float {
        if ($this->kpiScore >= 0.8) {
            return $this->baseSalary * 0.3;
        }
        return 0.0;
    }

    public function getTotalIncome(): float {
        return $this->baseSalary + $this->getBonus();
    }

    public function getBaseSalary(): float { return $this->baseSalary; }
    public function getKpiScore(): float { return $this->kpiScore; }
}

$managers = [
    new Manager('Hoàng Nam', 20000000, 0.85),
    new Manager('Minh Thư', 25000000, 0.70),
    new Manager('Quốc Tuấn', 18000000, 0.95),
];

// Sắp xếp giảm dần theo tổng thu nhập
usort($managers, fn(Manager $a, Manager $b) => $b->getTotalIncome() <=> $a->getTotalIncome());

echo "DANH SÁCH QUẢN LÝ (SẮP XẾP GIẢM DẦN THEO TỔNG THU NHẬP):\n";
echo "--------------------------------------------------------\n";
foreach ($managers as $m) {
    echo "- Họ tên: " . str_pad($m->getName(), 12)
       . " | Lương cứng: " . number_format($m->getBaseSalary(), 0, ',', '.') . " VNĐ"
       . " | KPI: " . $m->getKpiScore()
       . " | Thưởng: " . number_format($m->getBonus(), 0, ',', '.') . " VNĐ"
       . " | TỔNG THU NHẬP: " . number_format($m->getTotalIncome(), 0, ',', '.') . " VNĐ\n";
}
