# ĐỀ THI THỬ 01 (MOCK EXAM 01)
> **Mức độ:** Dễ hơn đề chuẩn 20% (Mục tiêu: Hoàn thành trong 45 phút, rèn luyện phản xạ tốc độ và độ chính xác).  
> **Thời gian:** 60 phút | **Tổng điểm:** 10.0 điểm | **Hình thức:** Thực hành trên máy

---

## Bối Cảnh Bài Toán: Quản Lý Sản Phẩm Văn Phòng Phẩm
Bạn được giao nhiệm vụ xây dựng module quản lý sản phẩm cho cửa hàng văn phòng phẩm "Minh Khang". CSDL mẫu đã được tạo sẵn trong file `schema.sql`.

---

## CÁC YÊU CẦU ĐỀ THI

### Câu 1: Kết Nối CSDL & Hiển Thị Danh Sách (2.5 điểm)
- Viết file `connect.php` tạo kết nối PDO đến CSDL `mock01_stationery` với `charset=utf8mb4` và `ERRMODE_EXCEPTION`.
- Viết file `index.php` hiển thị bảng danh sách sản phẩm gồm các cột: `Mã sản phẩm`, `Tên sản phẩm`, `Danh mục`, `Giá bán` (format tiền tệ VNĐ), `Số lượng tồn kho`.
- Tên danh mục phải được lấy từ bảng `categories` thông qua mệnh đề `INNER JOIN`.
- Chống XSS an toàn khi xuất dữ liệu ra HTML bằng `htmlspecialchars()`.

### Câu 2: Thêm Mới Sản Phẩm & Xử Lý Trùng Mã (3.5 điểm)
- Viết trang `create.php` chứa Form thêm sản phẩm: `Mã sản phẩm` (input text), `Tên sản phẩm` (input text), `Danh mục` (select box đổ dữ liệu từ bảng `categories`), `Giá bán` (input number), `Số lượng` (input number).
- **Validation:**
  - Tất cả các trường đều bắt buộc nhập.
  - Giá bán phải là số thực $> 0$.
  - Số lượng phải là số nguyên $\ge 0$ (Lưu ý: Không được báo lỗi khi nhập số lượng là `0`).
- **Thực thi:**
  - Dùng Prepared Statement thực hiện `INSERT`.
  - Bắt lỗi ngoại lệ `23000` nếu mã sản phẩm bị trùng lặp -> Báo lỗi đỏ thân thiện: *"Mã sản phẩm đã tồn tại!"*.
  - Áp dụng mẫu PRG: Sau khi thêm thành công, redirect về `index.php?msg=added`.

### Câu 3: Xóa Sản Phẩm An Toàn (2.0 điểm)
- Trong bảng ở `index.php`, thêm cột "Hành động" có nút / link "Xóa" cho từng sản phẩm.
- Viết file `delete.php?id=X`:
  - Lấy `id` từ `$_GET`, ép kiểu số nguyên an toàn.
  - Thực hiện xóa bằng Prepared Statement `DELETE FROM products WHERE id = :id`.
  - Sau khi xóa, chuyển hướng về `index.php?msg=deleted`.

### Câu 4: Tìm Kiếm Cơ Bản Theo Tên (2.0 điểm)
- Tại trang `index.php`, bổ sung ô input tìm kiếm và nút "Tìm".
- Tìm kiếm gần đúng theo tên sản phẩm (`name LIKE :kw`).
- Nếu từ khóa rỗng, hiển thị toàn bộ sản phẩm.
- Giữ lại từ khóa vừa tìm kiếm trong ô input (Sticky search box).

---

## PHẦN DÀNH CHO GIÁM THỊ: BẪY & THAY ĐỔI DỮ LIỆU
1. **Giám thị nhập số lượng là `0`:** Thí sinh nào dùng `empty($_POST['quantity'])` sẽ bị trừ 1.0 điểm vì hệ thống báo lỗi không cho nhập số 0.
2. **Giám thị thêm sản phẩm có mã trùng:** Kiểm tra hệ thống có bắt mã lỗi `23000` hay văng màn hình cam Fatal Error.
3. **Giám thị nhập tên sản phẩm là `<script>alert(1)</script>`:** Kiểm tra xem trang có bị popup XSS hay không.

---
*(Xem schema tại [schema.sql](file:///d:/MNM/OnTap/opensource_exam_training/mock_exams/mock_01/schema.sql) và lời giải tại [solutions/solution.md](file:///d:/MNM/OnTap/opensource_exam_training/mock_exams/mock_01/solutions/solution.md))*
