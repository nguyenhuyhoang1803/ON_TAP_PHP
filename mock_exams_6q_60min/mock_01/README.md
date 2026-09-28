# ĐỀ THI THỬ 6 CÂU 60 PHÚT - MOCK 01
> **Chủ đề:** Hệ thống Quản lý Thư viện Sách (`books`, `categories`, `borrowers`)  
> **Thời gian:** 60 phút | **Tổng điểm:** 10.0 điểm  
> **Mức độ:** Dễ hơn đề thật một chút (-15%) – Mục tiêu rèn luyện tốc độ hoàn thành trọn vẹn 6 câu.

---

## CẤU TRÚC ĐỀ BÀI (6 CÂU)

### Câu 1 (1.5 điểm) - Form Nhập & Validate Sách mới (Mục tiêu: ≤ 7 phút)
Tạo file `c1_add_book.php`:
- Form gồm: `title` (Tên sách), `price` (Giá tiền), `category_id` (Danh mục: dropdown gồm 1: Công nghệ, 2: Kinh tế, 3: Văn học).
- Xử lý khi submit POST:
  - `title`: Không được rỗng sau khi trim.
  - `price`: Bắt buộc là số, không âm ($\ge 0$).
  - `category_id`: Bắt buộc phải là 1 trong 3 giá trị `[1, 2, 3]`.
- Nếu có lỗi: Hiển thị danh sách lỗi bằng màu đỏ, giữ lại dữ liệu đã nhập trên form (Sticky Form).
- Nếu hợp lệ: Hiển thị thông báo thành công: `"Thêm thành công sách [Tên sách] - Giá: [Giá đã format tiền tệ VND]"` (chống XSS).

### Câu 2 (1.5 điểm) - Quản lý Phiên Đăng Nhập Thủ Thư (Mục tiêu: ≤ 7 phút)
Tạo 2 file `c2_login.php` và `c2_dashboard.php`:
- `c2_login.php`: Cho phép đăng nhập với `username` và `password`. Tài khoản mặc định hợp lệ: `admin` / `lib123`.
  - Nếu đúng: Lưu thông tin vào `$_SESSION['librarian'] = ['user' => 'admin', 'login_at' => time()]` và redirect sang `c2_dashboard.php`.
  - Nếu sai: Hiển thị lỗi `"Sai tên đăng nhập hoặc mật khẩu"`.
- `c2_dashboard.php`: Phải có Auth Guard. Nếu chưa đăng nhập, lập tức redirect về `c2_login.php` (kèm `exit;`).
  - Nếu đã đăng nhập: Hiển thị lời chào `"Xin chào admin! Đăng nhập lúc: [hh:mm:ss dd/mm/YYYY]"`. Có nút "Đăng xuất" xóa session và quay về trang login.

### Câu 3 (1.5 điểm) - Upload Bìa Sách & Ghi Log (Mục tiêu: ≤ 8 phút)
Tạo file `c3_upload_cover.php`:
- Form upload file input `cover_image`.
- Validate:
  - Chỉ chấp nhận định dạng: `jpg`, `png`, `webp`.
  - Dung lượng tối đa: 1.5MB.
- Xử lý:
  - Đổi tên file ngẫu nhiên dạng: `cover_[unique_id].[ext]`.
  - Lưu file vào thư mục `covers/`.
  - Ghi 1 dòng vào file `upload_history.log` (có khóa `LOCK_EX`): `[Y-m-d H:i:s] - File: [tên mới] - Size: [số bytes] bytes`.

### Câu 4 (1.5 điểm) - Xây dựng Mô Hình OOP Sách & Đa Hình (Mục tiêu: ≤ 9 phút)
Tạo file `c4_oop_publication.php`:
- Khai báo `abstract class Publication`:
  - Thuộc tính `protected string $title`, `protected float $basePrice`.
  - Constructor gán 2 thuộc tính trên.
  - Phương thức trừu tượng: `abstract public function calculateRentalFee(): float;`
- Tạo class `PhysicalBook extends Publication`:
  - Thuộc tính phụ `private float $shippingFee`.
  - Triển khai `calculateRentalFee()` = `$basePrice * 0.1 + $shippingFee`.
- Tạo class `EBook extends Publication`:
  - Triển khai `calculateRentalFee()` = `$basePrice * 0.05` (không có phí ship).
