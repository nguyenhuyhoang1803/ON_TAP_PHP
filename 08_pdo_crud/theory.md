# BÀI 08: KẾT NỐI PDO & THAO TÁC CRUD CHUẨN MỰC

## A. Mục tiêu
Sau bài này, bạn có thể:
1. Khởi tạo kết nối PDO an toàn với đầy đủ các thuộc tính chuẩn: UTF-8 (`utf8mb4`), chế độ ném ngoại lệ `ERRMODE_EXCEPTION`, và tắt `EMULATE_PREPARES`.
2. Thực hiện 4 thao tác CRUD chuẩn: `SELECT`, `INSERT`, `UPDATE`, `DELETE` bằng Prepared Statements (100% không dính SQL Injection).
3. Sử dụng đúng `fetch()` (lấy 1 dòng) và `fetchAll()` (lấy danh sách nhiều dòng).
4. Phân biệt `bindValue()` có ép kiểu dữ liệu (`PDO::PARAM_INT`, `PDO::PARAM_STR`).
5. Áp dụng chuẩn mẫu thiết kế Post/Redirect/Get (PRG) để chống submit lặp dữ liệu khi nhấn F5.
6. Xử lý triệt để lỗi trùng khóa (Duplicate Unique Key) bằng cách bắt mã lỗi ngoại lệ `23000`.

---

## B. Bản chất
- **Tại sao dùng PDO thay vì mysqli?** PDO (PHP Data Objects) hỗ trợ hướng đối tượng nhất quán, bảo mật cao hơn, hỗ trợ ngoại lệ (Exceptions) dễ bắt lỗi và là tiêu chuẩn công nghiệp của PHP hiện đại.
- **Bản chất của Prepared Statements:** Nó tách rời hoàn toàn giữa **MÃ LỆNH SQL** và **DỮ LIỆU CỦA USER**. Server biên dịch câu lệnh SQL trước, sau đó mới lắp dữ liệu vào các vị trí giữ chỗ `:placeholder`. Hacker có nhập `' OR 1=1 --` thì MySQL cũng chỉ coi đó là một chuỗi văn bản thông thường, triệt tiêu 100% SQL Injection!
- **Tại sao cần Post/Redirect/Get (PRG)?** Khi user submit form POST thêm sản phẩm, nếu server render luôn trang danh sách thì khi user bấm F5, trình duyệt sẽ hỏi *"Bạn có muốn gửi lại form không?"* và thêm sản phẩm đó lần thứ 2. PRG chuyển hướng (Redirect qua GET) sang `list.php`, giúp F5 an toàn.

---

## C. Cú pháp cốt lõi

### 1. Chuỗi kết nối PDO Chuẩn Phòng Thi
```php
function getDbConnection(): PDO {
    $host = '127.0.0.1';
    $db   = 'exam_db';
    $user = 'root';
    $pass = ''; // Mặc định trên Laragon là rỗng
    $dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    return new PDO($dsn, $user, $pass, $options);
}
```

### 2. SELECT (fetchAll & fetch)
```php
// Lấy danh sách nhiều dòng:
$stmt = $pdo->prepare("SELECT * FROM students WHERE class_id = :cid ORDER BY id DESC");
$stmt->execute(['cid' => $classId]);
$students = $stmt->fetchAll();

// Lấy 1 dòng theo ID:
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
$stmt->execute(['id' => $id]);
$student = $stmt->fetch(); // Trả về array kết hợp hoặc false nếu không thấy
```

### 3. INSERT & Bắt Lỗi Trùng Khóa (Duplicate Email / Code)
```php
try {
    $stmt = $pdo->prepare("INSERT INTO students (code, name, email) VALUES (:code, :name, :email)");
    $stmt->execute([
        'code'  => $code,
        'name'  => $name,
        'email' => $email
    ]);
    // Áp dụng PRG Pattern:
    header('Location: students.php?msg=created');
    exit;
} catch (PDOException $e) {
    if ($e->getCode() == 23000) { // Integrity constraint violation (trùng UNIQUE)
        $error = "Mã sinh viên hoặc Email đã tồn tại trong hệ thống!";
    } else {
        error_log($e->getMessage()); // Ghi log, không echo lỗi DB ra ngoài!
        $error = "Lỗi hệ thống khi lưu dữ liệu.";
    }
}
```

