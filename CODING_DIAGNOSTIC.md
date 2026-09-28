# BÀI THỰC HÀNH ĐỊNH VỊ NĂNG LỰC CODE (CODING DIAGNOSTIC)
> **Thời gian:** Đúng 20 phút (Mỗi task 5 phút) | **Hình thức:** Tự code vào file nháp hoặc gõ trực tiếp câu trả lời | **Mục tiêu:** Kiểm tra tốc độ gõ code và phản xạ cú pháp chuẩn xác dưới áp lực thời gian.  
> ⚠️ **Quy tắc:** Không mở file lời giải trước khi tự code xong!

---

## ⏱️ TASK A (5 PHÚT) – FORM POST + VALIDATION + HTMLSPECIALCHARS
**Đề bài:** Viết một file PHP `task_a.php` gồm cả khối xử lý PHP và khối hiển thị HTML:
1. Nhận form phương thức POST gồm 3 trường:
   - `fullname`: Bắt buộc nhập, tối thiểu 3 ký tự.
   - `email`: Bắt buộc nhập, đúng định dạng email.
   - `quantity`: Số lượng hàng (Bắt buộc nhập, số nguyên $\ge 0$. Lưu ý: Nhập số `0` là hợp lệ, không được báo lỗi rỗng).
2. Nếu có lỗi: Hiển thị danh sách lỗi màu đỏ và **giữ lại dữ liệu cũ** trong các ô input (Sticky form).
3. Nếu hợp lệ: In ra thông báo thành công: "Đăng ký thành công cho: [fullname] - [email] - Số lượng: [quantity]".
4. **Bảo mật:** Bắt buộc escape 100% dữ liệu xuất ra HTML bằng `htmlspecialchars()` với cờ `ENT_QUOTES`.

---

## ⏱️ TASK B (5 PHÚT) – SESSION LOGIN GUARD & FLASH MESSAGE
**Đề bài:** Viết đoạn code PHP đặt ở đầu file `admin.php`:
1. Khởi động phiên làm việc bằng hàm `session_start()`.
2. Kiểm tra nếu `$_SESSION['logged_user']` không tồn tại hoặc rỗng:
   - Chuyển hướng trình duyệt về `login.php?error=unauthorized`.
   - **Bắt buộc:** Dừng ngay lập tức quá trình thực thi của script bằng lệnh sống còn.
3. Nếu đã đăng nhập:
   - Lấy thông báo Flash từ `$_SESSION['flash_msg']` (nếu có) để in ra màn hình, sau đó xóa ngay khỏi session để khi F5 không bị lặp lại.
   - Hiển thị: "Chào mừng, [tên user đã escape HTML]! | `<a href="logout.php">Đăng xuất</a>`".

---

## ⏱️ TASK C (5 PHÚT) – PDO PREPARED STATEMENT SEARCH
**Đề bài:** Giả sử bạn đã có đối tượng kết nối `$pdo` (PDO).
Viết đoạn code PHP:
1. Lấy từ khóa tìm kiếm `$keyword = trim($_GET['kw'] ?? '');`.
2. Xử lý an toàn các ký tự đại diện của `LIKE`: `%` và `_` bằng hàm `strtr()`.
3. Viết câu lệnh Prepared Statement tìm kiếm trong bảng `products` các bản ghi có cột `name LIKE :kw` HOẶC `code LIKE :kw`, sắp xếp theo `id DESC`.
4. Thực thi câu lệnh, lấy danh sách kết quả dạng mảng kết hợp (`PDO::FETCH_ASSOC`) vào biến `$results`.
5. Đảm bảo an toàn 100% trước SQL Injection, kể cả khi từ khóa chứa dấu nháy đơn (`Patrick O'Connor`) hoặc ký tự `%`.

---

## ⏱️ TASK D (5 PHÚT) – SQL LEFT JOIN + GROUP BY + NULL HANDLING
**Đề bài:** Cho 2 bảng trong MySQL:
- `categories` (`id`, `name`)
- `products` (`id`, `name`, `price`, `category_id`)
Viết **MỘT câu lệnh SQL duy nhất** để:
1. Liệt kê: Mã danh mục (`category_id`), Tên danh mục (`category_name`), và Số lượng sản phẩm (`total_products`).
2. **Yêu cầu cốt lõi:** Phải hiển thị đầy đủ cả những danh mục **hiện chưa có bất kỳ sản phẩm nào** (với số lượng hiển thị là `0`).
3. Chỉ lấy các danh mục có từ 2 sản phẩm trở lên HOẶC danh mục chưa có sản phẩm nào.
4. Sắp xếp giảm dần theo số lượng sản phẩm.

---

*(Lời giải chi tiết được lưu riêng biệt tại file [CODING_DIAGNOSTIC_SOLUTION.md](file:///d:/MNM/OnTap/opensource_exam_training/CODING_DIAGNOSTIC_SOLUTION.md))*
