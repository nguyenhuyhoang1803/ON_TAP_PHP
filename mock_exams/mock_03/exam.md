# ĐỀ THI THỬ 03 (MOCK EXAM 03 - ĐỔI HOÀN TOÀN BỐI CẢNH)
> **Mục tiêu:** Kiểm tra khả năng biến ứng linh hoạt, không học vẹt theo mẫu có sẵn.  
> **Thời gian:** 60 phút | **Tổng điểm:** 10.0 điểm | **Hình thức:** Thực hành trên máy

---

## Bối Cảnh Bài Toán: Hệ Thống Đặt Bàn Nhà Hàng (Golden Spoon)
Nhà hàng "Golden Spoon" cần xây dựng hệ thống web quản lý bàn ăn, đặt bàn trực tuyến, tra cứu bàn trống theo thời gian thực (AJAX Realtime) và thống kê hiệu quả kinh doanh.

---

## CÁC YÊU CẦU ĐỀ THI

### Câu 1: Đặt Bàn & Validation Ngày Giờ (3.0 điểm)
- Viết trang `booking.php`:
  - Form gồm: `Tên khách hàng` (text), `Số điện thoại` (text, 10 số), `Số người` (number), `Ngày đặt bàn` (date), `Giờ đến` (time), `Ghi chú` (textarea, không bắt buộc).
  - **Validation:**
    - Tên khách hàng bắt buộc, tối thiểu 3 ký tự.
    - Số điện thoại đúng 10 số bắt đầu bằng số `0`.
    - Số người phải là số nguyên từ 1 đến 30.
    - Ngày đặt bàn phải lớn hơn hoặc bằng ngày hiện tại (`$bookingDate >= date('Y-m-d')`).
  - **Lưu dữ liệu:**
    - Insert vào bảng `reservations` bằng Prepared Statement.
    - Áp dụng kỹ thuật PRG (Post/Redirect/Get) kết hợp Session Flash message: "Đặt bàn thành công! Mã đặt chỗ của bạn là #ID".

### Câu 2: Live Search Bàn Ăn Thời Gian Thực (AJAX Fetch API) (3.0 điểm)
- Viết file `api_tables.php`:
  - Nhận tham số qua GET: `capacity` (sức chứa tối thiểu) và `type` (loại bàn: `indoor` - Trong nhà, `outdoor` - Ngoài trời, `vip` - Phòng VIP, hoặc `all`).
  - Trả về JSON danh sách các bàn ăn thỏa mãn điều kiện và đang ở trạng thái `status = 'available'`.
- Viết file `find_table.html`:
  - Giao diện có ô chọn Sức chứa (select box: 2, 4, 6, 8, 10+ người) và nút radio chọn Loại bàn.
  - Lắng nghe sự kiện thay đổi dữ liệu, áp dụng kỹ thuật Debounce 300ms.
  - Gọi Fetch API lấy dữ liệu ngầm không tải lại trang.
  - Render bảng kết quả bằng JavaScript DOM (`document.createElement` và `textContent`), đảm bảo chống XSS.

### Câu 3: Báo Cáo SQL Doanh Thu Bàn Ăn & Đồng Hạng (2.5 điểm)
- Viết file `table_report.php`:
  - Viết 1 câu lệnh SQL duy nhất thống kê: `Mã bàn`, `Tên bàn`, `Khu vực`, `Số lượt phục vụ` (`total_servings`), `Tổng doanh thu mang lại` (`total_revenue`).
  - **Bắt buộc:** Hiển thị cả các bàn chưa từng có khách ngồi (số lượt = 0, doanh thu = 0) bằng `LEFT JOIN`.
  - Tìm ra bàn (hoặc các bàn) đem lại doanh thu cao nhất. Nếu có 2 bàn cùng đạt doanh thu cao nhất thì hiển thị cả 2 (Xử lý đồng hạng bằng Subquery).

### Câu 4: Thiết Kế OOP Tính Chiết Khấu Khách Hàng (1.5 điểm)
- Viết file `DiscountService.php`:
  - Interface `DiscountPolicy`: Có phương thức `public function applyDiscount(float $billAmount): float;`.
  - Class `StandardMember` implement `DiscountPolicy`: Không giảm giá (trả về 0).
  - Class `VipMember` implement `DiscountPolicy`: Giảm 15% tổng hóa đơn.
  - Class `BirthdayMember` implement `DiscountPolicy`: Giảm 20% tổng hóa đơn nếu hóa đơn $\ge 500.000$ VNĐ, ngược lại giảm 10%.
  - Viết code chạy thử tính số tiền thanh toán cuối cùng cho một hóa đơn 1.200.000 VNĐ áp dụng lần lượt 3 loại khách hàng trên.

---

## PHẦN DÀNH CHO GIÁM THỊ: BẪY & TEST CASES
1. **Test ngày quá khứ:** Giám thị chọn ngày hôm qua trong ô đặt bàn -> Hệ thống phải báo lỗi đỏ: "Ngày đặt bàn không thể là ngày trong quá khứ".
2. **Test XSS trong ô Ghi chú:** Giám thị nhập ghi chú là `<script>alert('hack')</script>` -> Khi hiển thị lại phải render an toàn.
3. **Test Debounce:** Bấm đổi liên tục sức chứa -> Server chỉ phản hồi request cuối cùng.
4. **Test Đồng hạng SQL:** Có 2 bàn cùng doanh thu 10 triệu -> Bảng báo cáo phải nhận diện được cả 2 bàn là doanh thu cao nhất.

---
*(Xem schema tại [schema.sql](file:///d:/MNM/OnTap/opensource_exam_training/mock_exams/mock_03/schema.sql) và lời giải tại [solutions/solution.md](file:///d:/MNM/OnTap/opensource_exam_training/mock_exams/mock_03/solutions/solution.md))*
