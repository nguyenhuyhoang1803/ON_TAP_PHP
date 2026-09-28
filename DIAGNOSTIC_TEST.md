# BÀI KIỂM TRA ĐỊNH VỊ NĂNG LỰC ĐẦU VÀO (DIAGNOSTIC TEST)
> **Mục tiêu:** Định vị chính xác trình độ hiện tại qua 12 câu hỏi thực tế (không hỏi định nghĩa khô khan), tìm ra điểm yếu lớn nhất có ảnh hưởng mạnh nhất đến điểm thi 60 phút để lập kế hoạch học trọng tâm ngay lập tức.

---

### PHẦN 1: PHP CĂN BẢN & FORM VALIDATION
**Câu 1 (Bẫy số 0):** Đoạn code sau in ra gì? Tại sao?
```php
$score = "0";
if (empty($score)) {
    echo "Không hợp lệ";
} else {
    echo "Điểm: " . $score;
}
```

**Câu 2 (Bảo mật Form):** Điểm khác nhau giữa hai cách render này là gì và cách nào chống được tấn công XSS?
- Cách A: `<input type="text" name="name" value="<?= $_POST['name'] ?? '' ?>">`
- Cách B: `<input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">`

---

### PHẦN 2: SESSION & AUTH GUARD
**Câu 3 (Auth Guard):** Đoạn code sau có lỗ hổng bảo mật nghiêm trọng nào?
```php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
}
// Các thao tác xóa CSDL và in dữ liệu bảo mật ở bên dưới...
```

**Câu 4 (Session vs Cookie):** Nếu muốn lưu tên người dùng trong 7 ngày để lần sau mở trình duyệt tự điền vào ô đăng nhập, bạn dùng `$_SESSION` hay `$_COOKIE`? Cú pháp tạo như thế nào?

---

### PHẦN 3: PDO & CSDL MYSQL
**Câu 5 (SQL Injection):** Tại sao đoạn code dưới đây lại bị cấm tuyệt đối trong phòng thi? Sửa lại thế nào bằng Prepared Statement?
```php
$kw = $_GET['kw'];
$stmt = $pdo->query("SELECT * FROM products WHERE name LIKE '%$kw%'");
```

**Câu 6 (Bắt lỗi trùng lặp):** Khi chèn một bản ghi mới có email trùng với email đã có trong bảng (`UNIQUE constraint`), PDO sẽ ném ra ngoại lệ gì và làm thế nào để bắt được mã lỗi `23000` mà không làm sập website?

---

### PHẦN 4: SQL NÂNG CAO (JOIN, GROUP BY, HAVING)
**Câu 7 (INNER vs LEFT JOIN):** Đề bài: *"Hiển thị tất cả khách hàng và tổng số đơn hàng đã mua (kể cả khách chưa từng mua đơn nào)"*. Bạn dùng `INNER JOIN` hay `LEFT JOIN`? Và tại sao phải dùng `COUNT(o.id)` thay vì `COUNT(*)`?

**Câu 8 (WHERE vs HAVING):** Câu lệnh sau bị lỗi gì khi chạy trên MySQL? Sửa lại thế nào?
```sql
SELECT department_id, COUNT(*) AS total_emp
FROM employees
WHERE COUNT(*) >= 5
GROUP BY department_id;
```

---

### PHẦN 5: JAVASCRIPT, FETCH API & DOM
**Câu 9 (Debounce):** Trong tính năng tìm kiếm realtime, tại sao lại cần kỹ thuật Debounce khoảng 300ms? Nếu không có debounce thì điều gì xảy ra khi người dùng gõ nhanh 10 ký tự?

**Câu 10 (DOM Security):** Khi nhận dữ liệu JSON từ server và render ra bảng HTML bằng JavaScript, tại sao nên dùng `td.textContent = item.name` thay vì `td.innerHTML = item.name`?

---

### PHẦN 6: OOP & PHÂN TRANG (PAGINATION)
**Câu 11 (Abstract vs Interface):** Khi nào ta dùng `interface` và khi nào ta dùng `abstract class`? Từ khóa tương ứng trong class con là gì?

**Câu 12 (Whitelist Sắp xếp):** Tại sao ta không thể dùng `$stmt->bindValue(':sort', $_GET['sort'])` cho mệnh đề `ORDER BY :sort`? Cách xử lý chuẩn là gì?
