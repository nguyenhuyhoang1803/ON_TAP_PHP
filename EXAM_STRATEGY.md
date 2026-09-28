# CHIẾN THUẬT PHÂN BỔ THỜI GIAN & LÀM BÀI THI 60 PHÚT (EXAM STRATEGY)
> Mục tiêu tối thượng trong phòng thi: **TỐI ĐA HÓA ĐIỂM SỐ TRÊN MỖI PHÚT**. Đạt chắc chắn 7–8 điểm trong 40 phút đầu tiên, dành 15 phút tiếp theo nâng lên 9–10 điểm, và 5 phút cuối cùng kiểm tra an toàn & nộp bài.

---

## 1. Thời Khắc Vàng: Biểu Đồ 60 Phút

```text
[00:00 - 03:00] Đọc lướt toàn bộ đề, xác định schema, nhận diện bẫy
[03:00 - 15:00] Tạo CSDL, kết nối PDO, dựng câu truy vấn SELECT danh sách (2.0 - 2.5đ)
[15:00 - 30:00] Hoàn thiện CRUD (Thêm, Sửa, Xóa) + Validation chuẩn (3.0 - 3.5đ)
[30:00 - 42:00] Xử lý Lọc/Tìm kiếm/Sắp xếp Whitelist hoặc JavaScript Fetch (2.0đ)
[42:00 - 55:00] Giải quyết câu nâng cao (SQL Báo cáo/Phân trang hoàn chỉnh/OOP) (1.5 - 2.0đ)
[55:00 - 60:00] Checklist 5 phút cuối: Bật error check, test bảo mật, dọn rác, nộp bài
```

---

## 2. 5 Quy Tắc "Sống Còn" Trong Phòng Thi

### Quy tắc 1: Không CSS nếu đề không chấm giao diện
- **Sự thật phũ phàng:** Rất nhiều bạn dành 20 phút để chỉnh Bootstrap, căn giữa nút bấm, chọn màu pastel đẹp mắt... nhưng barem điểm chấm giao diện chỉ chiếm tối đa 0.5 điểm (thậm chí 0 điểm đối với môn Lập trình mã nguồn mở).
- **Hành động:** Sử dụng thẻ HTML thô: `<table>`, `<form>`, `<input>`, `<button>`. Viết thẻ `table border="1" cellpadding="5"` là đủ để giám khảo nhìn rõ dữ liệu!

### Quy tắc 2: Không nộp code bị lỗi Fatal Error
- Một file dính cú pháp Parse Error / Fatal Error sẽ bị chấm **0 điểm** cho toàn bộ chức năng của file đó, dù bạn đã viết đúng 90% logic bên dưới.
- Nếu một tính năng khó đang bị lỗi cú pháp chưa fix kịp và sắp hết giờ, hãy comment toàn bộ đoạn code lỗi đó lại để các chức năng khác vẫn chạy bình thường.

### Quy tắc 3: Làm theo thứ tự ưu tiên ROI (Tỷ suất điểm / thời gian)
1. **Ưu tiên 1 (Dễ lấy điểm nhất):** Kết nối PDO + Hiển thị danh sách bảng (SELECT) + Form Thêm dữ liệu (INSERT).
2. **Ưu tiên 2:** Chức năng Xóa (DELETE) + Sửa (UPDATE).
3. **Ưu tiên 3:** Tìm kiếm (Search) & Lọc (Filter).
4. **Ưu tiên 4:** Phân trang hoặc JavaScript Fetch API.
5. **Ưu tiên 5 (Dành cho điểm 9-10):** SQL Báo cáo phức tạp (Nhiều JOIN, HAVING, xử lý đồng hạng).

### Quy tắc 4: Khi nào nên "Bỏ qua tạm thời"?
- Nếu bạn mất quá 5 phút cho một lỗi (ví dụ: không hiểu sao câu UPDATE không chạy, hoặc hàm JS Fetch không bắt được sự kiện): **DỪNG LẠI NGAY LẬP TỨC**.
- Chuyển sang làm câu tiếp theo để gom trọn điểm của các phần khác. Sau khi đã nắm chắc 7 điểm trong tay, quay lại sửa lỗi với tâm lý cực kỳ thoải mái.

---

## 3. Quy Trình Test Chức Năng 7 Bước Siêu Tốc

Trước khi chuyển sang câu tiếp theo, hãy chạy thử 7 test case sau trong vòng 60 giây:

1. **Test Rỗng (Empty Input):** Bấm Submit khi form hoàn toàn để trống -> Kiểm tra xem có văng thông báo lỗi validation không, có bị lưu dòng trắng vào CSDL không.
2. **Test Số 0:** Nhập giá trị là `0` vào ô số lượng / điểm số -> Đảm bảo hệ thống không nhầm là để trống.
3. **Test Nháy Đơn (SQL Injection):** Nhập tên `Patrick O'Connor` hoặc `' OR 1=1 --` -> Đảm bảo câu lệnh PDO không bị crash và dữ liệu được lưu nguyên vẹn.
4. **Test XSS:** Nhập tên `<script>alert(1)</script>` -> Xem trên bảng hiển thị có bị nhảy popup hay không (nếu hiện nguyên dòng chữ `<script>...` là ĐÚNG).
5. **Test Trùng Khóa (Duplicate):** Thử thêm một email hoặc mã sinh viên đã tồn tại -> Kiểm tra thông báo trùng lặp thân thiện, không làm sập trang.
6. **Test URL Injection:** Sửa tham số trên thanh địa chỉ trình duyệt `?page=-1` hoặc `?sort=malicious_sql` -> Kiểm tra xem trang có tự fallback về giá trị mặc định an toàn không.
7. **Test Chuyển Hướng (Auth Guard):** Đăng xuất rồi copy URL trang quản trị dán vào tab mới -> Đảm bảo hệ thống bắt quay về `login.php`.

---

## 4. Checklist 5 Phút Cuối Cùng Trước Khi Nộp Bài

- [ ] **Bật hiển thị lỗi PHP để tự kiểm tra lần cuối:**
  ```php
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  ```
  *(Đảm bảo không còn bất kỳ dòng `Notice`, `Warning` hay `Deprecated` nào xuất hiện trên màn hình).*
- [ ] **Xóa bỏ toàn bộ code debug:** Tìm kiếm và xóa tất cả `var_dump()`, `print_r()`, `die()`, `echo "111"` còn sót lại.
- [ ] **Kiểm tra file cấu hình Database:** Đảm bảo username/password kết nối CSDL đúng với yêu cầu của giám khảo (thường là `root` và password rỗng `""` trên Laragon).
- [ ] **Export file CSDL:** Dùng phpMyAdmin bấm `Export` file `.sql` đặt vào thư mục nộp bài nếu đề yêu cầu nộp kèm CSDL.
- [ ] **Kiểm tra cấu trúc thư mục nộp:** Đảm bảo đúng tên định dạng quy định của phòng thi (ví dụ: `MSSV_HoTen/`).
- [ ] **Nén file và kiểm tra dung lượng:** Nén file dạng `.zip` (hoặc `.rar`), mở file zip kiểm tra xem có đầy đủ source code bên trong không trước khi nộp lên hệ thống.
