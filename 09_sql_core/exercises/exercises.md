# BÀI TẬP RÈN LUYỆN: SQL CORE

## Bối cảnh CSDL Quản lý Đào tạo:
- `khoa` (`ma_khoa`, `ten_khoa`)
- `lop` (`ma_lop`, `ten_lop`, `ma_khoa`)
- `sinh_vien` (`mssv`, `ho_ten`, `ngay_sinh`, `ma_lop`)
- `diem` (`mssv`, `ma_mon`, `diem_thi`)

## Các câu hỏi thực hành:
1. Viết câu SQL hiển thị: Mã lớp, Tên lớp, Tên khoa, và Sĩ số sinh viên của từng lớp (kể cả lớp chưa có sinh viên nào).
2. Tìm những sinh viên chưa từng có điểm thi ở bất kỳ môn học nào.
3. Tìm những môn học có điểm thi trung bình của sinh viên $\ge 7.0$ và có ít nhất 10 sinh viên tham gia thi.
4. Tìm sinh viên có điểm thi cao nhất trong môn có mã môn là `'CS101'`.
