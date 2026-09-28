# BÀI TẬP RÈN LUYỆN: OOP PHP

## Đề bài: Quản lý Phương Tiện Giao Thông
1. Tạo abstract class `Vehicle`:
   - Thuộc tính `protected string $id`, `protected string $brand`, `protected float $basePrice`.
   - Hàm trừu tượng: `abstract public function calculateTax(): float;`.
   - Hàm `public function getTotalPrice(): float` = `basePrice + calculateTax()`.
2. Tạo 2 lớp con:
   - `Car`: Có thêm `int $seats` (số chỗ ngồi). Nếu số chỗ ngồi $\le 5$, thuế = 30% giá gốc; nếu $> 5$ chỗ, thuế = 20% giá gốc.
   - `Motorbike`: Có thêm `int $engineCapacity` (dung tích xi lanh cc). Nếu cc $> 150$, thuế = 15% giá gốc, ngược lại thuế = 5%.
3. Tạo mảng chứa 4 phương tiện (2 Car, 2 Motorbike).
4. Sử dụng toán tử `instanceof` để thống kê có bao nhiêu xe là ô tô (`Car`).
5. Sắp xếp mảng tăng dần theo `getTotalPrice()`.
