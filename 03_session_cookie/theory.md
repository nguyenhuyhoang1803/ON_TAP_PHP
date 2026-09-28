# BÀI 03: SESSION, COOKIE & HỆ THỐNG XÁC THỰC (AUTH GUARD)

## A. Mục tiêu
Sau bài này, bạn có thể:
1. Hiểu bản chất cơ chế hoạt động của Session và Cookie trên giao thức HTTP không trạng thái (stateless).
2. Xây dựng chức năng Đăng nhập (Login), Đăng xuất (Logout) và Bảo vệ trang nội bộ (Login Guard).
3. Sử dụng `header('Location: ...')` kết hợp lệnh sống còn `exit` để chuyển hướng an toàn.
4. Triển khai biến đếm số lượt truy cập trong phiên hoặc giỏ hàng mini lưu trong `$_SESSION`.
5. Tạo Cookie ghi nhớ tên tài khoản (Remember Me) bằng `setcookie()` và hủy cookie khi cần.

---

## B. Bản chất
- **Tại sao cần Session?** Giao thức HTTP là Stateless (không nhớ ai vừa gửi request). Khi bạn sang trang `dashboard.php`, server không biết bạn đã đăng nhập ở `login.php` hay chưa. Session sinh ra một mã định danh ngẫu nhiên (Session ID) lưu vào Cookie của trình duyệt, còn dữ liệu thật (như `user_id`, `role`) được lưu an toàn trên ổ cứng của server.
- **Khi nào dùng Cookie?** Khi muốn lưu thông tin ít nhạy cảm ở phía máy khách trong thời gian dài (ví dụ: ghi nhớ username trong 7 ngày, lưu ngôn ngữ giao diện).
- **Lỗ hổng chết người:** Chuyển trang bằng `header('Location: login.php')` mà quên lệnh `exit;`. Trình duyệt tuy nhận được lệnh chuyển hướng nhưng PHP server vẫn âm thầm thực thi toàn bộ mã code phía sau (xóa dữ liệu, gửi tiền, lộ thông tin).

---

## C. Cú pháp cốt lõi

### 1. Bắt đầu phiên làm việc
```php
session_start(); // Luôn đặt ở DÒNG ĐẦU TIÊN của file, trước mọi output HTML
```

### 2. Bộ bảo vệ trang (Auth Guard)
```php
// File: admin.php hoặc dashboard.php
session_start();
if (empty($_SESSION['logged_in_user'])) {
    header('Location: login.php');
    exit; // BẮT BUỘC CÓ EXIT
}
```

### 3. Xử lý Đăng xuất (Logout)
```php
// File: logout.php
session_start();
$_SESSION = []; // Xóa sạch dữ liệu trong mảng session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy(); // Hủy file session trên server
header('Location: login.php');
exit;
```

### 4. Cookie ghi nhớ tên đăng nhập
```php
// Ghi nhớ trong 7 ngày
setcookie('remember_username', $username, time() + 7 * 24 * 3600, '/');

// Xóa cookie khi người dùng bỏ tick
setcookie('remember_username', '', time() - 3600, '/');
```

---

## D. Ví dụ tối giản

```php
<?php
// login_demo.php
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    // Giả lập tài khoản admin / 123456
    if ($user === 'admin' && $pass === '123456') {
        $_SESSION['logged_in_user'] = $user;
        header('Location: secret.php');
        exit;
    } else {
        $error = 'Sai tài khoản hoặc mật khẩu!';
    }
}
?>
<!DOCTYPE html>
<html>
<body>
    <?php if ($error): ?><p style="color:red"><?= $error ?></p><?php endif; ?>
    <form method="POST">
        Tài khoản: <input type="text" name="username"><br>
        Mật khẩu: <input type="password" name="password"><br>
        <button type="submit">Đăng nhập</button>
    </form>
</body>
</html>
```

---

## E. Luồng tư duy
```text
Trang Secret.php được gọi
       ↓
session_start()
       ↓
Kiểm tra $_SESSION['logged_in_user'] có tồn tại không?
   ├─ KHÔNG: header('Location: login.php') → exit (Dừng ngay lập tức)
   └─ CÓ: Hiển thị nội dung bí mật
```

---

## F. Những lỗi hay gặp
1. **Báo lỗi `Headers already sent`:** Do có khoảng trắng thừa trước `<?php` hoặc đã dùng `echo` trước khi gọi `session_start()` hoặc `header()`.
2. **Quên `exit` sau `header('Location: ...')`:** Dẫn đến code bên dưới vẫn chạy.
3. **Mảng `$_SESSION` bị mất khi sang file khác:** Do file thứ hai quên gọi `session_start()`.
4. **Nhầm lẫn thời gian hết hạn của Cookie:** Hàm `setcookie` nhận timestamp tuyệt đối (ví dụ: `time() + 3600` chứ không phải là `3600`).

---

## G. Cách nhận dạng câu hỏi
- Đề có câu: *"Chỉ cho phép người dùng đã đăng nhập xem danh sách sản phẩm, nếu chưa đăng nhập thì chuyển hướng về trang login"* -> **Dùng Session Guard + `header('Location: login.php'); exit;`**.
- Đề có câu: *"Đếm số lần người dùng đã xem trang này trong phiên hiện tại"* -> **`$_SESSION['views'] = ($_SESSION['views'] ?? 0) + 1;`**.
- Đề có checkbox: *"Ghi nhớ tài khoản"* -> **Dùng `setcookie('remember_user', ...)`**.

---

## H. Mini challenge
> **Đề bài:** Xây dựng hệ thống Đăng nhập thư viện mini gồm 3 file:
> 1. `login.php`: Cho phép đăng nhập với tài khoản `thuthu` / `lib123`. Có checkbox "Ghi nhớ tên đăng nhập". Nếu đăng nhập thành công, lưu session và chuyển sang `books.php`. Nếu có tick ghi nhớ thì lưu Cookie trong 3 ngày.
> 2. `books.php`: Bắt buộc kiểm tra đăng nhập. Nếu chưa thì redirect về `login.php`. Hiển thị: "Chào mừng [username]!", số lần user đã xem trang này, và nút Đăng xuất.
> 3. `logout.php`: Hủy session và chuyển về `login.php`.  
> *(Lời giải có trong thư mục `solutions/mini_challenge_solution.php`). Tự làm trước khi xem!*
