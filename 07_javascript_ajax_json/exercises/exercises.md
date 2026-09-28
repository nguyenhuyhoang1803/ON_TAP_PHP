# BÀI TẬP RÈN LUYỆN: JAVASCRIPT FETCH API & JSON

## Đề bài: Tra Cứu Điểm Thi Trực Tuyến
Xây dựng module tra cứu điểm thi:
1. File `api_check_score.php`:
   - Nhận mã thí sinh qua GET `sbd`.
   - Kết nối PDO kiểm tra trong CSDL.
   - Trả về JSON: `{ "found": true, "name": "...", "math": 8.5, "physics": 9.0, "chemistry": 7.5, "total": 25.0 }` hoặc `{ "found": false, "message": "Không tìm thấy số báo danh!" }`.
2. File `check_score.html`:
   - Giao diện có ô nhập Số báo danh và nút "Tra Cứu".
   - Bắt sự kiện click nút (hoặc nhấn phím Enter).
   - Gọi Fetch API, xử lý trạng thái Loading ("Đang tra cứu dữ liệu...").
   - Hiển thị kết quả tra cứu vào thẻ `<div id="scoreResult">` một cách an toàn.
