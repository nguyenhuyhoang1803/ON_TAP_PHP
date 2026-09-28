# MINI TEST 03: OOP PHP, ABSTRACT, INTERFACE & USORT
- **Thời gian làm bài:** 15 phút
- **Tổng điểm:** 10 điểm
- **Mục tiêu:** Kiểm tra kiến trúc hướng đối tượng, phân biệt Abstract/Interface, tính toán đa hình và sắp xếp mảng đối tượng với `usort()`.

---

## Đề Bài: Quản Lý Sản Phẩm Bán Lẻ & Tính Thuế / Chiết Khấu
Thiết kế hệ thống quản lý sản phẩm bằng OOP PHP:
1. **Interface `Discountable`:**
   - Chứa phương thức: `public function getDiscountAmount(): float;`.
2. **Abstract Class `Product`:**
   - Thuộc tính `protected string $id`, `protected string $name`, `protected float $basePrice`.
   - Constructor khởi tạo 3 thuộc tính trên (khuyên dùng Constructor Promotion PHP 8).
   - Phương thức trừu tượng: `abstract public function calculateFinalPrice(): float;`.
   - Các getter cần thiết: `getId()`, `getName()`, `getBasePrice()`.
3. **Lớp con `ElectronicProduct` (Điện tử) kế thừa `Product`:**
   - Thuộc tính riêng: `int $warrantyMonths` (số tháng bảo hành).
   - Thuế VAT mặc định là 10% giá gốc.
   - Phí bảo hành: Mỗi tháng bảo hành tính thêm 20.000 VNĐ.
   - `calculateFinalPrice()` = `basePrice + (basePrice * 0.10) + (warrantyMonths * 20000)`.
4. **Lớp con `FoodProduct` (Thực phẩm) kế thừa `Product` VÀ implement `Discountable`:**
   - Thuộc tính riêng: `int $daysToExpiry` (số ngày còn lại đến hạn sử dụng).
   - `getDiscountAmount()`: Nếu số ngày hết hạn $\le 3$ ngày, giảm 30% giá gốc; nếu từ 4 đến 7 ngày, giảm 10% giá gốc; trên 7 ngày không giảm (giảm 0).
   - Thuế VAT thực phẩm là 5% giá gốc.
   - `calculateFinalPrice()` = `(basePrice - getDiscountAmount()) + (basePrice * 0.05)`.
5. **Thao tác trên danh sách đối tượng:**
   - Khởi tạo mảng gồm ít nhất 4 sản phẩm (2 Điện tử, 2 Thực phẩm).
   - Dùng toán tử `instanceof` để thống kê có bao nhiêu sản phẩm được áp dụng chính sách giảm giá (`Discountable`).
   - Sử dụng `usort()` và toán tử phi thuyền `<=>` để sắp xếp danh sách sản phẩm giảm dần theo giá bán cuối cùng (`calculateFinalPrice()`).
   - In bảng kết quả ra màn hình.

---

## Test Cases & Rubric Chấm Điểm (10 điểm)

| Tiêu chí | Điểm | Test Case Kiểm Thử |
|---|:---:|---|
| **Cấu trúc Interface & Abstract Class** | 2.5đ | Interface chỉ có chữ ký hàm; Abstract class có hàm trừu tượng đúng cú pháp |
| **Kế thừa & Constructor Promotion** | 2.0đ | Lớp con gọi đúng `parent::__construct()` hoặc cú pháp PHP 8 gọn gàng |
| **Công thức Đa hình** | 2.5đ | Tính đúng giá cuối cho Điện tử và Thực phẩm cận date |
| **Kiểm tra `instanceof`** | 1.0đ | Đếm chính xác số sản phẩm implement `Discountable` |
| **Sắp xếp `usort()`** | 2.0đ | Danh sách in ra xếp đúng thứ tự từ đắt nhất đến rẻ nhất |

---
*(Xem lời giải chi tiết tại [mini_tests/solutions/test_03_solution.md](file:///d:/MNM/OnTap/opensource_exam_training/mini_tests/solutions/test_03_solution.md))*
