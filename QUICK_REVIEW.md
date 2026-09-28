# BẢNG PHẢN XẠ NHANH: NẾU THẤY ĐỀ NÓI [X] → NGHĨ NGAY TỚI [Y] (QUICK REVIEW)
> Đọc lại bảng này ngay trước khi giám thị phát đề để kích hoạt phản xạ vô điều kiện trong não. Thấy từ khóa là biết ngay cấu trúc code cần gõ!

---

| Khi đề bài yêu cầu... | Nghĩ ngay tới Kỹ thuật / Cú pháp cốt lõi | Mẫu code phản xạ trong 10 giây |
|---|---|---|
| **"Tính tổng / đếm / trung bình theo từng..."** (loại, lớp, phòng ban) | `GROUP BY` | `SELECT dept, COUNT(*), SUM(salary) FROM emp GROUP BY dept;` |
| **"Chỉ hiển thị các nhóm có tổng / số lượng / trung bình thỏa mãn điều kiện..."** | `HAVING` (Tuyệt đối không dùng `WHERE`) | `GROUP BY dept HAVING COUNT(*) >= 5;` |
| **"Hiển thị tất cả danh sách A, kể cả những mục chưa có quan hệ với B"** | `LEFT JOIN` | `FROM customers c LEFT JOIN orders o ON c.id = o.customer_id` |
| **"Tìm các mục chưa từng xuất hiện / chưa từng mua / chưa từng có đơn"** | `LEFT JOIN ... WHERE ... IS NULL` hoặc `NOT EXISTS` | `WHERE o.id IS NULL` hoặc `WHERE NOT EXISTS (SELECT 1 FROM orders ...)` |
| **"Tìm kiếm tức thì / gõ tới đâu lọc tới đó mà không tải lại trang"** | JavaScript `Fetch API` + Backend trả `json_encode()` | `const res = await fetch('api.php?q=...'); const data = await res.json();` |
| **"Sau khi người dùng dừng gõ 300ms mới bắt đầu tìm kiếm"** | Kỹ thuật `debounce` | `clearTimeout(timer); timer = setTimeout(() => search(), 300);` |
| **"Dữ liệu có chứa thẻ HTML hoặc ký tự đặc biệt phải hiển thị nguyên văn"** | `htmlspecialchars()` trong PHP hoặc `.textContent` trong JS | `<?= htmlspecialchars($text, ENT_QUOTES, 'UTF-8') ?>` hoặc `el.textContent = text;` |
| **"Sắp xếp theo cột và thứ tự người dùng chọn từ thanh URL"** | Cơ chế `Whitelist` mảng | `$col = in_array($_GET['sort'], ['id','price']) ? $_GET['sort'] : 'id';` |
| **"Phân trang danh sách dữ liệu (page, limit)"** | Ép kiểu nguyên + Kẹp (Clamp) + Tính `OFFSET` | `$offset = ($page - 1) * $limit;` với `$page = max(1, min($page, $totalPages));` |
| **"Tìm kiếm chính xác ký tự `%` hoặc `_` trong tên"** | Escape ký tự đại diện của `LIKE` | `$kw = strtr($input, ['%' => '\%', '_' => '\_']); LIKE :kw` |
| **"Trang quản trị chỉ cho phép người dùng đã đăng nhập truy cập"** | Session Auth Guard | `session_start(); if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }` |
| **"Đăng xuất khỏi hệ thống"** | Hủy session hoàn toàn | `session_start(); $_SESSION = []; session_destroy(); header('Location: login.php'); exit;` |
| **"Ghi nhớ tên tài khoản trên trình duyệt cho lần đăng nhập sau"** | Cookie với thời hạn tương lai | `setcookie('remember_user', $user, time() + 7*86400, '/');` |
| **"Upload file không được trùng lặp tên làm đè file của người khác"** | Đổi tên bằng `uniqid()` | `$filename = uniqid('file_', true) . '.' . $ext;` |
| **"Chỉ cho phép upload file ảnh jpg, png"** | Kiểm tra đuôi mở rộng + MIME type | `in_array(strtolower(pathinfo($name, PATHINFO_EXTENSION)), ['jpg','png','jpeg'])` |
| **"Ghi thêm log vào cuối file mà không làm mất nội dung cũ"** | `file_put_contents` với cờ `FILE_APPEND` | `file_put_contents('log.txt', $data, FILE_APPEND \| LOCK_EX);` |
| **"Đảm bảo an toàn không bị hỏng file khi nhiều tiến trình ghi đồng thời"** | Cờ khóa file `LOCK_EX` | `file_put_contents('data.txt', $data, LOCK_EX);` |
| **"Tránh việc người dùng bấm F5 bị submit lại dữ liệu trùng lặp"** | Post/Redirect/Get (PRG) Pattern | Xử lý POST xong -> `header('Location: list.php?status=success'); exit;` |
| **"Bắt lỗi khi người dùng thêm mới dữ liệu bị trùng Email hoặc Mã"** | Bắt mã lỗi PDO `23000` (Integrity Constraint) | `catch (PDOException $e) { if ($e->getCode() == 23000) { /* Báo trùng */ } }` |
| **"Liên kết danh mục - sản phẩm nhưng lấy tên danh mục hiển thị"** | `INNER JOIN` (hoặc `LEFT JOIN` nếu danh mục có thể rỗng) | `SELECT p.*, c.name AS cat_name FROM products p JOIN categories c ON p.cat_id = c.id` |
| **"Lớp cha có phương thức bắt buộc các lớp con phải tự định nghĩa"** | `abstract class` + `abstract method` | `abstract public function calculateSalary(): float;` |
| **"Nhiều lớp không cùng huyết thống nhưng đều có chung hành động"** | `interface` + `implements` | `interface Exportable { public function toCsv(): string; }` |
| **"Kiểm tra một đối tượng có phải thuộc lớp con hoặc thực thi giao diện không"** | Toán tử `instanceof` | `if ($item instanceof Discountable) { ... }` |
| **"Tự động load class mà không cần viết hàng chục dòng require"** | `spl_autoload_register()` hoặc Composer PSR-4 | `spl_autoload_register(fn($cls) => require "src/$cls.php");` |
| **"Chuyển hướng trang web sang địa chỉ khác"** | `header('Location: ...')` kèm `exit` | `header('Location: index.php'); exit;` |
| **"Định dạng số tiền hiển thị theo chuẩn Việt Nam"** | Hàm `number_format()` | `number_format($price, 0, ',', '.') . ' VNĐ'` |
