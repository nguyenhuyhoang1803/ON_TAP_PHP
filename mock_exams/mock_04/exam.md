# ĐỀ THI THỬ 04 (MOCK EXAM 04 - NÂNG CAO & FULL EDGE CASES)
> **Mức độ:** Khó hơn đề chuẩn 10-15% (Chinh phục điểm 9.5 - 10.0 tuyệt đối).  
> **Thời gian:** 60 phút | **Tổng điểm:** 10.0 điểm | **Hình thức:** Thực hành trên máy

---

## Bối Cảnh Bài Toán: Quản Lý Bệnh Nhân & Toa Thuốc (MediCare Pro)
Bệnh viện đa khoa "MediCare Pro" cần một hệ thống quản lý hồ sơ bệnh nhân, upload bệnh án số, tìm kiếm nâng cao (hỗ trợ tên nước ngoài có nháy đơn, ký tự đặc biệt), phân trang và thống kê doanh thu toa thuốc theo bác sĩ.

---

## CÁC YÊU CẦU ĐỀ THI

### Câu 1: Tiếp Nhận Bệnh Nhân & Upload Hồ Sơ Bệnh Án PDF (2.5 điểm)
- Viết trang `register_patient.php`:
  - Form tiếp nhận gồm: `Mã định danh CCCD/BHYT` (text), `Họ và tên` (text), `Ngày sinh` (date), `Giới tính` (radio: Nam/Nữ), `File hồ sơ bệnh án cũ` (file upload).
  - **Validation & Upload an toàn:**
    - Mã CCCD/BHYT bắt buộc, độ dài từ 10 đến 12 ký tự.
    - Họ tên bắt buộc, từ 3 ký tự.
    - File upload: Bắt buộc kiểm tra `['error'] === UPLOAD_ERR_OK`. Chỉ chấp nhận file có đuôi `.pdf` và dung lượng $\le 2\text{MB}$.
    - Đổi tên file ngẫu nhiên bằng `uniqid('record_', true) . '.pdf'` lưu vào thư mục `storage/medical_records/`.
  - **Lưu CSDL & Xử lý Trùng lặp:**
    - Dùng Prepared Statement lưu vào bảng `patients`.
    - Bắt ngoại lệ PDO khi trùng mã CCCD/BHYT (mã lỗi `23000`) -> Báo lỗi đỏ thân thiện, xóa file PDF vừa upload (nếu có) để tránh sinh file rác mồ côi.
    - Áp dụng PRG chuyển sang `patient_list.php?msg=success`.

### Câu 2: Tìm Kiếm Ký Tự Đặc Biệt & Phân Trang Chuẩn Mực (3.0 điểm)
- Viết trang `patient_list.php`:
  - Hiển thị bảng danh sách bệnh nhân gồm: `ID`, `Mã CCCD`, `Họ tên`, `Ngày sinh`, `Giới tính`, `File bệnh án` (link tải), `Ngày tiếp nhận`.
  - **Tìm kiếm:**
    - Ô tìm kiếm theo `Họ tên` hoặc `Mã CCCD`.
    - Phải xử lý an toàn tuyệt đối các từ khóa có dấu nháy đơn (`Patrick O'Connor`) và escape ký tự `%`, `_` trong LIKE.
  - **Sắp xếp Whitelist:** Theo `id`, `fullname`, `dob`, `created_at` (chiều `ASC`/`DESC`). Mặc định `id DESC`.
  - **Phân trang:** Hiển thị 5 bệnh nhân / trang.
  - **Thuật toán Kẹp (Clamp):** Ép `$page = max(1, min($page, $totalPages))` để chống lỗi trang âm hoặc trang vượt trần.
  - Giữ toàn bộ tham số tìm kiếm và sắp xếp khi click chuyển trang bằng `http_build_query()`.

### Câu 3: SQL Thống Kê Chi Phí Thuốc Theo Bác Sĩ (2.5 điểm)
- Viết trang `doctor_stats.php`:
  - Viết 1 câu lệnh SQL duy nhất thống kê: `Mã bác sĩ`, `Họ tên bác sĩ`, `Chuyên khoa`, `Số toa thuốc đã kê` (`total_prescriptions`), `Tổng tiền thuốc đã kê` (`total_drug_cost`).
  - **Điều kiện thời gian:** Lọc các toa thuốc được kê trong tháng 09/2026.
  - **Yêu cầu sống còn:** Bắt buộc hiển thị cả các bác sĩ **chưa kê toa thuốc nào trong tháng 9** (với số toa = 0, tiền thuốc = 0) bằng `LEFT JOIN` (Lưu ý: Đặt điều kiện ngày trong `ON`, không đặt trong `WHERE`).
  - Tìm bác sĩ có tổng tiền thuốc kê nhiều nhất trong tháng 9 (Xử lý đồng hạng bằng Subquery nếu có 2 bác sĩ bằng nhau).

### Câu 4: Phòng Thủ Bảo Mật Toàn Diện (2.0 điểm)
- Kiểm tra toàn bộ ứng dụng:
  - 100% các biến echo ra HTML đều phải bọc qua hàm `e()` (`htmlspecialchars` với `ENT_QUOTES`).
  - Bắt lỗi ngoại lệ PDO: Tuyệt đối không để lộ thông báo database ra màn hình người dùng, ghi chi tiết lỗi vào file `error.log`.
  - Bảo vệ URL và thư mục: Kiểm tra không để lộ đường dẫn nhạy cảm.

---

## PHẦN DÀNH CHO GIÁM THỊ: BẪY & TEST CASES
1. **Test nháy đơn:** Tìm kiếm `O'Connor` -> Đảm bảo không bị vỡ câu lệnh SQL syntax error.
2. **Test LIKE wildcard:** Tìm kiếm ký tự `%` -> Không được trả về toàn bộ danh sách bệnh nhân.
3. **Test File Upload:** Đổi tên file `shell.php` thành `shell.php.pdf` -> Kiểm tra hệ thống có xử lý an toàn không.
4. **Test LEFT JOIN ngày tháng:** Kiểm tra bác sĩ chưa có toa thuốc trong tháng 9 có bị mất khỏi bảng báo cáo hay không.

---
*(Xem schema tại [schema.sql](file:///d:/MNM/OnTap/opensource_exam_training/mock_exams/mock_04/schema.sql) và lời giải tại [solutions/solution.md](file:///d:/MNM/OnTap/opensource_exam_training/mock_exams/mock_04/solutions/solution.md))*
