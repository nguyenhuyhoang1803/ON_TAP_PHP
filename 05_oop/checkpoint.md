# CHECKPOINT 05: LẬP TRÌNH HƯỚNG ĐỐI TƯỢNG (OOP)

> **Điều kiện vượt qua:** Đạt từ 75% trở lên (>= 7.5/10 điểm).

---

### PHẦN A – LÝ THUYẾT NHANH (5 câu x 0.6đ = 3.0đ)
1. Điểm khác biệt quan trọng nhất giữa `Abstract Class` và `Interface` trong PHP là gì?
2. Từ khóa `parent::` dùng để làm gì và trong trường hợp nào bắt buộc phải dùng?
3. Toán tử `instanceof` dùng để làm gì? Cho ví dụ kiểm tra một đối tượng `$obj` có thực thi interface `Exportable` hay không.
4. Thuộc tính `static` khác gì thuộc tính thông thường? Khi nào nên dùng thuộc tính tĩnh trong đề thi?
5. Tại sao không thể khởi tạo trực tiếp đối tượng từ một Abstract class (`$x = new AbstractClass();`)?

---

### PHẦN B – ĐỌC CODE & BẮT BUG (2 đoạn code x 1.0đ = 2.0đ)

#### Bug 1:
```php
abstract class Shape {
    abstract public function area();
}

class Square extends Shape {
    public function __construct(private float $side) {}
    // Không khai báo phương thức area()
}
$sq = new Square(5);
```
*Lỗi gì sẽ xuất hiện khi chạy đoạn code trên?*

#### Bug 2:
```php
class Product {
    private static int $total = 0;
    public function __construct() {
        $this->total++;
    }
}
$p = new Product();
```
*Đoạn code trên bị sai cú pháp ở dòng nào? Sửa lại thế nào?*

---

### PHẦN C – VIẾT CODE THỰC HÀNH (3.0đ)
Thiết kế hệ thống tài khoản ngân hàng:
- Interface `Transferable`: Có phương thức `public function transferTo(BankAccount $dest, float $amount): bool;`.
- Class trừu tượng `BankAccount`: Thuộc tính `accountNumber`, `balance`. Hàm rút tiền `withdraw(float $amount)` và nạp tiền `deposit(float $amount)`.
- Lớp con `SavingsAccount` (Tài khoản tiết kiệm) và `CheckingAccount` (Tài khoản thanh toán, implement `Transferable`).
- Viết logic chuyển tiền từ CheckingAccount sang tài khoản khác (chỉ chuyển được nếu số dư đủ, trừ tiền tài khoản nguồn và cộng tiền tài khoản đích).

---

### PHẦN D – BIẾN THỂ ĐỀ THI (2.0đ)
Nếu đề bài yêu cầu: *"Xây dựng một lớp `Cart` chứa một mảng các đối tượng `Product`. Lớp `Cart` có phương thức `getTotal()` tính tổng tiền giỏ hàng, và phương thức `sortByPriceDesc()` để sắp xếp các sản phẩm trong giỏ"*.  
Hãy viết bộ khung class `Cart` hoàn chỉnh thực hiện yêu cầu này.
