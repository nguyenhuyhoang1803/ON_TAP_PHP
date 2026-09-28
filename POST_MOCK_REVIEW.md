# BIÊN BẢN RÀ SOÁT SAU MOCK EXAM (POST MOCK REVIEW)
> Sử dụng biểu mẫu này sau khi hoàn thành mỗi đề thi thử để mổ xẻ chính xác nguyên nhân mất điểm và lập kế hoạch sửa lỗi dứt điểm.

---

## 1. Thông Số Tổng Quan

- **Đề thi thử:** Mock Exam 0__
- **Tổng điểm đạt được:** _____ / 10.0 điểm
- **Tổng thời gian làm bài:** _____ phút (Quy định: 60 phút)
- **Tình trạng nộp bài:** [ ] Đúng giờ [ ] Quá giờ (quá _____ phút)

---

## 2. Nhật Ký Thời Gian Từng Câu

| Câu số | Nội dung kỹ thuật | Thời gian làm (phút) | Điểm đạt được | Bị kẹt bao lâu? |
|:---:|---|:---:|:---:|:---:|
| **Câu 1** | | _____ phút | _____ / _____ | _____ phút |
| **Câu 2** | | _____ phút | _____ / _____ | _____ phút |
| **Câu 3** | | _____ phút | _____ / _____ | _____ phút |
| **Câu 4** | | _____ phút | _____ / _____ | _____ phút |

- **Câu khiến tôi bị kẹt lâu nhất:** Câu số _____
- **Lý do bị kẹt:** __________________________________________________________________

---

## 3. Phân Loại Lỗi & Bug Gặp Phải

### A. Bug Cú Pháp (Syntax Errors)
- [ ] Quên dấu chấm phẩy `;`
- [ ] Sai tên hàm / sai thứ tự tham số
- [ ] Thiếu thẻ đóng `?>` hoặc ngoặc nhọn `}`
- *Chi tiết lỗi cụ thể:* ______________________________________________________________

### B. Bug Logic Nghiệp Vụ
- [ ] Bẫy số 0: dùng `empty()` làm mất giá trị `0`
- [ ] Tính sai `OFFSET`: `page * limit` thay vì `(page - 1) * limit`
- [ ] Thuật toán phân trang: không kẹp `page` làm trang bị âm hoặc vượt trần
- [ ] Quên `exit` sau `header('Location: ...')`
- *Chi tiết lỗi cụ thể:* ______________________________________________________________

### C. Bug SQL & Database
- [ ] Dùng `INNER JOIN` làm mất bản ghi chưa có dữ liệu quan hệ (đáng lẽ phải dùng `LEFT JOIN`)
- [ ] Dùng `COUNT(*)` trong `LEFT JOIN` làm bản ghi rỗng vẫn bị đếm là 1
- [ ] Dùng `WHERE` thay vì `HAVING` khi lọc hàm tổng hợp (`COUNT`, `SUM`)
- [ ] Dùng mù quáng `LIMIT 1` làm mất kết quả khi có đồng hạng cao nhất
- *Chi tiết lỗi cụ thể:* ______________________________________________________________

### D. Lỗ Hổng Bảo Mật (Security Bugs)
- [ ] Nối chuỗi biến trực tiếp vào câu lệnh SQL (Dính SQL Injection)
- [ ] Lấy trực tiếp `$_GET['sort']` đưa vào `ORDER BY` mà không dùng Whitelist
- [ ] Echo trực tiếp biến người dùng nhập mà không qua `htmlspecialchars(..., ENT_QUOTES)` (Dính XSS)
- [ ] In trực tiếp lỗi CSDL `$e->getMessage()` ra màn hình người dùng
- *Chi tiết lỗi cụ thể:* ______________________________________________________________

---

## 4. Bóc Tách Nguyên Nhân Gốc Rễ

Đánh dấu `X` vào nguyên nhân chính xác:
- [ ] **Lỗi do KHÔNG HIỂU BẢN CHẤT:** (Chưa hiểu vì sao phải dùng kỹ thuật đó)
- [ ] **Lỗi do NHỚ SAI CÚ PHÁP:** (Hiểu cần làm gì nhưng quên tên hàm, quên cấu hình)
- [ ] **Lỗi do TỐC ĐỘ GÕ CHẬM:** (Phải dừng lại suy nghĩ quá lâu, chưa có phản xạ cơ bắp)

---

## 5. Kế Hoạch Khắc Phục Dứt Điểm

### 🎯 Top 3 Nội Dung Cần Ôn Luyện Lại Ngay:
1. __________________________________________________________________
2. __________________________________________________________________
3. __________________________________________________________________

### 📝 Bài Luyện Bổ Sung Được Chỉ Định:
- Module cần làm lại: Module _____
- Mini test cần bấm giờ lại: Mini Test _____
- Mục tiêu cho Mock Exam tiếp theo: Hoàn thành trong _____ phút, đạt tối thiểu _____ điểm.
