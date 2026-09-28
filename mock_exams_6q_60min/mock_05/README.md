# ĐỀ THI THỬ 6 CÂU 60 PHÚT - MOCK 05
> **Chủ đề:** Dịch Vụ Vận Tải & Cho Thuê Xe Tự Lái (`vehicles`, `drivers`, `trips`, `customers`)  
> **Thời gian:** 60 phút | **Tổng điểm:** 10.0 điểm  
> **MÔ PHỎNG ĐỀ THI THẬT CHUẨN:** Các câu hỏi được diễn đạt dưới dạng yêu cầu nghiệp vụ thực tế, **KHÔNG GẮN NHÃN TÊN MODULE HAY KỸ THUẬT**. Bạn phải tự phân tích và quyết định giải pháp kỹ thuật phù hợp!

---

## NỘI DUNG ĐỀ THI

### Yêu Cầu 1 (1.5 điểm) - Thời gian dự kiến: 7 phút
Xây dựng trang `trip_estimator.php` hỗ trợ khách hàng ước tính chi phí cho chuyến đi:
- Khách hàng nhập vào: Số km di chuyển (`km`), Giờ xuất phát (`start_hour`: từ 0 đến 23), Loại xe (dropdown: 4 chỗ hoặc 7 chỗ).
- Quy tắc tính cước:
  - Giá mở cửa (1 km đầu tiên): Xe 4 chỗ là $15,000$ đ; Xe 7 chỗ là $18,000$ đ.
  - Từ km thứ 2 đến km thứ 30: Xe 4 chỗ tính $12,000$ đ/km; Xe 7 chỗ tính $14,000$ đ/km.
  - Từ km thứ 31 trở đi: Xe 4 chỗ tính $10,000$ đ/km; Xe 7 chỗ tính $11,500$ đ/km.
  - Nếu giờ xuất phát rơi vào ban đêm (từ 22h đêm đến 5h sáng hôm sau): Phụ thu thêm 15% trên tổng cước di chuyển.
- Validate: `km` phải là số thực $> 0$; `start_hour` phải là số nguyên từ 0 đến 23.
- Kết quả: Hiển thị bảng chi tiết tiền cước gốc, tiền phụ thu ban đêm (nếu có) và tổng thanh toán (định dạng tiền tệ VNĐ). Nếu có lỗi, hiển thị thông báo lỗi và giữ lại thông tin người dùng đã nhập.

### Yêu Cầu 2 (1.5 điểm) - Thời gian dự kiến: 7 phút
Xây dựng cơ chế đăng nhập và bảo vệ trang làm việc cho nhân viên điều vận trong 2 file `dispatcher_login.php` và `dispatcher_panel.php`:
- `dispatcher_login.php`: Nhân viên nhập tài khoản và mật khẩu. Tài khoản hợp lệ duy nhất: `dieuhanh` / `xe@2026`.
  - Có checkbox "Lưu đăng nhập trong 3 ngày". Nếu chọn, lưu cookie tương ứng.
  - Đăng nhập thành công: Lưu thông tin vào phiên làm việc (Session) gồm tên tài khoản và thời điểm đăng nhập, sau đó chuyển hướng ngay lập tức sang trang `dispatcher_panel.php`.
- `dispatcher_panel.php`: Chỉ những nhân viên đã đăng nhập thành công mới được xem trang này. Nếu phát hiện chưa đăng nhập, tự động chuyển hướng ngay về trang đăng nhập mà không thực thi bất kỳ mã nào phía sau.
  - Hiển thị tên nhân viên, thời điểm đăng nhập và cung cấp đường dẫn Đăng xuất an toàn.

### Yêu Cầu 3 (1.5 điểm) - Thời gian dự kiến: 8 phút
Xây dựng trang `driver_license_upload.php` cho phép tài xế cập nhật ảnh chụp Giấy phép lái xe:
- Tiếp nhận file ảnh thông qua form upload.
- Tiêu chuẩn kiểm duyệt:
  - Chỉ chấp nhận các định dạng ảnh: `jpg`, `png`, `webp`.
  - Dung lượng file tối đa cho phép là 2MB.
