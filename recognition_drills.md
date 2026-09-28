# BÀI TẬP NHẬN DIỆN MẪU ĐỀ THI NHANH (RECOGNITION DRILLS)
> **Mục tiêu:** Đọc yêu cầu đề bài và gọi tên kỹ thuật xử lý trong **dưới 10 giây**. Không chần chừ, không nhầm lẫn công nghệ.

---

## Cách Luyện Tập
1. Lấy tay che cột **"Kỹ thuật / Cú pháp giải quyết"**.
2. Đọc to từng yêu cầu ở cột **"Đề bài yêu cầu..."**.
3. Tự trả lời ngay trong đầu hoặc nói ra miệng kỹ thuật cần dùng.
4. Bỏ tay kiểm tra đáp án. Nếu sai hoặc mất $> 10$ giây, đánh dấu ❌ để luyện lại.

---

## BẢNG 30 TÌNH HUỐNG NHẬN DIỆN PHẢN XẠ NHANH

| STT | Đề bài yêu cầu... | Kỹ thuật / Cú pháp giải quyết ngay trong đầu |
|:---:|---|---|
| **01** | "Hiển thị cả khách hàng chưa từng mua đơn nào" | **`LEFT JOIN`** (kết hợp `COUNT(bảng_phụ.id)` hoặc `WHERE bảng_phụ.id IS NULL`) |
| **02** | "Chỉ lấy các nhóm có tổng doanh thu trên 50 triệu" | **`GROUP BY ... HAVING SUM(...) >= 50000000`** (Không dùng WHERE) |
| **03** | "Người dùng có thể truyền tham số cột sắp xếp trên URL" | **Whitelist Array Check**: `in_array($_GET['sort'], ['id', 'price', ...], true)` |
| **04** | "Tìm kiếm tức thì ngay sau khi người dùng dừng gõ phím 300ms" | **Debounce** (`clearTimeout` + `setTimeout 300ms`) + **Fetch API** |
| **05** | "Tìm kiếm từ khóa có chứa ký tự `%` hoặc `_` như ký tự chữ bình thường" | **Escape Wildcard**: `addcslashes($kw, '%_')` và `LIKE :kw ESCAPE '\\'` |
| **06** | "Ngăn chặn người dùng bấm F5 bị gửi lại form và tạo trùng bản ghi" | **PRG Pattern (Post / Redirect / Get)**: Xử lý POST xong `header('Location: ...'); exit;` |
| **07** | "Bắt lỗi hệ thống khi thêm mới một email hoặc mã số đã tồn tại" | Bắt ngoại lệ PDO: `catch (PDOException $e) { if ($e->getCode() === '23000') ... }` |
| **08** | "Tìm sinh viên có điểm trung bình cao nhất trường (kể cả có nhiều người bằng điểm)" | **Subquery MAX**: `HAVING avg_score = (SELECT MAX(...) FROM ...)` (Cấm dùng LIMIT 1) |
| **09** | "Khi sang trang 2 hoặc đổi cột sắp xếp, các điều kiện lọc và tìm kiếm không bị mất" | **`http_build_query(array_merge($_GET, ['page' => $newPage]))`** |
| **10** | "Giữ lại giá trị người dùng vừa gõ trên form sau khi bấm gửi bị báo lỗi" | **Sticky Form**: `value="<?= htmlspecialchars($oldValue, ENT_QUOTES, 'UTF-8') ?>"` |
| **11** | "Chỉ người dùng có quyền Admin mới được xem trang này, người khác bị chặn" | **Auth / Role Guard**: Kiểm tra `$_SESSION['user']['role'] === 'admin'`, nếu không thì redirect kèm `exit;` |
| **12** | "Tự động đổi tên file upload để tránh trùng lặp và không bị ghi đè" | **`uniqid('prefix_', true) . '.' . $ext`** hoặc `hash('sha256', ...)` |
| **13** | "Định nghĩa một lớp cha bắt buộc các lớp con phải tự viết hàm tính lương" | **`abstract class`** với **`abstract public function calculateSalary(): float;`** |
| **14** | "Định nghĩa một bộ quy chuẩn chung để nhiều lớp khác nhau bắt buộc tuân theo" | **`interface`** (dùng từ khóa `implements`) |
| **15** | "Sắp xếp một danh sách các đối tượng sách theo giá tiền giảm dần" | **`usort($books, fn($a, $b) => $b->getPrice() <=> $a->getPrice())`** (Toán tử Spaceship) |
| **16** | "Tự động nạp file class theo chuẩn PSR-4 mà không cần require thủ công từng file" | **`spl_autoload_register()`** hoặc cấu hình `psr-4` trong `composer.json` rồi `dump-autoload` |
| **17** | "Hiển thị dữ liệu trả về từ AJAX lên giao diện an toàn không bị tiêm mã JavaScript" | Gán vào thuộc tính **`.textContent`** (hoặc `document.createElement`), KHÔNG dùng `.innerHTML` |
| **18** | "Tính tổng doanh thu của toàn bộ kết quả lọc, không phải tổng của trang hiện tại" | Chạy một câu query **`SUM()` riêng biệt** trên cùng điều kiện `WHERE` trước khi áp dụng `LIMIT / OFFSET` |
| **19** | "Tìm kiếm tên tiếng Việt không phân biệt hoa thường trong mảng PHP" | **`mb_stripos($haystack, $needle) !== false`** |
| **20** | "Bảo vệ file log không bị ghi đè hỏng khi nhiều request upload ghi cùng thời điểm" | Cờ khóa file: **`file_put_contents($file, $entry, FILE_APPEND \| LOCK_EX)`** |
| **21** | "Đếm số đơn hàng của khách nhưng khách chưa mua gì phải ra số 0 thay vì 1" | **`COUNT(o.id)`** (Đếm theo cột của bảng phụ), TUYỆT ĐỐI KHÔNG DÙNG `COUNT(*)` |
| **22** | "Nếu khách hàng chưa mua gì, tổng tiền mua hiển thị 0 đ thay vì để trống (NULL)" | **`COALESCE(SUM(o.total_price), 0)`** hoặc `IFNULL()` |
| **23** | "Người dùng nhập `page = -5` hoặc `page = 999999` trên URL phân trang" | **Clamp Range**: `$page = max(1, min($page, $totalPages))` |
| **24** | "Thông báo 'Thêm thành công' chỉ xuất hiện 1 lần duy nhất sau khi redirect rồi biến mất" | **Flash Message**: Lưu vào `$_SESSION['flash']`, đọc xong gọi `unset($_SESSION['flash'])` |
| **25** | "Ghi nhớ tên đăng nhập của người dùng trên trình duyệt trong 7 ngày" | **Cookie**: `setcookie('remember_user', $val, time() + 7 * 86400, '/')` |
| **26** | "Kiểm tra định dạng file upload xem có thực sự là ảnh hay là mã độc đổi đuôi .php.jpg" | **`finfo_file()`** hoặc **`mime_content_type()`** kiểm tra MIME type thực tế |
| **27** | "Form nhập số lượng nhưng khi người dùng gõ số 0 lại bị code báo là chưa nhập" | Lỗi dùng hàm `empty()`! Phải sửa thành so sánh chuỗi rỗng: **`$qty === ''`** |
| **28** | "Tìm danh sách các danh mục hiện KHÔNG có bất kỳ sản phẩm nào" | `LEFT JOIN ... WHERE products.id IS NULL` hoặc **`NOT EXISTS (...)`** |
| **29** | "Khi dùng PDO LIMIT và OFFSET bị lỗi cú pháp trong MySQL" | Bind tham số ép kiểu số nguyên rõ ràng: **`$stmt->bindValue(':limit', $limit, PDO::PARAM_INT)`** |
| **30** | "Đề bài đổi từ Quản lý Sinh viên sang Bán Vé Máy Bay, Cửa Hàng Bánh, Garage Xe" | **Bản chất kỹ thuật không đổi!** Chỉ là ánh xạ tên bảng (`students` $\rightarrow$ `tickets`), giữ nguyên toàn bộ kiến trúc pipeline! |
