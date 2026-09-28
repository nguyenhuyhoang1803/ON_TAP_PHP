# BÀI TẬP RÈN LUYỆN: FORM VALIDATION

## Đề bài: Form Tính Tiền Điện Sinh Hoạt
Tạo file `electric_bill.php` xử lý form tính tiền điện:
- Các trường đầu vào:
  - `customer_name`: Tên chủ hộ (bắt buộc, không quá 50 ký tự).
  - `prev_number`: Chỉ số điện cũ (kWh). Bắt buộc, số nguyên $\ge 0$.
  - `curr_number`: Chỉ số điện mới (kWh). Bắt buộc, số nguyên $\ge$ chỉ số điện cũ.
- Bậc giá điện:
  - 50 kWh đầu: 1.800 VNĐ / kWh
  - Từ 51 - 100 kWh: 2.200 VNĐ / kWh
  - Trên 100 kWh: 3.000 VNĐ / kWh
  - Thuế VAT: 10% tổng tiền điện.

### Yêu cầu kỹ thuật:
1. Xử lý form gửi về chính trang.
2. Hiển thị thông báo lỗi chi tiết nếu chỉ số mới < chỉ số cũ.
3. Giữ lại toàn bộ dữ liệu đã nhập trên form khi có lỗi.
4. Hiển thị bảng chi tiết các bậc tính tiền và tổng tiền thanh toán (có format dấu chấm hàng nghìn).
