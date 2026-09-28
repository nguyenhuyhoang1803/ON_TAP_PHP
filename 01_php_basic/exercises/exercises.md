# BÀI TẬP RÈN LUYỆN: PHP CĂN BẢN

## Bài 1: Quản lý học lực sinh viên
Cho mảng sinh viên gồm `mssv`, `fullname`, `diem_qt` (hệ số 0.4), `diem_thi` (hệ số 0.6).
- Tính điểm trung bình môn: `dtb = diem_qt * 0.4 + diem_thi * 0.6` (làm tròn 2 chữ số thập phân).
- Xếp loại:
  - `dtb >= 8.5`: "Xuất sắc"
  - `7.0 <= dtb < 8.5`: "Khá"
  - `5.0 <= dtb < 7.0`: "Trung bình"
  - `dtb < 5.0`: "Yếu"
- Tìm sinh viên có điểm trung bình cao nhất (xử lý trường hợp có thể nhiều người cùng cao nhất).

### Edge Cases cần xử lý:
- Điểm số là chuỗi số: `"8.5"` (cần ép kiểu `(float)`).
- Mảng sinh viên rỗng (`$students = []`): Không để chương trình bị crash lỗi chia cho 0.
