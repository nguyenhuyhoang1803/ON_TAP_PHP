# CHECKPOINT 02: FORM VALIDATION & STICKY FORM

> **Điều kiện vượt qua:** Đạt từ 75% trở lên (>= 7.5/10 điểm).

---

### PHẦN A – LÝ THUYẾT NHANH (5 câu x 0.6đ = 3.0đ)
1. Tại sao thẻ `<form>` nên để thuộc tính `action=""` khi xử lý trên cùng một file?
2. Sự khác biệt giữa `$_SERVER['REQUEST_METHOD'] === 'POST'` và `isset($_POST['submit'])` là gì? Cách nào đáng tin cậy hơn?
3. Nếu người dùng nhập vào ô tuổi giá trị `-5`, đoạn code `empty($_POST['age'])` trả về `true` hay `false`? Có bắt được lỗi nhập tuổi âm không?
4. Kỹ thuật "Sticky Form" đối với thẻ `<select>` được triển khai như thế nào? (Thuộc tính HTML nào quyết định option được chọn?).
5. Trình bày ít nhất 2 lý do tại sao hàm `strip_tags()` không an toàn bằng `htmlspecialchars()` khi phòng chống XSS trên form input.

---

### PHẦN B – ĐỌC CODE & BẮT BUG (2 đoạn code x 1.0đ = 2.0đ)

#### Bug 1:
```php
if ($_POST) {
    $score = $_POST['score'];
    if (empty($score)) {
        $error = "Chưa nhập điểm";
    }
}
// Giả sử thí sinh nhập điểm là 0. Chuyện gì xảy ra?
```

#### Bug 2:
```php
<form method="POST">
    <input type="text" name="email" value="<?php echo $_POST['email']; ?>">
    <button type="submit">Submit</button>
</form>
<!-- Nhập thử: " onfocus="alert('hacked')" autofocus=" -->
```
*Chỉ ra lỗ hổng bảo mật và viết lại dòng input an toàn.*

---

### PHẦN C – VIẾT CODE THỰC HÀNH (3.0đ)
Viết một script `feedback.php` hoàn chỉnh gồm cả PHP logic và HTML Form:
- Trường `phone`: Bắt buộc, phải gồm đúng 10 chữ số và bắt đầu bằng số `0` (Gợi ý dùng Regex `preg_match('/^0[0-9]{9}$/', $phone)`).
- Trường `rating`: Bắt buộc chọn từ 1 đến 5 sao.
- Trường `comment`: Không bắt buộc, nhưng nếu có nhập thì tối thiểu 10 ký tự.
- Thực hiện đầy đủ Sticky Form và hiển thị thông báo lỗi đỏ dưới từng ô nhập.

---

### PHẦN D – BIẾN THỂ ĐỀ THI (2.0đ)
Nếu đề bài yêu cầu: *"Chống việc người dùng bấm Submit liên tục nhiều lần (Double Submit) làm dữ liệu bị nhân đôi"*, bạn sẽ xử lý vấn đề này bằng kỹ thuật PRG (Post/Redirect/Get) kết hợp Session như thế nào? Nêu các bước thực hiện.
