# BÀI TẬP RÈN LUYỆN: UPLOAD VÀ XỬ LÝ FILE

## Đề bài: Thư Viện Tài Liệu Học Tập
Xây dựng trang `doc_manager.php`:
1. Cho phép tải lên các tài liệu có phần mở rộng: `pdf`, `docx`, `pptx` (tối đa 5MB).
2. Lưu các file vào thư mục `documents/` với tên không trùng lặp.
3. Tạo file `documents/metadata.json` chứa danh sách các tài liệu đã upload:
   - `id`, `original_name`, `stored_name`, `file_size`, `uploaded_at`.
4. Hiển thị bảng danh sách các tài liệu đã tải lên kèm dung lượng (tính theo KB) và link để tải/xem file.
