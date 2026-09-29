# CÁCH HỌC VỚI REPO NÀY

Tài liệu này gom cách học trong `README.md`, `ROADMAP.md` và `DAILY_60MIN_PLAN.md` thành một quy trình dễ làm theo. Mục tiêu là tự viết được lời giải và xử lý được đề biến thể, không chỉ nhận ra đáp án khi đọc.

## Bắt đầu từ đâu

1. Làm bài định vị trong [`DIAGNOSTIC_TEST.md`](DIAGNOSTIC_TEST.md) mà chưa xem lời giải. Ghi lại phần nào chưa chắc.
2. Dùng [`ROADMAP.md`](ROADMAP.md) để chọn module ưu tiên. Không cần học theo thứ tự số thư mục: ưu tiên Form/Session, PDO/SQL và Search/Pagination trước; sau đó học AJAX, OOP/Autoload, rồi Reporting và Security.
3. Ghi điểm, thời gian và lỗi vào [`PROGRESS.md`](PROGRESS.md). Chọn tối đa ba điểm yếu để luyện trong một buổi.

## Một vòng học cho mỗi chủ đề

Làm lần lượt các bước sau trước khi chuyển chủ đề:

1. **Đọc lý thuyết có câu hỏi.** Với mỗi kỹ thuật, tự trả lời: dùng khi nào, giải quyết lỗi gì, và nếu bỏ qua thì điều gì xảy ra?
2. **Đóng tài liệu và nhớ lại.** Tự viết các bước hoặc khung code từ trí nhớ. Đánh dấu chỗ quên thay vì nhìn đáp án ngay.
3. **Đọc ví dụ.** So sánh với phần vừa tự viết; giải thích được từng dòng quan trọng.
4. **Tự làm bài tập.** Bắt đầu từ `exercises/` hoặc `blank_code_drills/`. Thử ít nhất một trường hợp biên như dữ liệu rỗng, số 0, ký tự nháy đơn, trang ngoài giới hạn hoặc đầu vào có HTML.
5. **Đối chiếu lời giải sau khi đã thử.** Tìm nguyên nhân sai, sửa bài của mình và ghi một câu nhắc lỗi vào `PROGRESS.md`.
6. **Làm quiz và checkpoint.** Mục tiêu là đạt ít nhất 75%. Nếu chưa đạt, ôn đúng phần sai, làm một bài tương tự rồi thử checkpoint lại.

Đừng chép lời giải ngay khi gặp khó. Thử tự làm trước; nếu bí, xem gợi ý hoặc một phần lời giải, đóng lại rồi tự hoàn thiện từ đầu.

## Buổi luyện tập 60 phút

| Thời gian | Việc cần làm |
|---|---|
| 0–10 phút | Làm `recognition_drills.md`; gọi tên kỹ thuật và nhắc lại một mẫu cú pháp từ trí nhớ. |
| 10–25 phút | Làm hai bài ngắn trong `speed_drills/`, có bấm giờ. |
| 25–45 phút | Tự viết một bài trong `blank_code_drills/` hoặc ghép hai kỹ năng trên file mới. |
| 45–55 phút | Làm một bài trong `debug_drills/`; tìm nguyên nhân rồi sửa. |
| 55–60 phút | Ghi thời gian, lỗi và chủ đề cần ôn vào `PROGRESS.md`. |

Nếu một bài vượt thời gian, ghi lại chỗ vướng và tiếp tục buổi học. Buổi sau luyện đúng kỹ năng đó bằng bài ngắn trước khi làm lại bài tổng hợp.

## Thứ tự ưu tiên khi thời gian có hạn

1. Form validation, session và kiểm tra đăng nhập.
2. PDO CRUD, prepared statements, JOIN, GROUP BY và HAVING.
3. Tìm kiếm, whitelist sắp xếp và phân trang.
4. Fetch/AJAX và xử lý DOM an toàn.
5. OOP, namespace và Composer autoload.
6. SQL báo cáo, bảo mật và các trường hợp biên.
7. Đề thi thử trọn 60 phút.

Thứ tự này giúp nắm phần nền và phần có nhiều kỹ năng dùng chung trước. Dùng trọng số và thời gian từng phần trong `ROADMAP.md` để điều chỉnh nếu đề thi của bạn có cấu trúc khác.

## Khi làm đề thi thử

- Chọn một đề trong `mock_exams_6q_60min/` hoặc `mock_exams/` và đặt giờ đúng thời lượng đề.
- Làm như thi thật: không xem `solutions/`, không tra lý thuyết giữa giờ, tự gõ code.
- Sau khi hết giờ, tự chấm và điền [`POST_MOCK_REVIEW.md`](POST_MOCK_REVIEW.md). Chọn ba lỗi quan trọng nhất để ôn lại.
- Chỉ làm đề tiếp theo sau khi đã sửa hoặc luyện lại các lỗi lặp lại.

## Quy tắc tự kiểm tra

- Có thể giải thích vì sao chọn kỹ thuật, không chỉ nhớ mẫu code.
- Tự viết lại được lời giải mà không nhìn tài liệu.
- Kiểm tra được trường hợp rỗng, số 0, dữ liệu trùng, ký tự đặc biệt và đầu vào không hợp lệ khi phù hợp.
- Dùng prepared statement cho dữ liệu đưa vào SQL; escape dữ liệu trước khi hiển thị HTML; whitelist giá trị không thể bind như tên cột sắp xếp.
- Khi đạt checkpoint từ 75% trở lên và làm được một biến thể, chuyển sang chủ đề kế tiếp; ôn lại lỗi cũ trong buổi sau.

## Mở nhanh tài liệu

- Lộ trình: [`ROADMAP.md`](ROADMAP.md)
- Lịch một buổi: [`DAILY_60MIN_PLAN.md`](DAILY_60MIN_PLAN.md)
- Theo dõi tiến độ: [`PROGRESS.md`](PROGRESS.md)
- Ôn nhanh: [`QUICK_REVIEW.md`](QUICK_REVIEW.md) và [`CHEATSHEET.md`](CHEATSHEET.md)
- Kỷ luật thi thử: [`EXAM_MODE.md`](EXAM_MODE.md)
