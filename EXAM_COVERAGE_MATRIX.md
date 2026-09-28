# MA TRẬN PHỦ KIẾN THỨC ĐỀ THI (EXAM COVERAGE MATRIX)
> **Nguyên tắc vàng:** Hai đề mẫu hiện có chỉ là mẫu đại diện để suy ra phạm vi kiến thức. **Không bao giờ loại bỏ một chủ đề ra khỏi kế hoạch ôn tập chỉ vì nó chưa xuất hiện dưới dạng một câu hỏi độc lập trong đề mẫu!**

---

## 1. Bảng Ma Trận So Sánh & Mức Độ Ưu Tiên

| Kỹ năng / Chủ đề | Đề mẫu 1 | Đề mẫu 2 | Khả năng ghép trong Đề 6Q | Mức độ ưu tiên | Tỷ trọng dự kiến |
|---|:---:|:---:|---|:---:|:---:|
| **PHP Cơ bản / Form / Validation** | Có (Câu 1) | Gián tiếp | Ghép với PDO Insert hoặc Session login | 🔴 **Cao** | 10% - 15% |
| **Session / Cookie / Auth Guard** | Có (Câu 2) | Có (Câu 2) | Ghép với Protected Page, File Upload | 🔴 **Cao** | 10% - 15% |
| **Upload File & I/O Log** | Có (Câu 3) | Có (Câu 3) | Ghép với Form nhập liệu hoặc Thống kê log | 🔴 **Cao** | 10% - 15% |
| **OOP (Abstract, Interface, usort)** | Có (Câu 4) | Có (Câu 4) | Ghép với Namespace/Autoload hoặc JSON | 🔴 **Cao** | 15% - 20% |
| **Namespace & Autoload (PSR-4)** | Có (Ghép) | Có (Ghép) | Ghép với OOP Model / Repository | 🔴 **Cao** | 5% - 10% |
| **JavaScript Fetch API & AJAX JSON** | Có (Câu 5) | Có (Câu 5) | Ghép với Realtime Search từ DB hoặc Mảng PHP | 🔴 **Cao** | 15% - 20% |
| **PDO Prepared Statements & CRUD** | Có (Câu 6) | Có (Câu 6) | Ghép với Search, PRG Pattern, Bắt trùng khóa | 🟣 **Rất cao** | 20% - 25% |
| **SQL JOIN (INNER / LEFT JOIN)** | Có (Câu 6) | Có (Câu 6) | Ghép với Báo cáo hoặc Tìm kiếm quan hệ | 🟣 **Rất cao** | 15% - 20% |
| **GROUP BY / HAVING / Aggregate** | Có | Có | Ghép với Thống kê doanh thu / Ties for Max | 🟣 **Rất cao** | 15% - 20% |
| **Search, Sort Whitelist, Pagination**| Có | Có | Ghép với PDO Repository | 🔴 **Cao** | 15% - 20% |
| **Bảo mật (XSS, SQLi, Whitelist, PRG)**| Xuyên suốt | Xuyên suốt | Xuất hiện ở MỌI câu hỏi từ nhập liệu đến DB | 🟣 **Rất cao** | Tiêu chí trừ điểm toàn bài |

---

## 2. Phân Tích Sự Chuyển Đổi Sang Format 6 Câu / 60 Phút

Khi đề thi chuyển sang format **6 câu độc lập trong 60 phút**:
1. **Mỗi câu sẽ tập trung và tinh gọn hơn:** Không yêu cầu dựng cả một hệ thống lớn hoàn chỉnh, mà tập trung kiểm tra một kỹ năng cốt lõi hoặc một chuỗi ghép 2 kỹ năng trực tiếp.
2. **Khả năng cao của 6 câu trong đề thật:**
   - **Câu 1:** Form xử lý dữ liệu, sticky form, validate, tính toán logic (5 – 7 phút).
   - **Câu 2:** Session / Cookie: Login guard, đếm số lần truy cập hoặc bảo vệ trang (6 – 8 phút).
   - **Câu 3:** OOP: Xây dựng class/abstract class, kế thừa, interface, sắp xếp mảng đối tượng (8 – 10 phút).
   - **Câu 4:** Upload file an toàn (kiểm tra MIME/ext/size, lưu tên ngẫu nhiên, ghi log append) (7 – 9 phút).
   - **Câu 5:** JavaScript Fetch API + Debounce: Tìm kiếm động lấy dữ liệu JSON từ file PHP (7 – 9 phút).
   - **Câu 6:** PDO + SQL (JOIN / GROUP BY / Phân trang / Báo cáo) (8 – 10 phút).
3. **Hoặc đề sẽ ghép theo cặp kỹ năng:**
   - Câu 1: Form + PDO Insert (bắt trùng mã, PRG).
   - Câu 2: Session Auth + Upload File có phân quyền.
   - Câu 3: OOP + PSR-4 Autoload.
   - Câu 4: AJAX Debounce + PHP PDO Search trả JSON.
   - Câu 5: SQL Query phức tạp (LEFT JOIN + GROUP BY + HAVING).
   - Câu 6: Phân trang danh sách dữ liệu có tìm kiếm và whitelist sort.

---

## 3. Kết Luận Chiến Lược Ôn Tập
- Không được học tủ đề mẫu!
- Rèn luyện theo **Pattern (Khuôn mẫu phản xạ)**: Khi đọc một yêu cầu, xác định ngay nó thuộc nhóm nào và gọi ra khung code chuẩn đã định hình trước.
