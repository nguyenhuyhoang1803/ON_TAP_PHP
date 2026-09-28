# LỘ TRÌNH ÔN THI CẤP TỐC (ROADMAP)
> Chiến lược: **Tối đa hóa Điểm số / Thời gian học**. Đảm bảo chắc chắn 7–8 điểm trước, bứt phá 9–10 điểm khi làm chủ biến thể và edge cases.

---

## Ma trận Trọng số Đề thi 60 Phút

| Nhóm nội dung | Tỷ trọng điểm | Mức độ bẫy | Thời gian thi khuyến nghị |
|---|:---:|:---:|:---:|
| **PDO CRUD & Prepared Statements** | 30% - 35% | Cao (SQLi, duplicate email, PRG) | 12 - 15 phút |
| **SQL (JOIN, GROUP BY, Aggregate)** | 20% - 25% | Rất cao (NULL logic, HAVING, LEFT JOIN) | 10 - 12 phút |
| **Form, Session & Auth Guard** | 15% - 20% | Trung bình (Empty vs 0, thiếu `exit`) | 8 - 10 phút |
| **Search, Pagination & Whitelist** | 15% - 20% | Cao (Query string, injection `ORDER BY`) | 10 - 12 phút |
| **JavaScript Fetch API & Realtime** | 10% - 15% | Trung bình (Debounce, textContent XSS) | 7 - 10 phút |
| **OOP & Namespace / Composer** | 10% - 15% | Trung bình (Abstract vs Interface, PSR-4) | 8 - 10 phút |

---

## 7 Giai đoạn Học Cấp tốc

### GIAI ĐOẠN 1: NỀN TẢNG SỐNG CÒN (Bắt buộc phải nhanh như phản xạ)
- **Mục tiêu:** Viết form xử lý, validation, session guard không quá 5 phút.
- **Modules:**
  - `01_php_basic`: Cú pháp, kiểu dữ liệu, toán tử `??`, foreach mảng kết hợp, hàm.
  - `02_form_validation`: Phân biệt `empty($x)` với `$x === ''` (bẫy số 0), `trim()`, sticky form, `htmlspecialchars()`.
  - `03_session_cookie`: `session_start()`, cấu trúc kiểm tra đăng nhập `login_check`, `header("Location: ...")` + `exit`, đếm lượt truy cập, cookie ghi nhớ tài khoản.
  - `04_upload_file`: Bắt lỗi `$_FILES['error']`, MIME check, kiểm tra đuôi mở rộng, `uniqid()`, `file_put_contents` với `FILE_APPEND | LOCK_EX`.
- **Checkpoint sát hạch:** Mini Test 1 & Mini Test 2.

---

### GIAI ĐOẠN 2: PDO + SQL CỐT LÕI (Xương sống chiếm 50% điểm thi)
- **Mục tiêu:** Kết nối PDO chuẩn UTF-8, dẹp bỏ 100% nguy cơ SQL Injection bằng Prepared Statement; viết thành thạo JOIN và thống kê.
- **Modules:**
  - `08_pdo_crud`: Cấu hình PDO Exception, `prepare()`, `execute()`, `fetch()`, `fetchAll(PDO::FETCH_ASSOC)`, `bindValue()`, CRUD hoàn chỉnh, kỹ thuật Post-Redirect-Get (PRG), xử lý lỗi trùng khóa (Duplicate Key).
  - `09_sql_core`: Khắc cốt ghi tâm sự khác nhau giữa `INNER JOIN` và `LEFT JOIN` (tìm khách hàng chưa có đơn, sản phẩm chưa từng bán), `GROUP BY`, `HAVING` (lọc sau khi gom nhóm), các hàm `COUNT()`, `SUM()`, `AVG()`, `ROUND()`, `MIN()`, `MAX()`, subquery cơ bản.
- **Checkpoint sát hạch:** Mini Test 5 & Mini Test 6.

---

### 🔄 SPACED REVIEW 1: TRỘN NỀN TẢNG & DATABASE
> **Bài tập tích hợp:** Viết hệ thống quản lý danh bạ hoặc điểm sinh viên có Form POST, Session thông báo Flash Message, PDO Insert có kiểm tra trùng mã/email, và hiển thị danh sách có `LEFT JOIN` lấy tên lớp.

---

### GIAI ĐOẠN 3: SEARCH, SORT, FILTER & PHÂN TRANG CHUẨN MỰC
- **Mục tiêu:** Xử lý trang danh sách chuyên nghiệp, an toàn tuyệt đối với bẫy SQLi ở `ORDER BY`, phân trang mượt mà không mất từ khóa tìm kiếm.
- **Modules:**
  - `10_search_sort_pagination`:
    - Tìm kiếm nhiều trường (`WHERE name LIKE :kw OR email LIKE :kw`), escape ký tự đặc biệt `%` và `_`.
    - Whitelist cột sắp xếp và chiều `ASC`/`DESC`.
    - Thuật toán phân trang: `ceil($total / $limit)`, tính `offset = ($page - 1) * $limit`, kẹp giá trị `page` hợp lệ (clamp `max(1, min($page, $totalPages))`).
    - Giữ tham số trên URL bằng `http_build_query($_GET)`.
