# BÀI 02: FORM, STICKY FORM & VALIDATION CHUẨN

## A. Mục tiêu
Sau bài này, bạn có thể:
1. Xử lý form gửi dữ liệu về chính trang hiện tại (`action=""` hoặc gửi cùng file).
2. Phân biệt chính xác phương thức `GET` (tìm kiếm, lọc) và `POST` (thêm, sửa, xóa, nhạy cảm).
3. Validate dữ liệu chặt chẽ: bắt buộc nhập, định dạng email, khoảng số nguyên, độ dài chuỗi.
4. Kỹ thuật Sticky Form (giữ lại dữ liệu người dùng đã nhập khi form bị lỗi, không bắt gõ lại từ đầu).
5. Escape an toàn 100% dữ liệu xuất ra HTML bằng `htmlspecialchars()` phòng ngừa bẫy XSS.

---

## B. Bản chất
- **Tại sao cần Form gửi về chính trang?** Trong bài thi thực hành 60 phút, gom cả logic hiển thị Form và xử lý Form vào chung một file `register.php` hoặc `index.php` giúp tiết kiệm thời gian chuyển file, quản lý biến lỗi dễ dàng hơn rất nhiều.
- **Khi nào dùng Sticky Form?** Khi người dùng nhập 10 ô mà chỉ sai 1 ô, nếu tải lại trang mà xóa sạch 9 ô còn lại thì trải nghiệm cực kỳ tệ và bị trừ điểm form validation.
- **Bản chất của `htmlspecialchars`:** Trình duyệt hiểu `<script>` là thẻ thực thi mã. Hàm `htmlspecialchars` biến `<` thành `&lt;`, `>` thành `&gt;`, `"` thành `&quot;`. Nhờ đó trình duyệt in ra chữ thuần túy, vô hiệu hóa mã độc.

---

## C. Cú pháp cốt lõi

### 1. Kiểm tra phương thức gửi form
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Chỉ xử lý khi người dùng thực sự bấm nút Submit dạng POST
}
```

### 2. Validation chuẩn (Tránh bẫy số 0)
```php
$errors = [];

// Lấy dữ liệu và cắt khoảng trắng 2 đầu
$fullname = trim($_POST['fullname'] ?? '');
$email    = trim($_POST['email'] ?? '');
$age      = trim($_POST['age'] ?? '');

// 1. Kiểm tra rỗng (Dùng === '')
if ($fullname === '') {
    $errors['fullname'] = 'Họ và tên không được để trống.';
}

// 2. Kiểm tra Email
if ($email === '') {
    $errors['email'] = 'Email không được để trống.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Email không đúng định dạng.';
}

// 3. Kiểm tra số nguyên trong khoảng [18 - 60] (Không dùng empty($age) vì tuổi 0 vẫn là empty)
if ($age === '') {
    $errors['age'] = 'Tuổi không được để trống.';
} elseif (!filter_var($age, FILTER_VALIDATE_INT, ['options' => ['min_range' => 18, 'max_range' => 60]])) {
    $errors['age'] = 'Tuổi phải là số nguyên từ 18 đến 60.';
}
```

### 3. Hàm Escape HTML ngắn gọn
```php
function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
```

---

## D. Ví dụ tối giản

```php
<?php
// sticky_form.php
$errors = [];
$name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        $errors['name'] = 'Vui lòng nhập tên!';
    } else {
        $success = "Xin chào, " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<body>
    <?php if (!empty($success)): ?>
        <p style="color: green;"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Họ tên:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>">
        <?php if (isset($errors['name'])): ?>
            <span style="color: red;"><?= $errors['name'] ?></span>
        <?php endif; ?>
        <br><br>
        <button type="submit">Gửi</button>
    </form>
</body>
</html>
```

---

## E. Luồng tư duy
```text
Nhận Request từ Browser
       ↓
Kiểm tra REQUEST_METHOD === 'POST' ?
    ├─ KHÔNG: Chỉ render Form rỗng
    └─ CÓ:
         ↓
       Lấy dữ liệu thô: trim($_POST['...'] ?? '')
         ↓
       Chạy bộ kiểm tra (Validation):
         - Bắt buộc nhập (=== '')
         - Kiểm tra kiểu/định dạng (filter_var)
         - Ràng buộc logic (min, max, length)
         ↓
       Có lỗi ($errors không rỗng) ?
         ├─ CÓ: Đổ lỗi ra mảng $errors, giữ lại input để làm Sticky Form
         └─ KHÔNG: Thực hiện lưu CSDL hoặc hiển thị thông báo thành công
```

---

## F. Những lỗi hay gặp
1. **Dùng `empty($_POST['qty'])`:** Khi nhập `0`, `empty()` trả về `true` làm form báo lỗi "Chưa nhập số lượng!".
2. **Không có `ENT_QUOTES` trong `htmlspecialchars`:** Ký tự dấu nháy đơn `'` không được mã hóa, nếu in vào `value='...'` sẽ làm vỡ thẻ input HTML.
3. **Quên `method="POST"` trong thẻ `<form>`:** Mặc định HTML dùng `GET`, dữ liệu sẽ bị lộ trên URL.
4. **Không `trim()` dữ liệu:** Người dùng chỉ gõ dấu cách `"   "`, nếu không `trim` thì chuỗi không rỗng và vượt qua bước kiểm tra rỗng sai luật.

---

## G. Cách nhận dạng câu hỏi
- Đề có câu: *"Form gửi dữ liệu về chính trang, nếu có lỗi thì hiển thị thông báo dưới từng ô nhập và giữ lại dữ liệu cũ"* -> **Áp dụng cấu trúc Form POST + mảng `$errors` + Sticky input `value="<?= e($val) ?>"`**.
- Đề có câu: *"Kiểm tra điểm thi phải từ 0 đến 10"* -> **`filter_var($score, FILTER_VALIDATE_FLOAT)` kết hợp kiểm tra `$score >= 0 && $score <= 10`**.

---

## H. Mini challenge
> **Đề bài:** Tạo một form nhập thông tin Đặt phòng khách sạn gồm:
> 1. `customer_name`: Bắt buộc nhập, từ 3 ký tự trở lên.
> 2. `num_nights` (Số đêm): Bắt buộc nhập, là số nguyên $\ge 1$.
> 3. `room_type`: Chọn giữa `Standard` (500.000 VNĐ), `Deluxe` (800.000 VNĐ), `Suite` (1.500.000 VNĐ).
> - Nếu submit hợp lệ: In ra tổng tiền phải trả (`num_nights * đơn giá`).
> - Nếu lỗi: Báo lỗi cụ thể từng ô và giữ lại các giá trị đã chọn.  
> *(Lời giải có trong thư mục `solutions/mini_challenge_solution.php`). Tự làm trước khi xem!*
