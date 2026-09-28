# ĐỀ THI THỬ 6 CÂU 60 PHÚT - MOCK 04
> **Chủ đề:** Nền tảng Bán Khóa Học Trực Tuyến (`courses`, `instructors`, `enrollments`, `reviews`)  
> **Thời gian:** 60 phút | **Tổng điểm:** 10.0 điểm  
> **Đặc điểm nổi bật:** **CHUYÊN ĐỀ BẪY XỬ LÝ BIÊN & BẢO MẬT (EDGE CASES & SECURITY TEST)**. Các test cases được thiết kế để phát hiện ngay các lỗ hổng XSS, SQL Injection, Sort Injection, lỗi chia cho 0, và tràn số.

---

## CẤU TRÚC ĐỀ BÀI (6 CÂU)

### Câu 1 (1.5 điểm) - Form Đăng Ký Khóa Học & Mã Giảm Giá (Mục tiêu: ≤ 8 phút)
Tạo file `c1_course_enroll.php`:
- Nhận input POST: `student_name`, `email`, `course_price` (ẩn hoặc disabled), `discount_code`.
- Danh sách mã giảm giá hợp lệ trong hệ thống: `PHPPRO` (giảm 20%), `FREESHIP` (giảm 50.000 đ), `FULLFREE` (giảm 100%).
- **Edge cases & Security:**
  - `student_name`: Có thể chứa ký tự đặc biệt như `<script>alert(1)</script>`. Bắt buộc phải render an toàn bằng `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
  - `email`: Validate bằng `filter_var($email, FILTER_VALIDATE_EMAIL)`.
  - Tiền thanh toán cuối cùng không bao giờ được phép âm (`max(0, $finalPrice)`). Nếu dùng mã giảm giá vượt quá giá gốc thì tiền trả về 0 đ.
- Xuất hóa đơn chi tiết: Tên học viên, Email, Giá gốc, Số tiền giảm, Số tiền thực trả.

### Câu 2 (1.5 điểm) - Upload Ảnh Giảng Viên Kháng File Nguy Hiểm (Mục tiêu: ≤ 8 phút)
Tạo file `c2_upload_avatar.php`:
- Input upload: `avatar`.
- **Bẫy kiểm tra bảo mật:**
  - Kiểm tra dung lượng file: từ 10KB đến 2MB (từ chối file rỗng 0 byte).
  - Kiểm tra đuôi mở rộng: Chỉ cho phép `['jpg', 'jpeg', 'png', 'webp']`. Bẫy tên file nguy hiểm như `shell.php.png` hoặc chứa `../`.
  - Kiểm tra MIME type thực tế bằng `mime_content_type()` hoặc `finfo_file()`: Chỉ chấp nhận `image/jpeg`, `image/png`, `image/webp`.
  - Sinh tên file hoàn toàn ngẫu nhiên không giữ lại tên gốc của người dùng: `hash('sha256', uniqid() . microtime()) . '.' . $ext`. Lưu vào thư mục `avatars/`.

### Câu 3 (2.0 điểm) - PDO Search & Escape LIKE Wildcard Ký Tự Đặc Biệt (Mục tiêu: ≤ 9 phút)
Cho bảng `courses(id, course_code, course_name, description, price)`.
Tạo file `c3_search_courses.php`:
- Người dùng tìm kiếm theo từ khóa GET `keyword`.
- **Bẫy Edge Case:**
  - Nếu người dùng nhập ký tự `%` hoặc `_`, hệ thống **không được** coi đó là ký tự đại diện (wildcard) của SQL để lấy ra toàn bộ bảng, mà phải tìm chính xác từ khóa có chứa dấu `%` hoặc `_`.
  - Sử dụng hàm escape: `$escapedKeyword = addcslashes($keyword, '%_');` (hoặc `strtr`).
  - Dùng Prepared Statement `LIKE :kw ESCAPE '\\'`.
  - Trả về danh sách kết quả hoặc thông báo: `"Không tìm thấy khóa học phù hợp với từ khóa: [htmlspecialchars(keyword)]"`.

### Câu 4 (1.5 điểm) - Phân Trang Chống Sort Injection & Tràn Trang (Mục tiêu: ≤ 9 phút)
Tạo file `c4_courses_paging.php`:
- Nhận `page`, `sort`, `dir` từ `$_GET`.
- **Bẫy Edge Case & Security:**
  - Test trường hợp `page = -5`, `page = 'abc'`, `page = 99999999`: Phải clamp giá trị `$page = max(1, min($page, $totalPages))`.
  - Test tấn công Sort Injection: `sort = sleep(5)` hoặc `sort = price; DROP TABLE`: Bắt buộc Whitelist các cột được phép sắp xếp: `['id', 'price', 'created_at']`. Hướng sắp xếp chỉ chấp nhận `ASC` hoặc `DESC`.
  - Bind `limit` và `offset` bắt buộc ép kiểu `PDO::PARAM_INT` để tránh MySQL syntax error khi ở chế độ `EMULATE_PREPARES = false`.

### Câu 5 (1.5 điểm) - OOP Đánh Giá Khóa Học & Bẫy Chia Cho Không (Mục tiêu: ≤ 8 phút)
Tạo file `c5_oop_course_review.php`:
- Class `Review`: thuộc tính `private float $rating`, `private string $comment`. Validate `$rating` phải trong khoảng `[1.0, 5.0]`, ném ngoại lệ `InvalidArgumentException` nếu sai.
- Class `Course`:
  - Thuộc tính `private string $name`, `private array $reviews = []`.
  - Phương thức `addReview(Review $review)`.
  - Phương thức `getAverageRating(): float`:
    - **Bẫy:** Nếu khóa học chưa có review nào (`empty($reviews)`), phương thức phải trả về `0.0`, **tuyệt đối không để xảy ra lỗi Division by Zero** (`$sum / 0`).
  - Phương thức `getRatingSummary(): string`: trả về chuỗi `"Khóa học: [Tên] | Điểm TB: [x.x] (tổng [N] lượt đánh giá)"`.

### Câu 6 (2.0 điểm) - SQL Core Doanh Thu Khóa Học & Phân Hạng (Mục tiêu: ≤ 8 phút)
Cho 2 bảng:
- `instructors(id, instructor_name, email)`
- `enrollments(id, instructor_id, course_price, status)` -- status: 'active', 'refunded'
Tạo file `c6_advanced_reporting.sql`:
1. *(1.0 điểm)* Lấy danh sách giảng viên kèm: Tổng số lượt đăng ký thành công (`status = 'active'`), Tổng tiền thu được (`revenue`), Tổng tiền đã hoàn trả (`refund_amount`). Giảng viên chưa có học viên nào thì các cột số tiền hiển thị `0`.
2. *(1.0 điểm)* Tìm giảng viên có doanh thu thuần cao nhất (`revenue - refund_amount`) trong số các giảng viên có ít nhất 1 học viên active. Sử dụng Subquery tìm MAX xử lý đồng hạng.

---

## TIÊU CHÍ ĐÁNH GIÁ ĐẶC THÙ MOCK 04
- Nếu dính bất kỳ lỗi bảo mật nào (XSS, SQLi qua sort, bỏ qua whitelist, không escape `%` trong LIKE): **Trừ trực tiếp 1.0đ - 1.5đ mỗi lỗi**.
- Nếu code bị lỗi Fatal do Division by Zero hoặc Exception không được bắt: **0đ câu đó**.
