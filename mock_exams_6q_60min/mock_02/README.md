# ĐỀ THI THỬ 6 CÂU 60 PHÚT - MOCK 02
> **Chủ đề:** Hệ thống Đặt Phòng Khách Sạn (`hotels`, `rooms`, `bookings`, `guests`)  
> **Thời gian:** 60 phút | **Tổng điểm:** 10.0 điểm  
> **Mức độ:** Chuẩn độ khó đề thi thật (Mục tiêu rèn phản xạ chính xác và bảo mật).

---

## CẤU TRÚC ĐỀ BÀI (6 CÂU)

### Câu 1 (1.5 điểm) - Form Đặt Phòng & Tính Tiền (Mục tiêu: ≤ 8 phút)
Tạo file `c1_calc_booking.php`:
- Form gồm: `guest_name`, `check_in` (date), `check_out` (date), `room_rate` (giá phòng/đêm), `children_count` (số trẻ em, mặc định 0).
- Xử lý khi POST:
  - `guest_name`: Không rỗng, độ dài $\ge 3$ ký tự.
  - `check_in` và `check_out`: Đúng định dạng ngày `Y-m-d`, và `check_out` phải sau `check_in` ít nhất 1 ngày.
  - `room_rate`: Số dương $\ge 200,000$ đ.
  - `children_count`: Số nguyên $\ge 0$. Mỗi trẻ em phụ thu $100,000$ đ/đêm.
- Tính toán: `Số đêm = (check_out - check_in)`, `Tổng tiền = (Số đêm * room_rate) + (Số đêm * children_count * 100,000)`.
- Hiển thị kết quả ra bảng tóm tắt kèm định dạng tiền tệ. Giữ lại dữ liệu đã nhập (Sticky form).

### Câu 2 (1.5 điểm) - Xác Thực & Phân Quyền Vai Trò (Role Guard) (Mục tiêu: ≤ 7 phút)
Tạo 2 file `c2_login.php` và `c2_admin.php`:
- `c2_login.php`: Cho phép đăng nhập. Có tùy chọn checkbox `"Ghi nhớ tài khoản"` (Remember me).
  - Có 2 tài khoản: `receptionist` (mật khẩu `123456`, vai trò `staff`), `manager` (mật khẩu `adminpass`, vai trò `admin`).
  - Nếu tick "Ghi nhớ", lưu cookie `saved_user` thời hạn 7 ngày.
  - Sau khi đăng nhập: Điều hướng `admin` sang `c2_admin.php`, còn `staff` sang `c2_staff.php`.
- `c2_admin.php`: Trang chỉ dành riêng cho `admin`.
  - Nếu chưa đăng nhập: redirect về `c2_login.php`.
  - Nếu đã đăng nhập nhưng vai trò là `staff`: Hiển thị lỗi `"403 - Bạn không có quyền truy cập trang quản trị"` (dùng HTTP status 403) và nút Quay lại.

### Câu 3 (1.5 điểm) - Upload Hộ Chiếu / CCCD Khách Lưu Trú (Mục tiêu: ≤ 8 phút)
Tạo file `c3_upload_id.php`:
- Form upload file input `id_card`.
- Validate:
  - Đuôi mở rộng: Chỉ cho phép `jpg`, `png`, `pdf`.
  - Dung lượng: Tối đa 2MB ($2 \times 1024 \times 1024$ bytes).
- Đổi tên file ngẫu nhiên: `guest_id_[md5_timestamp].[ext]`.
- Lưu vào thư mục `id_storage/`.
- Ghi log vào file `guests_access.csv` theo chuẩn CSV (dùng `fputcsv()` hoặc chuỗi có phẩy): `timestamp, original_name, stored_name, file_size`. Có cơ chế khóa `LOCK_EX`.

### Câu 4 (1.5 điểm) - Mô Hình OOP Phòng Khách Sạn & Interface VIP (Mục tiêu: ≤ 9 phút)
Tạo file `c4_oop_hotel.php`:
- Định nghĩa `interface VipService`: có phương thức `public function getVipAmenities(): array;`.
- Định nghĩa `abstract class Room`:
  - Thuộc tính `protected string $roomNumber`, `protected float $dailyRate`.
  - Constructor gán 2 thuộc tính.
  - Phương thức: `abstract public function calculateCost(int $days): float;`
- Class `StandardRoom extends Room`:
  - `calculateCost($days)` = `$days * $dailyRate`.
- Class `SuiteRoom extends Room implements VipService`:
  - Thuộc tính phụ `private float $minibarFee`.
  - `calculateCost($days)` = `($days * $dailyRate * 1.2) + $minibarFee`.
  - `getVipAmenities()` trả về `['Đưa đón sân bay miễn phí', 'Buffet sáng tại phòng', 'Spa 60 phút']`.
- Viết hàm `printInvoice(Room $room, int $days)` in ra số phòng, loại phòng (`Standard` hay `Suite`), tổng chi phí và danh sách dịch vụ VIP (nếu là Suite).

### Câu 5 (2.0 điểm) - PDO Thêm Đặt Phòng & PRG Pattern (Mục tiêu: ≤ 10 phút)
Cho bảng `bookings(id, booking_code, guest_name, room_id, total_price, created_at)` với `booking_code` là UNIQUE.
Tạo file `c5_booking_create.php`:
- Kết nối DB an toàn qua PDO.
- Nhận dữ liệu POST gồm `booking_code`, `guest_name`, `room_id`, `total_price`.
- Validate dữ liệu cơ bản. Dùng Prepared Statement thực hiện `INSERT`.
- Bắt lỗi trùng `booking_code` bằng `PDOException` kiểm tra mã lỗi `23000`. Thông báo lỗi rõ ràng nếu trùng mã.
- Nếu thành công: Áp dụng PRG pattern redirect sang `c5_booking_list.php?status=success` (kèm `exit;`).

### Câu 6 (2.0 điểm) - SQL Doanh Thu & Khách Sạn Đứng Đầu (Mục tiêu: ≤ 8 phút)
Cho 2 bảng:
- `hotels(id, hotel_name, city)`
- `bookings(id, hotel_id, total_price, status)`
Tạo file `c6_hotel_queries.sql` viết 2 câu SQL:
1. *(1.0 điểm)* Lấy danh sách toàn bộ khách sạn (kể cả khách sạn chưa có lượt đặt nào), kèm theo: Tổng số lượt đặt thành công (`status = 'completed'`), Tổng doanh thu thu được. Sắp xếp theo doanh thu giảm dần.
2. *(1.0 điểm)* Tìm khách sạn (hoặc các khách sạn nếu đồng hạng) có tổng doanh thu cao nhất thành phố `'Đà Nẵng'`. **(Bắt buộc dùng Subquery, không dùng LIMIT 1)**.

---

## RUBRIC CHẤM ĐIỂM
- Đúng nghiệp vụ: 5.0đ
- Xử lý biên (Edge case ngày check-out <= check-in, số trẻ em âm, trùng mã booking): 2.0đ
- Bảo mật (Prepared statement, XSS, Whitelist): 2.0đ
- Code style & logic OOP: 1.0đ
