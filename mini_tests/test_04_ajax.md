# MINI TEST 04: JAVASCRIPT FETCH API & REALTIME DEBOUNCE
- **Thời gian làm bài:** 15 phút
- **Tổng điểm:** 10 điểm
- **Mục tiêu:** Kiểm tra kỹ năng xây dựng ô tìm kiếm tức thì bằng JS Fetch API, kỹ thuật debounce 300ms, phòng chống XSS bằng `textContent` và xử lý trạng thái Loading/Empty.

---

## Đề Bài: Tìm Kiếm Điện Thoại Di Động Realtime
Xây dựng tính năng tìm kiếm realtime sản phẩm điện thoại gồm 2 file:
1. `api_phones.php`:
   - Endpoint nhận tham số qua GET `kw` (từ khóa).
   - Thiết lập header HTTP: `Content-Type: application/json; charset=utf-8`.
   - Có sẵn mảng dữ liệu mẫu (hoặc query PDO) gồm ít nhất 6 dòng: `id`, `name`, `brand`, `price`, `ram_gb`.
   - Tìm kiếm không phân biệt chữ hoa/thường theo `name` HOẶC `brand`.
   - Nếu từ khóa rỗng (`kw === ''`), trả về toàn bộ danh sách.
   - Trả về JSON bằng `json_encode($result)`.
2. `search_phone.html`:
   - Giao diện có ô input `id="keywordInput"` và một bảng `<table>` hiển thị kết quả.
   - Có dòng hiển thị trạng thái `id="loadingStatus"` ("Đang tải...").
   - Kỹ thuật Debounce 300ms: Khi người dùng gõ vào ô input, chỉ khi dừng gõ đủ 300ms mới gửi request Fetch đến `api_phones.php?kw=...`.
   - Bắt buộc dùng `encodeURIComponent()` khi ghép từ khóa vào URL.
   - Xóa bảng cũ trước khi render dữ liệu mới.
   - Render từng dòng bảng bằng `document.createElement()` và gán giá trị bằng `.textContent` để triệt tiêu XSS.
   - Nếu không có kết quả phù hợp, hiển thị dòng: "Không tìm thấy điện thoại nào phù hợp!".
   - Xử lý bắt lỗi qua `try/catch` để nếu mất mạng thì thông báo lỗi lên giao diện.

---

## Test Cases & Rubric Chấm Điểm (10 điểm)

| Tiêu chí | Điểm | Test Case Kiểm Thử |
|---|:---:|---|
| **Backend API JSON** | 2.5đ | Trả về JSON chuẩn, có header application/json, lọc đúng theo tên/hãng |
| **Kỹ thuật Debounce 300ms** | 2.5đ | Gõ liên tiếp 5 phím thật nhanh -> Server chỉ nhận đúng 1 request cuối |
| **Mã hóa URI** | 1.0đ | Tìm kiếm `iPhone 15 & Plus` -> Server nhận đúng toàn bộ chuỗi |
| **Bảo mật XSS (DOM textContent)**| 2.5đ | Thử thêm sản phẩm có tên `<script>alert(1)</script>` -> Hiện đúng chữ, không popup |
| **Trạng thái Loading & Empty** | 1.5đ | Hiện "Đang tải" khi fetch, hiện thông báo rỗng khi không có kết quả |

---
*(Xem lời giải chi tiết tại [mini_tests/solutions/test_04_solution.md](file:///d:/MNM/OnTap/opensource_exam_training/mini_tests/solutions/test_04_solution.md))*
