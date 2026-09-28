# TỔNG HỢP CÁC KỊCH BẢN TẤN CÔNG THỰC TẾ TRONG PHÒNG THI (ATTACK CASES)

Giám thị thường thử nghiệm các payload sau để kiểm tra code của bạn:

---

## 1. Kịch bản Tấn công XSS (Cross-Site Scripting)

### Payload 1: Thẻ Script cổ điển
```html
<script>alert('XSS-VULNERABLE')</script>
```
- **Mục tiêu:** Kiểm tra ô input có bị dính Reflected/Stored XSS không.
- **Nếu dính:** Trình duyệt hiện popup cảnh báo.
- **Code chuẩn:** `<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>`.

### Payload 2: Phá vỡ thuộc tính `value` của thẻ Input
```text
" onfocus="alert(1)" autofocus="
```
- **Mục tiêu:** Nếu viết `<input value="<?php echo $val; ?>">`, dấu ngoặc kép sẽ đóng thẻ `value`, biến `onfocus` thành thuộc tính thực thi JavaScript mà không cần thẻ `<script>`.
- **Code chuẩn:** Phải có cờ `ENT_QUOTES`.

---

## 2. Kịch bản Tấn công SQL Injection

### Payload 1: Bypass Đăng Nhập
```text
' OR '1'='1' -- 
admin' #
```
- **Mục tiêu:** Biến câu truy vấn thành luôn đúng để đăng nhập trái phép không cần mật khẩu.
- **Khắc phục:** Dùng Prepared Statements với placeholder `:user` và `:pass`.

### Payload 2: Tên có dấu nháy đơn hợp pháp
```text
Patrick O'Connor
McDonald's
```
- **Mục tiêu:** Kiểm tra xem code có bị văng lỗi `SQL syntax error near 'Connor'` hay không.
- **Khắc phục:** Prepared Statements tự động xử lý dấu nháy an toàn.

### Payload 3: Injection ở mệnh đề `ORDER BY`
```text
?sort=price; DROP TABLE users; --
?sort=(CASE WHEN (SELECT 1=1) THEN id ELSE name END)
```
- **Mục tiêu:** Tấn công mù (Blind SQLi) hoặc phá hoại CSDL qua URL.
- **Khắc phục:** Whitelist bắt buộc: `in_array($sort, ['id', 'price', 'name'], true)`.

---

## 3. Kịch bản Tấn công File Upload

### Payload 1: File mã độc đội lốt ảnh
Tạo file `shell.php` hoặc `avatar.php.jpg` có nội dung:
```php
<?php system($_GET['cmd']); ?>
```
- **Mục tiêu:** Thực thi mã lệnh tùy ý trên server.
- **Khắc phục:** 
  1. Chỉ kiểm tra đuôi mở rộng cuối cùng bằng `pathinfo($name, PATHINFO_EXTENSION)`.
  2. Đổi tên file thành `uniqid() . '.' . $ext`.
  3. Không cấp quyền thực thi file PHP trong thư mục `uploads/`.