- Tạo mảng gồm 3 cuốn sách (2 PhysicalBook, 1 EBook). Dùng `usort` sắp xếp mảng theo phí thuê (`calculateRentalFee`) giảm dần. Duyệt mảng in ra: Tên sách và Phí thuê.

### Câu 5 (2.0 điểm) - AJAX Realtime Tìm Kiếm Sách (Mục tiêu: ≤ 9 phút)
Tạo 2 file `c5_search_api.php` và `c5_search_view.html`:
- `c5_search_api.php`: Khởi tạo sẵn một mảng PHP mô phỏng 5 cuốn sách (gồm `id`, `title`, `author`, `price`). Nhận tham số GET `keyword`. Lọc các cuốn sách có `title` chứa `keyword` (không phân biệt hoa thường). Trả về JSON với header chuẩn UTF-8.
- `c5_search_view.html`: Có 1 ô input tìm kiếm. Dùng Vanilla JS lắng nghe sự kiện `input`, áp dụng kỹ thuật Debounce 300ms, gọi `fetch()` tới `c5_search_api.php`. Hiển thị kết quả dưới dạng danh sách `<ul><li>` bằng `textContent` an toàn. Nếu rỗng hiển thị thông báo "Không tìm thấy".

### Câu 6 (2.0 điểm) - SQL Core Thống Kê Sách Theo Danh Mục (Mục tiêu: ≤ 8 phút)
Cho 2 bảng:
- `categories(id, category_name)`
- `books(id, category_id, title, price, quantity)`
Tạo file `c6_query.sql` viết 2 câu truy vấn SQL:
1. *(1.0 điểm)* Lấy danh sách tất cả các danh mục, kèm theo: Tổng số lượng sách (`total_quantity`), Giá trung bình (`avg_price`), Kể cả danh mục chưa có sách nào cũng phải xuất hiện trong kết quả (nếu chưa có sách thì tổng là 0, giá TB là 0). Sắp xếp theo tổng số sách giảm dần.
2. *(1.0 điểm)* Lấy các danh mục có tổng số sách $\ge 50$ cuốn và giá trung bình trên 80,000 đ.

---

## TIÊU CHÍ CHẤM & RUBRIC (10 ĐIỂM)

| Tiêu chí | Trọng số | Yêu cầu |
|---|:---:|---|
| **Độ chính xác (Correctness)** | 50% (5.0đ) | Chạy đúng các yêu cầu nghiệp vụ của đề. |
| **Bảo mật (Security)** | 20% (2.0đ) | Đủ `htmlspecialchars`, Prepared Statement, Whitelist, Không hở path. |
| **Xử lý biên (Edge Cases)** | 15% (1.5đ) | Xử lý rỗng, giá âm, định dạng file lạ, chia cho 0, NULL aggregate. |
| **Cấu trúc & Cú pháp** | 15% (1.5đ) | Code sạch, đúng chuẩn PHP 8, có `exit` sau redirect, đúng kiểu trả về. |

---

## BỘ TEST CASES TỰ ĐÁNH GIÁ
1. **Câu 1:** Nhập price = `0` $\rightarrow$ Form phải hợp lệ, không báo lỗi thiếu giá. Nhập price = `-50` $\rightarrow$ Báo lỗi. Nhập title = `<script>` $\rightarrow$ Không được thực thi script khi echo.
2. **Câu 2:** Chưa login mà truy cập trực tiếp `c2_dashboard.php` $\rightarrow$ Ngay lập tức bị đẩy về login.
3. **Câu 3:** Thử đổi tên 1 file `.txt` thành `.php` rồi upload $\rightarrow$ Bị từ chối. Upload ảnh 500KB $\rightarrow$ Đổi tên ngẫu nhiên, ghi log thành công.
4. **Câu 4:** Kết quả in ra đúng thứ tự phí thuê từ cao nhất đến thấp nhất.
5. **Câu 5:** Gõ liên tục 5 ký tự $\rightarrow$ Chỉ gửi 1 request mạng sau khi ngừng gõ 300ms.
6. **Câu 6:** Danh mục không có sách hiển thị `0` chứ không phải `NULL`. Dùng đúng `HAVING` cho câu b.