- Khi tiếp nhận file thành công:
  - Không dùng tên gốc của file. Đổi sang tên ngẫu nhiên có tiền tố `gplx_` kèm chuỗi định danh duy nhất để tránh xung đột file.
  - Lưu vào thư mục `driver_licenses/`.
  - Ghi nhận nhật ký vào file `upload_log.txt` với cơ chế khóa file độc quyền (LOCK): `[Thời gian] | Tên file đã lưu | Kích thước bytes`.
  - Thông báo rõ ràng cho tài xế kết quả tiếp nhận.

### Yêu Cầu 4 (1.5 điểm) - Thời gian dự kiến: 9 phút
Xây dựng mô hình tính toán định mức nhiên liệu và chi phí vận hành cho các dòng xe trong file `fleet_management.php`:
- Tạo một lớp trừu tượng đại diện cho Phương tiện vận tải (`Vehicle`):
  - Chứa biển số xe (`licensePlate`), mức tiêu hao cơ sở trên 100km (`baseFuelRate` - lít).
  - Có phương thức trừu tượng tính toán chi phí nhiên liệu cho quãng đường $S$ km với đơn giá xăng $P$ đ/lít: `calculateFuelCost(float $km, float $fuelPrice): float`.
- Tạo lớp Xe Tải (`Truck`) kế thừa từ Phương tiện:
  - Có thêm thuộc tính tải trọng hàng đang chở (`cargoWeight` - tấn).
  - Mỗi tấn hàng chở theo làm mức tiêu hao thực tế tăng thêm 5% so với mức tiêu hao cơ sở.
- Tạo lớp Xe Khách (`Bus`) kế thừa từ Phương tiện:
  - Có thêm phụ phí bật điều hòa cố định là $50,000$ đ cho cả chuyến đi.
- Khởi tạo danh sách gồm 2 Xe Tải và 1 Xe Khách. Tính toán chi phí cho quãng đường 200 km với giá xăng 23,500 đ/lít. Sắp xếp danh sách theo chi phí nhiên liệu tăng dần và xuất thông tin ra màn hình.

### Yêu Cầu 5 (2.0 điểm) - Thời gian dự kiến: 9 phút
Xây dựng tính năng tra cứu xe trực tuyến cho hành khách mà không làm tải lại trang web trong `api_vehicles.php` và `search_vehicles.html`:
- Dữ liệu xe có thể lấy từ mảng giả lập hoặc cơ sở dữ liệu gồm các trường: Biển số, Hãng xe, Số chỗ, Giá thuê/ngày.
- Ô tìm kiếm trên giao diện phải tự động gửi yêu cầu tra cứu sau khi người dùng dừng bấm phím 300 mili-giây.
- Dữ liệu nhận về ở định dạng JSON và được đưa vào giao diện an toàn (không để xảy ra lỗi tiêm mã độc kịch bản XSS). Nếu không có kết quả phù hợp, phải có phản hồi trực quan trên màn hình.

### Yêu Cầu 6 (2.0 điểm) - Thời gian dự kiến: 8 phút
Cho cơ sở dữ liệu quản lý các chuyến xe gồm:
- `drivers(id, driver_name, phone)`
- `trips(id, driver_id, trip_cost, status)` -- `status` có thể là 'completed', 'canceled'
Viết các câu lệnh SQL trong file `fleet_reports.sql`:
1. *(1.0 điểm)* Lập danh sách toàn bộ các tài xế trong hệ thống (kể cả những tài xế mới chưa thực hiện chuyến đi nào), kèm theo: Tổng số chuyến hoàn thành (`total_completed_trips`), Tổng doanh thu mang lại (`total_revenue`). Nếu chưa có chuyến nào thì doanh thu hiển thị là 0.
2. *(1.0 điểm)* Tìm tài xế có doanh thu hoàn thành cao nhất hệ thống (xử lý trường hợp có từ 2 tài xế trở lên cùng đạt mức doanh thu cao nhất, không sử dụng `LIMIT 1`).

---

## HƯỚNG DẪN CHẤM & ĐÁNH GIÁ
- **Tốc độ:** Tổng thời gian hoàn thành toàn bộ 6 câu phải $\le 60$ phút.
- **Tính trọn vẹn:** Đề thi ưu tiên độ chính xác từng câu. Câu nào làm xong phải chắc điểm câu đó.
