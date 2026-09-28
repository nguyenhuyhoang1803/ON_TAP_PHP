# ĐỀ THI THỬ 6 CÂU 60 PHÚT - MOCK 03
> **Chủ đề:** Hệ thống Quản lý Y Tế & Phòng Khám (`doctors`, `patients`, `prescriptions`, `records`)  
> **Thời gian:** 60 phút | **Tổng điểm:** 10.0 điểm  
> **Đặc điểm nổi bật:** **ĐỀ THI GHÉP KỸ NĂNG** – Mỗi câu kết hợp từ 2 kỹ năng cốt lõi trở lên nhằm rèn luyện tư duy tích hợp dưới áp lực thời gian.

---

## CẤU TRÚC ĐỀ BÀI (6 CÂU GHÉP)

### Câu 1 (2.0 điểm) - Ghép [Session Guard + File Upload Hồ Sơ] (Mục tiêu: ≤ 10 phút)
Tạo file `c1_doctor_upload.php`:
- Giả lập phiên bác sĩ: Nếu chưa tồn tại `$_SESSION['doctor']`, giả lập đăng nhập hoặc redirect về login.
- Form upload hồ sơ bệnh án file `record_file`.
- Kiểm tra: File phải là `pdf`, `png` hoặc `jpg`, dung lượng $\le 3\text{MB}$.
- Đổi tên: `record_[doctor_id]_[time]_[random].[ext]`.
- Lưu vào `patient_records/` và ghi 1 dòng log vào `records.log` (khóa `LOCK_EX`): `[Thời gian] - Bác sĩ: [doctor_name] - File: [tên mới]`.
- Hiển thị thông báo thành công hoặc danh sách lỗi chi tiết.

### Câu 2 (1.5 điểm) - Ghép [OOP Kế Thừa + Autoload PSR-4] (Mục tiêu: ≤ 8 phút)
Tổ chức thư mục và code:
- Thư mục `src/Medical/`:
  - `Staff.php`: `abstract class Staff` thuộc namespace `App\Medical;` có thuộc tính `$id, $name, $baseSalary`. Phương thức abstract: `abstract public function calculateMonthlySalary(): float;`.
  - `Doctor.php`: `class Doctor extends Staff` thuộc namespace `App\Medical;` có thêm `$specialtyBonus`. Triển khai tính lương = `$baseSalary + $specialtyBonus`.
- File `c2_autoload_test.php` (ở thư mục gốc ngoài `src`):
  - Tự viết hàm `spl_autoload_register` ánh xạ namespace `App\` sang thư mục `src/` (thay `\` thành `/`).
  - Dùng lệnh `use App\Medical\Doctor;`. Khởi tạo 1 đối tượng Doctor, gọi phương thức tính lương và in ra màn hình.

### Câu 3 (2.0 điểm) - Ghép [AJAX Debounce + PHP PDO Database Search] (Mục tiêu: ≤ 10 phút)
Cho database bảng `patients(id, patient_code, fullname, phone)`.
- File `c3_api_patients.php`:
  - Kết nối PDO. Nhận tham số GET `q`.
  - Nếu `q` không rỗng: Dùng Prepared Statement `WHERE patient_code LIKE :kw OR fullname LIKE :kw OR phone LIKE :kw`.
  - Trả về JSON với `Content-Type: application/json`.
- File `c3_live_search.html`:
  - Ô input gõ từ khóa. Lắng nghe sự kiện `input` với Debounce 300ms.
  - Dùng `fetch()` gọi API. Nhận JSON và render danh sách bảng `<table>` bằng DOM an toàn (dùng `textContent` tránh XSS). Nếu không có dữ liệu hiển thị dòng "Không tìm thấy bệnh nhân".

### Câu 4 (2.0 điểm) - Ghép [PDO Phân Trang + Whitelist Sắp Xếp] (Mục tiêu: ≤ 10 phút)
Tạo file `c4_prescriptions_pagination.php`:
Cho bảng `prescriptions(id, prescription_code, patient_name, total_cost, created_at)`.
- Nhận `page` từ `$_GET['page']` (mặc định 1, min 1), `limit = 5`.
- Nhận `sort` (`total_cost`, `created_at`, mặc định `created_at`) và `dir` (`ASC`, `DESC`, mặc định `DESC`). Bắt buộc Whitelist an toàn.
- Đếm tổng số bản ghi bằng PDO `COUNT(*)`, tính `totalPages = ceil(total / limit)`. Kẹp `$page` trong đoạn `[1, totalPages]`.
- Truy vấn lấy dữ liệu trang hiện tại bằng `LIMIT :limit OFFSET :offset` (phải bind `PDO::PARAM_INT`).
- Xuất danh sách bảng và cụm nút phân trang: `Trang trước`, danh sách số trang `[1] [2] ...`, `Trang sau`. Tham số sắp xếp phải được giữ nguyên trên URL phân trang.

### Câu 5 (1.5 điểm) - Ghép [SQL JOIN 3 Bảng + GROUP BY + HAVING Báo Cáo] (Mục tiêu: ≤ 8 phút)
Cho 3 bảng:
- `doctors(id, doctor_name, department)`
- `prescriptions(id, doctor_id, diagnosis)`
- `prescription_items(id, prescription_id, medicine_name, quantity, unit_price)`
Tạo file `c5_doctor_report.sql`:
- Viết 1 câu SQL thống kê theo từng bác sĩ:
  - Mã bác sĩ, Tên bác sĩ, Khoa (`department`).
  - Tổng số toa thuốc đã kê (`total_prescriptions`).
  - Tổng tiền thuốc đã kê (`total_medicine_cost = SUM(quantity * unit_price)`). Nếu bác sĩ chưa kê toa nào thì tổng tiền là 0 (`COALESCE`).
  - Kể cả bác sĩ chưa từng kê toa nào cũng phải hiển thị.
  - Chỉ lọc ra các bác sĩ có tổng tiền thuốc $\ge 15,000,000$ đ. Sắp xếp theo tổng tiền giảm dần.

### Câu 6 (1.0 điểm) - Ghép [Sticky Form + Grand Total & Giảm Trừ BHYT] (Mục tiêu: ≤ 6 phút)
Tạo file `c6_insurance_calc.php`:
- Form nhập: Tiền khám (`exam_fee`), Tiền xét nghiệm (`lab_fee`), Tiền thuốc (`med_fee`), Mức hưởng BHYT (dropdown: 0%, 80%, 100%).
- Validate số dương, giữ lại dữ liệu form (Sticky form).
- Tính:
  - `Tổng chi phí gốc = exam_fee + lab_fee + med_fee`
  - `BHYT chi trả = Tổng chi phí gốc * (Tỷ lệ BHYT / 100)`
  - `Bệnh nhân thanh toán = Tổng chi phí gốc - BHYT chi trả`
- Hiển thị bảng thanh toán định dạng tiền VND.