### 4. UPDATE & DELETE
```php
// Update:
$stmt = $pdo->prepare("UPDATE students SET name = :name, email = :email WHERE id = :id");
$stmt->execute(['name' => $name, 'email' => $email, 'id' => $id]);

// Delete:
$stmt = $pdo->prepare("DELETE FROM students WHERE id = :id");
$stmt->execute(['id' => $id]);
```

---

## D. Ví dụ tối giản

```php
<?php
// pdo_quick.php
$pdo = new PDO("mysql:host=localhost;dbname=test;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Thêm
$stmt = $pdo->prepare("INSERT INTO items (name, price) VALUES (:n, :p)");
$stmt->execute(['n' => 'Bút chì', 'p' => 5000]);

// Đọc
$stmt = $pdo->query("SELECT * FROM items");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "{$r['id']}: {$r['name']} - {$r['price']} đ\n";
}
```

---

## E. Luồng tư duy
```text
Trình duyệt gửi POST (Form Thêm / Sửa / Xóa)
              ↓
Validate đầu vào (Kiểm tra rỗng, kiểu dữ liệu)
       ├─ LỖI: Render lại Form kèm báo lỗi đỏ
       └─ HỢP LỆ:
              ↓
           Mở kết nối PDO
              ↓
           $stmt = $pdo->prepare("INSERT / UPDATE / DELETE ... :param")
              ↓
           $stmt->execute(['param' => $value])
              ↓
           Bắt ngoại lệ try / catch (PDOException $e)
              ├─ Lỗi 23000: Báo trùng lặp dữ liệu
              └─ Thành công: PRG (header('Location: list.php?status=success') -> exit)
```

---

## F. Những lỗi hay gặp
1. **Dùng biến nối chuỗi trực tiếp vào SQL:** `$pdo->query("SELECT * FROM users WHERE id = $id")` -> Dính ngay SQL Injection.
2. **Quên `charset=utf8mb4`:** Tiếng Việt bị thành dấu hỏi chấm `???`.
3. **Quên `exit` sau khi redirect trong PRG:** Script tiếp tục chạy gây lỗi khó hiểu.
4. **Không bắt mã lỗi `23000`:** Khi trùng email, web văng màn hình cam chết chóc của PDOException làm lộ thông tin cấu trúc database.

---

## G. Cách nhận dạng câu hỏi
- Đề có câu: *"Xây dựng trang Quản lý nhân viên gồm: Hiển thị danh sách, Thêm mới nhân viên (kiểm tra không trùng email), Sửa thông tin và Xóa nhân viên"* -> **Dựng trọn bộ CRUD PDO với Prepared Statements + Bắt lỗi PDO 23000 + PRG Pattern**.
- Đề có câu: *"Xóa nhân viên có id truyền từ URL"* -> **Đọc `$_GET['id']`, ép kiểu `(int)`, dùng `DELETE FROM employees WHERE id = :id`**.

---

## H. Mini challenge
> **Đề bài:** Xây dựng tính năng Thêm sản phẩm và Chống trùng mã sản phẩm:
> 1. Bảng `products` có các cột: `id` (PK, AI), `code` (VARCHAR, UNIQUE), `name` (VARCHAR), `price` (DECIMAL).
> 2. Form gồm 3 ô: Mã sản phẩm, Tên sản phẩm, Giá bán.
> 3. Xử lý lưu bằng PDO Prepared Statement:
>    - Nếu mã sản phẩm đã tồn tại -> Bắt lỗi ngoại lệ và báo đỏ: "Mã sản phẩm [code] đã tồn tại!".
>    - Nếu thành công -> Redirect về `products.php?msg=added`.  
> *(Lời giải có trong thư mục `solutions/mini_challenge_solution.php`). Tự làm trước khi xem!*
