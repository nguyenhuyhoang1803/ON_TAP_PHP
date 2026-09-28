# BẢNG THEO DÕI TIẾN ĐỘ & TỐC ĐỘ HỌC TẬP (PROGRESS)

> **Quy ước mức độ thành thạo:**  
> 🔴 **Chưa học** | 🟠 **Đang yếu (< 70%)** | 🟡 **Tạm ổn (70% - 84%)** | 🟢 **Chắc chắn (85% - 94%)** | 🔵 **Exam Ready (Hoàn thành $\le$ Target Time)**  
> 
> [!IMPORTANT]
> **Một chủ đề CHỈ ĐƯỢC ĐÁNH DẤU 🔵 Exam Ready khi thỏa mãn đủ 4 tiêu chí:**
> 1. Code chạy chính xác yêu cầu logic.
> 2. Vượt qua 100% các Test Cases (kể cả Edge Cases).
> 3. Tự viết từ đầu, **KHÔNG xem solution / gợi ý**.
> 4. Hoàn thành trong giới hạn **Target Time** (Thời gian mục tiêu).

---

## 1. Bảng Theo Dõi Tiến Độ & Mục Tiêu Tốc Độ (Speed Targets)

| Module | Chủ đề | Hiểu | Code được | Thời gian | Target Time | Exam Ready | Ghi chú & Lỗi cần lưu ý |
|:---:|---|:---:|:---:|:---:|:---:|:---:|---|
| **01** | PHP Form, Sticky & Data Validation | ⏳ | ⏳ | -- | **$\le$ 5 phút** | 🔴 Chưa | Chú ý bẫy `empty()` với số 0 |
| **02** | Session, Auth Guard & Remember Cookie| ⏳ | ⏳ | -- | **$\le$ 7 phút** | 🔴 Chưa | Chú ý `exit;` ngay sau `header('Location')` |
| **03** | Upload File An Toàn & Append Log | ⏳ | ⏳ | -- | **$\le$ 8 phút** | 🔴 Chưa | MIME check, uniqid & `LOCK_EX` |
| **04** | OOP Cơ Bản (Abstract, Interface, usort)| ⏳ | ⏳ | -- | **$\le$ 8 phút** | 🔴 Chưa | Spaceship `<=>`, chia cho 0 |
| **05** | Namespace, Autoload & PSR-4 | ⏳ | ⏳ | -- | **$\le$ 5 phút** | 🔴 Chưa | `spl_autoload_register` |
| **06** | JavaScript AJAX Fetch & Debounce 300ms | ⏳ | ⏳ | -- | **$\le$ 8 phút** | 🔴 Chưa | `encodeURIComponent`, DOM `.textContent` |
| **07** | PDO Prepared Statements (Select/Insert)| ⏳ | ⏳ | -- | **$\le$ 7 phút** | 🔴 Chưa | 100% Prepared Statements |
| **08** | CRUD Task (Duplicate Key 23000 + PRG) | ⏳ | ⏳ | -- | **$\le$ 10 phút** | 🔴 Chưa | Bắt trùng mã / email, PRG redirect |
| **09** | SQL JOIN & GROUP BY + HAVING | ⏳ | ⏳ | -- | **$\le$ 7 phút** | 🔴 Chưa | `LEFT JOIN`, `COALESCE`, `HAVING` sau SUM |
| **10** | Tìm kiếm, Whitelist Sort & Phân trang | ⏳ | ⏳ | -- | **$\le$ 10 phút** | 🔴 Chưa | Clamp `$page`, Whitelist sort, `PDO::PARAM_INT` |
| **11** | Repository Pattern (Search + Count) | ⏳ | ⏳ | -- | **$\le$ 12 phút** | 🔴 Chưa | Tách biệt Database Layer |
| **12** | SQL Báo cáo đa bảng & Thống kê Ties | ⏳ | ⏳ | -- | **$\le$ 10 phút** | 🔴 Chưa | Subquery tìm MAX không dùng `LIMIT 1` |

---

## 2. Kết Quả Thi Thử 6 Câu / 60 Phút (Mock Exams 6Q/60M)

