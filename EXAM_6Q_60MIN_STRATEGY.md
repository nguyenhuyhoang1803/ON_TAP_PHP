# CHIẾN THUẬT PHÒNG THI: 6 CÂU TRONG 60 PHÚT (EXAM 6Q/60MIN STRATEGY)
> **Mục tiêu:** Tối ưu hóa 60 phút để đạt 8.5 – 10.0 điểm mà không bị hoảng loạn, không thiếu thời gian, và không bị điểm liệt vì lỗi ngớ ngẩn.

---

## 1. Bản Chất Format Đề Thi 6 Câu / 60 Phút

- **Tổng số câu:** 6 câu.
- **Tổng thời gian:** 60 phút.
- **Thời gian trung bình lý thuyết:** 10 phút / câu.
- **Thời gian thực tế khả dụng:**
  - Đọc đề & phân loại: **3 – 4 phút**
  - Thời gian code thực tế: **48 – 50 phút** (bình quân **8 – 9 phút / câu**)
  - Rà soát, test & sửa lỗi cuối giờ: **6 – 7 phút**

> [!WARNING]
> **Không bao giờ giả định 6 câu có trọng số điểm bằng nhau!**  
> Đề thi thường phân bổ: Câu 1, 2 (1.0 – 1.5đ), Câu 3, 4 (1.5 – 2.0đ), Câu 5, 6 (2.0 – 2.5đ). Đôi khi một câu dễ lại bằng điểm một câu khó. Vì vậy, nguyên tắc sống còn là **ăn trọn vẹn điểm các câu dễ trước, tuyệt đối không làm dở dang tất cả các câu**.

---

## 2. Dòng Thời Gian Vàng (Timeline 60 Phút Chuẩn)

```text
 0m ────────── 4m ────────────────────────────── 40m ──────────── 53m ───────── 60m
 [ Đọc & Scan ] [ Giải quyết Nhóm A & Nhóm B ]   [ Nhóm C / Vét ] [ Test & Freeze ]
```

### Phút 0 – 4: Đọc quét & Phân loại A - B - C (Bắt buộc)
Mở đề thi ra, **KHÔNG GÕ CODE NGAY LẬP TỨC**. Dành trọn 4 phút đọc lướt qua cả 6 câu. Đánh dấu ngay vào nháp:
- **Nhóm A (Ăn điểm nhanh - ≤ 7 phút):** Câu quen thuộc, nhìn thấy ngay hướng làm (Ví dụ: Form validation, Session Guard, SQL JOIN cơ bản, PDO Select).
- **Nhóm B (Chắc chắn nhưng cần suy nghĩ - 8 – 10 phút):** Cần tư duy một chút về thuật toán, luồng dữ liệu hoặc cấu trúc (Ví dụ: Phân trang, AJAX Debounce, OOP Hierarchy + usort, Upload file + unique name).
- **Nhóm C (Dài hoặc Khó - > 10 phút):** Đề dài, yêu cầu thống kê phức tạp nhiều bảng, xử lý chuỗi đặc thù hoặc câu kết hợp nhiều kỹ năng.

### Phút 4 – 40: Quét sạch Nhóm A, chuyển sang Nhóm B
- Làm toàn bộ các câu nhóm A trước. Mục tiêu: gom trọn **4 – 5 điểm đầu tiên trong 20-25 phút**.
- Chuyển sang nhóm B. Viết code dứt khoát, test nhanh tại chỗ.
- Đạt mốc phút 40: Bạn đã phải cầm chắc **6.5 – 8.0 điểm an toàn**.

### Phút 40 – 53: Xử lý Nhóm C & Vét điểm
- Tập trung vào câu nhóm C hoặc các phần tính năng nâng cao còn thiếu.
- **Chiến lược ăn điểm từng phần (Partial Credit):** 
  - Nếu đề yêu cầu báo cáo phức tạp: Viết xong câu SQL chuẩn, bind PDO và in ra bảng trước (đã được 70% số điểm câu đó).
  - Nếu câu AJAX phức tạp: Viết endpoint PHP trả JSON chuẩn trước, sau đó mới viết `fetch()` hiển thị DOM.
  - Tuyệt đối không để trắng câu nào.

### Phút 53 – 60: ĐÓNG BĂNG TÍNH NĂNG MỚI – Chỉ Test & Sửa Lỗi
> [!CAUTION]
> **Quy tắc bất di bất dịch: KHÔNG BẮT ĐẦU TÍNH NĂNG LỚN MỚI SAU PHÚT 53!**  
> Việc cố gõ thêm một câu mới trong 5 phút cuối thường dẫn đến:
> 1. Syntax Error làm hỏng cả trang.
> 2. Mất tập trung, không kịp kiểm tra 5 câu đã làm.
> 3. Bị trừ điểm hàng loạt vì các lỗi ngớ ngẩn (quên `exit`, XSS, SQLi).

---

## 3. Checklist 7 Phút Cuối (Phút 53 – 60)

| Kiểm tra | Thao tác cần làm | Nguy cơ nếu quên |
|---|---|---|
| **Syntax Error** | Chạy thử từng file trên trình duyệt / CLI | Bị 0 điểm toàn câu nếu file báo lỗi Fatal / Parse Error |
| **XSS Prevention** | Mọi chỗ `echo $variable` ra HTML đều bọc `htmlspecialchars()` | Mất điểm bảo mật (thường trừ 0.5 - 1.0đ) |
| **SQL Injection** | Kiểm tra toàn bộ câu SQL: 100% dùng Prepared Statement (`:placeholder`) | Bị đánh trượt hoặc trừ điểm nặng lỗi bảo mật |
| **Session & Redirect** | Mọi lệnh `header('Location: ...');` phải có ngay `exit;` bên dưới | Lỗi header already sent hoặc bypass redirect |
| **Mở Session** | Mọi file dùng `$_SESSION` phải có `session_start();` ở đầu file | Session trống, code logic sai |
| **Whitelist** | Cột Sort và Direction từ `$_GET` phải qua `in_array()` | Lỗi SQL Injection ẩn qua Order By |
| **Đường dẫn & File** | Kiểm tra đường dẫn include/require, thư mục upload có tồn tại | Lỗi file not found khi chạy trên máy chấm |

---

## 4. Tâm Lý & Phản Xạ Khi Gặp Biến Thể Đề

1. **Khi thấy đề lạ:**  
   Bóc tách câu hỏi về bản chất kỹ thuật:
   - "Tìm sinh viên điểm cao nhất" $\equiv$ "Tìm khách hàng mua nhiều nhất" $\equiv$ `HAVING total = (SELECT MAX(...))`
   - "Đăng ký tiêm chủng" $\equiv$ "Đặt vé máy bay" $\equiv$ Form validate + PDO Insert + Bắt trùng Unique Key.
2. **Khi gặp bug không chạy:**
   - Dành tối đa **2 phút** debug. Dùng `var_dump()` hoặc `print_r()` tại từng bước để xem dữ liệu rẽ nhánh ở đâu.
   - Nếu quá 2 phút không tìm ra: **Comment đoạn lỗi lại**, cho hàm trả về giá trị mặc định để các phần khác vẫn chạy, chuyển sang câu tiếp theo!
3. **Tuyệt đối không gõ lại code từ đầu khi gặp lỗi:** Sửa tại chỗ hoặc khoanh vùng lại.