- **Checkpoint sát hạch:** Thử thách xây dựng trang tìm kiếm sản phẩm kết hợp lọc danh mục + phân trang.

---

### GIAI ĐOẠN 4: JAVASCRIPT / AJAX / REALTIME SEARCH
- **Mục tiêu:** Xây dựng tính năng Live Search (gõ tới đâu tìm tới đó) không tải lại trang, có debounce chống spam server, render bảng an toàn với `textContent`.
- **Modules:**
  - `07_javascript_ajax_json`:
    - Lắng nghe sự kiện `input` trên ô tìm kiếm.
    - Kỹ thuật `debounce` (300ms) bằng `clearTimeout` và `setTimeout`.
    - Gửi request GET bằng `fetch()` có `encodeURIComponent()`.
    - PHP trả dữ liệu dạng `json_encode()` với header `Content-Type: application/json`.
    - Bắt lỗi mạng qua `.catch()` hoặc `try/catch`.
    - Tạo DOM động, phòng ngừa XSS trong JavaScript bằng `document.createElement()` và `.textContent` (nói KHÔNG với `.innerHTML` cho dữ liệu do user nhập).
- **Checkpoint sát hạch:** Mini Test 4 (Live search sách / sinh viên).

---

### 🔄 SPACED REVIEW 2: TÍCH HỢP AJAX + PDO + SECURITY
> **Bài tập tích hợp:** Xây dựng ô Live Search sản phẩm bằng JS Fetch gọi về endpoint PHP truy vấn PDO có phân trang & chống SQLi, trả JSON, render ra bảng kèm highlight từ khóa tìm kiếm.

---

### GIAI ĐOẠN 5: LẬP TRÌNH HƯỚNG ĐỐI TƯỢNG (OOP PHP)
- **Mục tiêu:** Thiết kế đúng kiến trúc class, hiểu rõ ranh giới giữa Abstract class và Interface, áp dụng đa hình không nhầm lẫn.
- **Modules:**
  - `05_oop`:
    - Class, thuộc tính, phương thức, constructor promotion (PHP 8).
    - `public`, `protected`, `private`, Getter/Setter.
    - Kế thừa (`extends`), lớp trừu tượng (`abstract class`, `abstract method`).
    - Giao diện (`interface`, `implements`), toán tử kiểm tra kiểu `instanceof`.
    - Thuộc tính/phương thức tĩnh (`static`, `self::`, `parent::`).
    - Xử lý mảng đối tượng: sắp xếp danh sách đối tượng bằng `usort()`.
- **Checkpoint sát hạch:** Mini Test 3.

---

### GIAI ĐOẠN 6: NAMESPACE, AUTOLOAD & COMPOSER
- **Mục tiêu:** Hiểu nguyên lý phân giải namespace thành đường dẫn file, cấu hình Composer PSR-4 chạy trơn tru trong 2 phút.
- **Modules:**
  - `06_namespace_autoload`:
    - Khái niệm `namespace` và từ khóa `use`.
    - Tự viết hàm `spl_autoload_register()` chuyển đổi `App\Models\User` thành `src/Models/User.php`.
    - Cấu hình file `composer.json` chuẩn PSR-4.
    - Lệnh sống còn: `composer dump-autoload` (khi tạo file class mới hoặc sửa namespace).
    - Nhúng `vendor/autoload.php` vào file chạy chính.
    - Dependency Injection cơ bản qua constructor (truyền kết nối PDO vào Repository/Model).

---

### GIAI ĐOẠN 7: SQL BÁO CÁO NÂNG CAO, BẢO MẬT TOÀN DIỆN & LUYỆN ĐỀ 60 PHÚT
- **Mục tiêu:** Nuốt trọn các câu 9–10 điểm: báo cáo doanh thu phức tạp, xử lý đồng hạng (ties), lọc theo khoảng ngày kết hợp `LEFT JOIN`, phòng vệ mọi lỗ hổng trước khi nộp bài.
- **Modules:**
  - `11_sql_reporting`: Thống kê khách hàng chi tiêu nhiều nhất, danh mục bán chạy nhất, xử lý các trường hợp NULL bằng `IFNULL()` / `COALESCE()`.
  - `12_security`: Checklist phòng thủ 5 lớp: SQLi, Reflected XSS, Stored XSS, Path Traversal, File upload shell, URL tampering.
- **Luyện 4 Đề Thi Thử (Mock Exams):**
  - `mock_01`: Mức dễ (-20%) - Rèn luyện phản xạ bấm giờ 45-50 phút hoàn thành.
  - `mock_02`: Mức tiêu chuẩn - Cấu trúc tương đương 100% đề thi chính thức.
  - `mock_03`: Mức đổi bối cảnh - Đổi dữ liệu sang bài toán khác để chống học tủ.
  - `mock_04`: Mức nâng cao (+15%) - Có edge cases hiểm hóc để cán mốc 10 tuyệt đối.
