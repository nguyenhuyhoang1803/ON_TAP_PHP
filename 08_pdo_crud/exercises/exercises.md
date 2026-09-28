# BÀI TẬP RÈN LUYỆN: PDO CRUD HOÀN CHỈNH

## Đề bài: Quản Lý Danh Sách Sách Thư Viện
Tạo cơ sở dữ liệu `lib_management` và bảng `books`:
- `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `isbn` (VARCHAR(20), UNIQUE, NOT NULL)
- `title` (VARCHAR(255), NOT NULL)
- `author` (VARCHAR(100), NOT NULL)
- `price` (DECIMAL(10,2), NOT NULL)
- `quantity` (INT, DEFAULT 1)

### Yêu cầu chức năng:
1. `index.php`: Hiển thị danh sách tất cả các cuốn sách dưới dạng bảng HTML. Có nút "Thêm sách mới", cột "Hành động" gồm 2 nút "Sửa" và "Xóa".
2. `add_book.php`: Form thêm sách mới. Validate bắt buộc nhập đầy đủ, giá và số lượng phải là số không âm. Bắt ngoại lệ PDO khi trùng ISBN. Áp dụng PRG chuyển về `index.php?msg=added`.
3. `edit_book.php?id=X`: Lấy thông tin sách theo ID hiển thị lên form để chỉnh sửa. Sau khi update thành công, chuyển về `index.php?msg=updated`.
4. `delete_book.php?id=X`: Thực hiện xóa sách theo ID an toàn bằng Prepared Statement.
