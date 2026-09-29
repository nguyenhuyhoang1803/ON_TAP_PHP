# WORKSPACE ÔN THI CẤP TỐC: LẬP TRÌNH MÃ NGUỒN MỞ (PHP + MySQL + JS)
> **Mục tiêu:** Thi thực hành 60 phút đạt điểm 8.0 - 10.0 | **Phương pháp:** Hiểu bản chất - Nhớ mẫu tư duy - Phản xạ nhanh - Phòng chống bẫy & lỗi bảo mật.

---

## 1. Giới thiệu Workspace

Workspace này được thiết kế dành riêng cho bạn: người học nhanh, cần lấy lại hoặc củng cố toàn bộ kiến thức môn **Lập trình mã nguồn mở** trong thời gian ngắn nhất, không học vẹt mà nắm chắc bản chất để tự tin xử lý mọi biến thể của đề thi.

Môi trường chuẩn của phòng thi:
- **Server:** Laragon (Apache, MySQL 8.x, PHP 8.x)
- **Database tool:** phpMyAdmin / MySQL CLI
- **Editor:** Visual Studio Code
- **Stack công nghệ:** PHP thuần (no framework), PDO, MySQL (InnoDB, `utf8mb4`), JavaScript (Vanilla, Fetch API, DOM), JSON, Composer (PSR-4 autoloading).

---

## 2. Cấu trúc Thư mục

```text
opensource_exam_training/
│
├── README.md                  # Hướng dẫn tổng quan & quy trình học (BẠN ĐANG Ở ĐÂY)
├── CACH_HOC.md                # Quy trình học từng chủ đề và lịch luyện tập hằng ngày
├── ROADMAP.md                 # Lộ trình 7 giai đoạn học cấp tốc theo mức độ ưu tiên
├── PROGRESS.md                # Bảng theo dõi tiến độ, điểm số checkpoint và điểm yếu
├── CHEATSHEET.md              # Sổ tay tóm tắt cú pháp tối giản đọc 10 phút trước giờ thi
├── COMMON_ERRORS.md           # 27+ lỗi ngớ ngẩn thường gặp, nguyên nhân & cách khắc phục
├── EXAM_STRATEGY.md           # Chiến thuật phân bổ thời gian 60 phút, checklist 5 phút cuối
├── QUICK_REVIEW.md            # Bảng phản xạ: "Thấy đề nói X -> Nghĩ ngay tới Y"
│
├── setup/                     # Kiểm tra môi trường máy thi (PHP, PDO, MySQL, Composer)
│   ├── environment_check.md
│   ├── php_check.php
│   ├── database_check.php
│   └── README.md
│
├── 01_php_basic/              # Module 01: PHP Căn bản (Biến, mảng, hàm, vòng lặp, GET/POST cơ bản)
├── 02_form_validation/        # Module 02: Form & Validation (Empty vs 0, escape HTML, sticky form)
├── 03_session_cookie/         # Module 03: Session & Cookie (Auth, Login Guard, Logout, Flash message)
├── 04_upload_file/            # Module 04: Upload File & Đọc/Ghi file an toàn (MIME, uniqid, LOCK_EX)
├── 05_oop/                    # Module 05: OOP PHP (Class, Abstract, Interface, Polymorphism, Static)
├── 06_namespace_autoload/     # Module 06: Namespace & PSR-4 Autoload (spl_autoload, Composer)
├── 07_javascript_ajax_json/   # Module 07: JS Fetch API & Debounce (Realtime search, DOM textContent)
├── 08_pdo_crud/               # Module 08: PDO Cơ bản & CRUD chuẩn (Prepared statement, PRG pattern)
├── 09_sql_core/               # Module 09: SQL Core (INNER/LEFT JOIN, GROUP BY, HAVING, Subquery)
├── 10_search_sort_pagination/ # Module 10: Tìm kiếm, Lọc, Sắp xếp Whitelist & Phân trang chuẩn
├── 11_sql_reporting/          # Module 11: SQL Báo cáo nâng cao & Thống kê doanh thu / Khách hàng
├── 12_security/               # Module 12: Bảo mật phòng thi (Chống XSS, SQLi, File Upload exploit)
│
├── mini_tests/                # 6 bài kiểm tra nhanh 10-20 phút theo từng cụm chủ đề
├── mock_exams/                # 4 đề thi thử chuẩn 60 phút (Mock 1 -> Mock 4) kèm CSDL & test case
└── sandbox/                   # Thư mục nháp để bạn tự gõ code thử nghiệm tức thì
```

---

## 3. Quy chuẩn mỗi Module Học tập

Mỗi thư mục từ `01` đến `12` đều tuân thủ chặt chẽ cấu trúc:
1. `theory.md`: Tóm tắt bản chất, luồng tư duy, cú pháp cốt lõi, lỗi hay gặp, mẹo nhận diện đề và Mini Challenge.
2. `examples/`: Code mẫu chạy được ngay, tối giản, tuân thủ `readability > clever code`.
3. `exercises/`: Bài tập rèn luyện có phân hóa, kèm test case và edge case.
4. `quiz.md`: Bộ câu hỏi trắc nghiệm / câu hỏi kiểm tra tư duy sâu.
5. `checkpoint.md`: Bài thi sát hạch module (Lý thuyết nhanh + Tìm bug + Viết code + Biến thể). **Phải đạt $\ge 75\%$ mới qua bài**.
6. `solutions/`: Lời giải chi tiết (tách biệt hoàn toàn, không nhìn trước khi tự làm).

---

## 4. Cách học hiệu quả cùng AI Mentor

Khi học bất kỳ bài nào, chúng ta đi qua chu trình 7 bước:
- **Bước 1:** Kiểm tra đầu vào (tối đa 3 câu cực ngắn).
- **Bước 2:** Giải thích bản chất (5–10 phút, tập trung vào "Tại sao? Khi nào dùng?").
- **Bước 3:** Xem code mẫu tối giản.
- **Bước 4:** Bạn tự code bài tập tương tự.
- **Bước 5:** Review code (Bug, Security, Edge case, Tối ưu tốc độ thi).
- **Bước 6:** Đổi biến thể đề để kiểm tra khả năng thích ứng.
- **Bước 7:** Làm bài `checkpoint.md`.

---

## 5. Nguyên tắc Vàng trong Phòng Thi 60 Phút

1. **Điểm số trước, thẩm mỹ sau:** Không viết CSS cầu kỳ trừ khi đề yêu cầu điểm giao diện.
2. **Prepared Statements là bắt buộc:** 100% câu truy vấn có dữ liệu từ người dùng phải dùng `prepare()` + `execute()`.
3. **Luôn `htmlspecialchars()` khi echo ra HTML:** Tránh bẫy XSS mất điểm oan.
4. **Luôn có `exit` sau `header('Location: ...')`:** Phòng chống bypass kiểm tra đăng nhập.
5. **Whitelist cột sắp xếp:** Tuyệt đối không nhét trực tiếp `$_GET['sort']` vào mệnh đề `ORDER BY`.

---

# BẮT ĐẦU Ở ĐÂY

Hãy bắt đầu với [CACH_HOC.md](CACH_HOC.md) để nắm quy trình học, làm **Diagnostic Test** trong `DIAGNOSTIC_TEST.md`, rồi mở [ROADMAP.md](ROADMAP.md) để chọn module phù hợp với phần còn yếu.
