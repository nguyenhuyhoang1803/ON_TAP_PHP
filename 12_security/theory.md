# BÀI 12: BẢO MẬT PHÒNG THI (SECURITY ESSENTIALS)

## A. Mục tiêu
Sau bài này, bạn có thể:
1. Phòng chống triệt để lỗ hổng Cross-Site Scripting (XSS) dạng Reflected và Stored.
2. Phòng chống 100% SQL Injection qua PDO Prepared Statements và Whitelist.
3. Chặn đứng Path Traversal và tải lên file mã độc (Web Shell).
4. Che giấu thông tin lỗi nhạy cảm của CSDL và hệ thống khi xảy ra ngoại lệ.
5. Bảo vệ an toàn các tham số truyền qua URL (URL Tampering).

---

## B. Bản chất
- **XSS là gì?** Là khi kẻ tấn công đưa mã JavaScript độc hại vào form. Nếu server in thẳng ra HTML, trình duyệt nạn nhân sẽ thực thi mã đó (đánh cắp Cookie session, chuyển hướng trang lừa đảo). *Giải pháp:* `htmlspecialchars($val, ENT_QUOTES, 'UTF-8')` trong PHP và `textContent` trong JS.
- **SQL Injection là gì?** Là khi kẻ tấn công chèn các ký tự điều khiển SQL (như `'`, `--`, `OR 1=1`) để thay đổi cấu trúc câu truy vấn. *Giải pháp:* Tách biệt dữ liệu và mã lệnh bằng Prepared Statements.
- **Che giấu lỗi hệ thống:** Không bao giờ để trang web lộ ra dòng chữ `Fatal error: Uncaught PDOException: SQLSTATE[42S02]: Base table or view not found...`. Kẻ xấu nhìn thấy tên bảng và cấu trúc CSDL sẽ dễ dàng tấn công. Bắt lỗi bằng `try/catch`, ghi vào file log nội bộ và chỉ in câu thông báo thân thiện ra màn hình.

---

## C. Cú pháp cốt lõi

### 1. Hàm Escape Toàn Diện
```php
function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
```

### 2. Phòng Chống SQL Injection Tuyệt Đối
```php
// GIÁ TRỊ DỮ LIỆU: Dùng Prepared Statement
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);

// TÊN CỘT / SẮP XẾP: Bắt buộc dùng Whitelist
$allowed = ['id', 'name', 'price'];
$col = in_array($sort, $allowed, true) ? $sort : 'id';
```

### 3. Phòng Chống Lộ Lỗi Database
```php
try {
    $stmt->execute();
} catch (PDOException $e) {
    error_log("[" . date('Y-m-d H:i:s') . "] DB Error: " . $e->getMessage() . "\n", 3, __DIR__ . '/storage/logs/db.log');
    $userMessage = "Hệ thống đang bận. Vui lòng quay lại sau!";
}
```

---

## D. Ví dụ tối giản
```php
<?php
// security_demo.php
$userInput = "<script>alert('Hacked')</script>";
echo "Xin chào: " . htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
```

---

## E. Luồng tư duy
```text
Dữ liệu từ người dùng ($_GET, $_POST, $_COOKIE)
              ↓
[LỚP 1] Validate & Ép kiểu chặt chẽ (int, float, regex, in_array whitelist)
              ↓
[LỚP 2] Tương tác CSDL qua Prepared Statements (Tham số hóa :param)
              ↓
[LỚP 3] Xử lý ngoại lệ với try/catch (Ghi log bảo mật, giấu lỗi kỹ thuật)
              ↓
[LỚP 4] Xuất ra HTML qua htmlspecialchars() hoặc JavaScript textContent
```

---

## F. Những lỗi hay gặp
1. **Dùng `htmlspecialchars` thiếu cờ `ENT_QUOTES`:** Dấu nháy đơn `'` không được mã hóa, làm vỡ thuộc tính `value='...'` của thẻ HTML.
2. **Nghĩ rằng `strip_tags()` là đủ an toàn:** Hacker có thể lợi dụng thuộc tính sự kiện `onload=...`, `onerror=...` trong các thẻ không bị lọc.
3. **In thẳng `$e->getMessage()` ra màn hình:** Bị giám khảo trừ điểm bảo mật nặng nề.

---

## G. Cách nhận dạng câu hỏi
- Đề có câu: *"Đảm bảo ứng dụng an toàn trước các cuộc tấn công SQL Injection và XSS"* -> **Áp dụng Prepared Statements cho mọi câu SQL + `htmlspecialchars` cho mọi output HTML**.
- Giám thị nhập thử `Patrick O'Connor` hoặc `<script>alert(1)</script>` -> **Hệ thống vẫn chạy mượt mà, lưu đúng dữ liệu và in ra đúng chuỗi nguyên bản**.

---

## H. Mini challenge
> **Đề bài:** Hãy rà soát đoạn script sau và viết lại phiên bản an toàn tuyệt đối 100%:
> ```php
> $cat = $_GET['cat'];
> $sort = $_GET['sort'];
> $res = $pdo->query("SELECT * FROM products WHERE cat_id = $cat ORDER BY $sort");
> while ($row = $res->fetch()) {
>     echo "<li>" . $row['name'] . " - " . $row['price'] . "</li>";
> }
> ```  
> *(Lời giải có trong thư mục `solutions/mini_challenge_solution.php`). Tự làm trước khi xem!*
