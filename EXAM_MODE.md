# KỶ LUẬT THI THỬ THỰC CHIẾN (EXAM MODE)
> **Mục đích:** Tạo môi trường áp lực mô phỏng 100% điều kiện phòng thi thật. Nếu bạn vi phạm các quy tắc này khi làm Mock Exam, kết quả điểm số sẽ không phản ánh đúng năng lực khi đi thi!

---

## 1. 5 Điều Cấm Kỵ Tuyệt Đối Trong Giờ Thi

1. **KHÔNG XEM SOLUTION:** Tuyệt đối không mở thư mục `solutions/` hoặc xem đáp án trước/trong khi làm bài.
2. **KHÔNG XEM THEORY:** Không mở lại các file `theory.md`. Bạn chỉ được phép mở duy nhất file [CHEATSHEET.md](file:///d:/MNM/OnTap/opensource_exam_training/CHEATSHEET.md) trong 5 phút trước khi bấm giờ bắt đầu.
3. **KHÔNG COPY CODE CŨ:** Tự tay gõ từng dòng code vào VS Code. Gõ bằng tay giúp xây dựng trí nhớ cơ bắp (muscle memory).
4. **KHÔNG DÀNH THỜI GIAN CHO CSS:** Nói KHÔNG với Bootstrap, Tailwind, flexbox, chỉnh màu mè. Dùng thẻ HTML thuần (`<table>`, `<form>`, `<input>`, `<button>`).
5. **KHÔNG GIAN LẬN THỜI GIAN:** Hết đúng 60 phút là phải dừng bàn phím ngay lập tức.

---

## 2. Kịch Bản Đồng Hồ 60 Phút

### ⏰ Phút 00 – 03: Đọc Đề & Lựa Chọn Thứ Tự
- Không gõ code vội! Dành trọn vẹn 3 phút đọc lướt từ Câu 1 đến câu cuối cùng.
- Nhận diện các bẫy: Cột nào cần Whitelist? Có yêu cầu `LEFT JOIN` không? Có bắt trùng email không?
- Xác định thứ tự chiến thuật: Làm phần dễ kiếm điểm trước (PDO kết nối + SELECT hiển thị + Form INSERT).

### ⏰ Phút 03 – 55: Tập Trung Cao Độ Gõ Code
- Bám sát luồng tư duy chuẩn (đã học trong `EXAM_PATTERN_MAP.md`).
- Vừa viết vừa test nhanh từng tính năng. Xong câu nào kiểm tra chạy được câu đó ngay.
- Nếu kẹt ở một lỗi logic quá 5 phút: Comment tạm lại và nhảy sang câu khác để gom điểm.

### ⏰ Phút 55 – 60: Dừng Code Mới – Chỉ Test & Sửa Lỗi
- **QUY TẮC BẤT DI BẤT DỊCH:** Tuyệt đối không viết thêm bất kỳ chức năng mới nào trong 5 phút cuối!
- Chạy checklist an toàn:
  - Bật hiển thị lỗi PHP (`display_errors = 1`) kiểm tra xem có dính `Notice` hay `Warning` không.
  - Xóa sạch các lệnh debug `var_dump()`, `print_r()`.
  - Thử nhập nháy đơn `Patrick O'Connor` xem câu query có sập không.
  - Thử nhập `<script>alert(1)</script>` xem có bị popup XSS không.
  - Kiểm tra xem đã có `exit;` sau các lệnh `header('Location: ...')` chưa.
  - Nén thư mục bài làm đúng quy định của giám thị.
