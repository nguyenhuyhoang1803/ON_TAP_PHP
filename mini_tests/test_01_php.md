# MINI TEST 01: PHP CORE & FORM VALIDATION
- **Thời gian làm bài:** 15 phút
- **Tổng điểm:** 10 điểm
- **Mục tiêu:** Kiểm tra phản xạ viết form POST xử lý tính tiền, validation bẫy số 0, sticky form và escape HTML chống XSS.

---

## Đề Bài: Tính Cước Phí Vận Chuyển Hàng Hóa (Shipping Fee Calculator)
Viết một trang PHP đơn lẻ `shipping_calculator.php`:
1. **Form nhận các trường:**
   - `receiver_name`: Tên người nhận (bắt buộc, tối thiểu 3 ký tự).
   - `weight`: Khối lượng hàng tính bằng kg (bắt buộc, số thực $> 0$).
   - `distance`: Khoảng cách vận chuyển tính bằng km (bắt buộc, số nguyên $\ge 1$).
   - `is_fragile`: Hàng dễ vỡ (checkbox: có hoặc không).
2. **Quy tắc tính cước:**
   - Cước cơ bản: 20.000 VNĐ.
   - Mỗi kg: 5.000 VNĐ.
   - Mỗi km: 2.000 VNĐ.
   - Nếu có tick chọn `is_fragile` (Hàng dễ vỡ): Phụ phí thêm 30.000 VNĐ.
   - Nếu tổng cước trước thuế $\ge 200.000$ VNĐ: Giảm giá 10% trên tổng cước.
   - Thuế VAT: 8% sau khi đã trừ giảm giá.
3. **Yêu cầu kỹ thuật:**
   - Form gửi về chính trang (`action=""`).
   - Sticky Form: Khi có bất kỳ lỗi nào, giữ lại toàn bộ dữ liệu đã nhập trong các ô input và checkbox.
   - Hiển thị thông báo lỗi màu đỏ ngay dưới ô nhập bị sai.
   - Khi tính toán thành công: Hiển thị bảng chi tiết cước phí có định dạng tiền tệ `number_format()`.
   - Chống XSS 100% bằng `htmlspecialchars()` với `ENT_QUOTES`.

---

## Test Cases & Rubric Chấm Điểm (10 điểm)

| Tiêu chí | Điểm | Test Case Kiểm Thử |
|---|:---:|---|
| **Cấu trúc Form & Method** | 1.0đ | Form submit POST về chính file, thẻ đóng mở hợp lệ |
| **Validation Bắt buộc & Kiểu** | 2.5đ | Nhập rỗng báo lỗi; nhập chữ vào ô số báo lỗi |
| **Bẫy Số 0 & Âm** | 1.5đ | Nhập `weight = 0` hoặc âm -> Phải báo lỗi hợp lý |
| **Công thức Tính Cước & Thuế** | 2.5đ | Tính đúng cơ bản, phụ phí hàng dễ vỡ, giảm 10% khi $\ge 200k$, VAT 8% |
| **Sticky Form** | 1.5đ | Khi sai ô `receiver_name`, ô `weight` và checkbox vẫn giữ nguyên giá trị |
| **Bảo mật XSS** | 1.0đ | Nhập tên `<script>alert(1)</script>` -> Hiển thị nguyên văn, không popup |

---

## Edge Cases Cần Cẩn Trọng
- Người dùng nhập `weight = "0.5"` (số thập phân) -> Không được ép kiểu `(int)` làm mất số lẻ.
- Người dùng chỉ gõ dấu cách `"   "` vào ô tên người nhận -> Cần `trim()` trước khi kiểm tra rỗng.
- Checkbox `is_fragile` khi không tick sẽ không tồn tại trong `$_POST` -> Dùng `isset($_POST['is_fragile'])`.

---
*(Xem lời giải chi tiết tại [mini_tests/solutions/test_01_solution.md](file:///d:/MNM/OnTap/opensource_exam_training/mini_tests/solutions/test_01_solution.md))*
