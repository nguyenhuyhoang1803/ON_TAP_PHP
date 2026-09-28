# KẾ HOẠCH LUYỆN TẬP 60 PHÚT MỖI NGÀY (DAILY 60-MIN PLAN)
> **Nguyên tắc:** Mỗi ngày dành trọn vẹn 1 phiên 60 phút tập trung cao độ 100%, bấm giờ nghiêm ngặt, rèn luyện tốc độ và loại bỏ triệt để các lỗi ngớ ngẩn.

---

## 1. Cấu Trúc Khung Giờ Một Buổi Luyện Tập Chuẩn (60 Phút)

```text
 0m ──────── 10m ───────────── 25m ──────────────────────── 45m ────────── 55m ──── 60m
 [ Khởi động ]   [ 2 Speed Drills ]   [ 1 Bài Code Tổng Hợp ]   [ Debug Drill ] [ Log Lỗi ]
```

### Phút 0 – 10: Khởi Động Não & Phản Xạ Nhận Diện (10 Phút)
- Mở file [recognition_drills.md](file:///d:/MNM/OnTap/opensource_exam_training/recognition_drills.md).
- Quét nhanh 15 – 20 câu hỏi tình huống: Che cột đáp án, đọc yêu cầu và gọi tên kỹ thuật trong $< 10$ giây.
- Ôn nhanh 1 – 2 cú pháp boilerplate dễ quên (ví dụ: `PDO connection`, `spl_autoload_register`, `debounce setTimeout`).

### Phút 10 – 25: Tăng Tốc Với 2 Speed Drills (15 Phút)
- Chọn ngẫu nhiên **2 bài Speed Drills** trong thư mục [speed_drills/](file:///d:/MNM/OnTap/opensource_exam_training/speed_drills/README.md) (1 bài 5 phút + 1 bài 7 phút).
- Bấm giờ chạy ngược. Tự gõ code hoàn chỉnh.
- Mục tiêu: Hoàn thành trước khi chuông reo mà không cần sửa lỗi cú pháp.

### Phút 25 – 45: Thực Chiến 1 Bài Tổng Hợp / Blank Code (20 Phút)
- Chọn **1 bài Blank Code Drill** trong [blank_code_drills/](file:///d:/MNM/OnTap/opensource_exam_training/blank_code_drills/README.md) hoặc 1 bài toán ghép 2 kỹ năng (ví dụ: Session + Upload, AJAX + PDO Database, Phân trang).
- Mở file mới hoàn toàn trống, tự viết toàn bộ từ đầu đến cuối.
- Kiểm tra các tiêu chí: Đúng logic $\rightarrow$ Đạt bảo mật (XSS, SQLi) $\rightarrow$ Đạt tốc độ $\le 10$ phút.

### Phút 45 – 55: Tinh Mắt Với Debug Drill (10 Phút)
- Mở **1 bài Debug Drill** trong [debug_drills/](file:///d:/MNM/OnTap/opensource_exam_training/debug_drills/README.md).
- Đọc đoạn code có sẵn, đặt timer 4 phút.
- Tìm đủ từ 2 đến 4 lỗi ẩn (SQLi, sort injection, quên `exit`, sai JOIN/HAVING, XSS). Viết lại code sửa chuẩn.

### Phút 55 – 60: Đúc Kết & Ghi Nhận Lỗi Vào PROGRESS.md (5 Phút)
- Mở file [PROGRESS.md](file:///d:/MNM/OnTap/opensource_exam_training/PROGRESS.md).
- Ghi nhận thời gian thực hiện của từng bài hôm nay.
- Phân loại lỗi mắc phải vào 1 trong 7 nhóm lỗi:
  - *Knowledge Gap / Syntax Recall / Logic / SQL / Security / Time Management / Careless Mistake*.
- Ghi chú bài học rút ra để không tái phạm vào ngày mai.

---

## 2. Giai Đoạn Nước Rút (3 – 5 Ngày Trước Kỳ Thi)

Khi kỳ thi cận kề, **THAY THẾ TOÀN BỘ BUỔI 60 PHÚT** bằng việc thi thử một đề chuẩn:
- Mở 1 đề trong [mock_exams_6q_60min/](file:///d:/MNM/OnTap/opensource_exam_training/mock_exams_6q_60min/mock_01/README.md) (từ Mock 01 đến Mock 05).
- Bấm đúng 60 phút liên tục, không tra cứu tài liệu, không mở solution.
- Hết 60 phút: Chấm điểm theo rubric, điền bảng Post-Mock Review trong `PROGRESS.md`.