| Đề thi | Mức độ | Thời gian làm | Điểm tổng | Tình trạng | Đánh giá & Khắc phục |
|---|---|:---:|:---:|:---:|---|
| **Mock 01** | Dễ (-15%) | -- phút | -- / 10 | ⏳ Chưa làm | Mục tiêu: Hoàn thành 6 câu trong $\le 50$ phút |
| **Mock 02** | Chuẩn thi thật | -- phút | -- / 10 | ⏳ Chưa làm | Mục tiêu: Đạt $\ge 8.5$ điểm, sạch lỗi bảo mật |
| **Mock 03** | Ghép kỹ năng | -- phút | -- / 10 | ⏳ Chưa làm | Mục tiêu: Xử lý mượt các bài ghép 2 skills |
| **Mock 04** | Edge Cases & Bảo mật| -- phút | -- / 10 | ⏳ Chưa làm | Mục tiêu: Không sập bẫy XSS, Sort Injection, Wildcard |
| **Mock 05** | Mô phỏng thi thật | -- phút | -- / 10 | ⏳ Chưa làm | Mục tiêu: Tự phân tích đề không có nhãn module |

---

## 3. Bảng Phân Tích Chi Tiết Sau Mỗi Mock 6 Câu (Post-Mock Review)

> [!WARNING]
> **Quan trọng:** Một câu làm ra kết quả đúng nhưng **mất 20 phút** vẫn phải đánh dấu là lỗi về mặt tốc độ (**Time Management** hoặc **Syntax Recall**) và bắt buộc phải luyện lại!

### Mẫu bảng ghi nhận sau mỗi đề thi:
| Câu | Chủ đề kỹ thuật | Điểm đạt | Thời gian | Lỗi mắc phải (nếu có) | Phân loại lỗi |
|:---:|---|:---:|:---:|---|---|
| **1** | PHP / Form | -- / 1.5 | -- phút | | ⏳ |
| **2** | Session / Auth | -- / 1.5 | -- phút | | ⏳ |
| **3** | File Upload / Log | -- / 1.5 | -- phút | | ⏳ |
| **4** | OOP / Interface | -- / 1.5 | -- phút | | ⏳ |
| **5** | AJAX Fetch / JSON | -- / 2.0 | -- phút | | ⏳ |
| **6** | SQL / PDO Data | -- / 2.0 | -- phút | | ⏳ |
| **Tổng**| **Toàn bộ đề thi** | **-- / 10** | **-- / 60m**| | |

### 7 Nhóm Phân Loại Lỗi Chuẩn:
1. **Knowledge Gap (Hổng kiến thức):** Chưa từng biết hàm hoặc khái niệm này trước đây.
2. **Syntax Recall (Quên cú pháp):** Biết hướng làm nhưng phải khựng lại nhớ cú pháp lệnh (mất 2 - 3 phút).
3. **Logic (Sai luồng tư duy):** Nhầm thứ tự xử lý, nhầm thuật toán, điều kiện if/else sai.
4. **SQL (Lỗi cơ sở dữ liệu):** Sai JOIN, nhầm lẫn giữa WHERE và HAVING, sai cú pháp subquery.
5. **Security (Lỗi bảo mật):** Quên escape HTML, quên Prepared statement, hở sort injection, thiếu `exit`.
6. **Time Management (Quản lý thời gian):** Sa đà vào 1 câu khó $> 12$ phút làm bỏ lỡ các câu dễ phía sau.
7. **Careless Mistake (Lỗi ẩu):** Sai chính tả tên biến, thiếu dấu chấm phẩy, nhầm `POST` thành `GET`.

---

## 4. Tình Trạng Năng Lực Hiện Tại (Cập Nhật Liên Tục)

- **Top 3 điểm mạnh:** *(Sẽ được xác định ngay sau Baseline Test)*
- **Top 3 điểm yếu:** *(Sẽ được xác định ngay sau Baseline Test)*
- **Top 3 phần tốn thời gian nhất:** *(Sẽ được xác định ngay sau Baseline Test)*
- **Thời gian trung bình 1 câu:** -- phút
- **Điểm thi dự kiến hiện tại:** **-- / 10**
