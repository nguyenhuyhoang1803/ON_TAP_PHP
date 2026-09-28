# CHECKPOINT 08: PDO KẾT NỐI & THAO TÁC CRUD

> **Điều kiện vượt qua:** Đạt từ 75% trở lên (>= 7.5/10 điểm).

---

### PHẦN A – LÝ THUYẾT NHANH (5 câu x 0.6đ = 3.0đ)
1. Cấu hình `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` có tác dụng gì khi câu truy vấn SQL bị sai cú pháp?
2. Tại sao ta không được dùng `echo $e->getMessage();` trực tiếp trong khối `catch (PDOException $e)` trên trang web thực tế?
3. Sự khác nhau giữa `bindValue()` và `bindParam()` trong PDO là gì?
4. Khi thực hiện lệnh `INSERT`, làm thế nào để lấy được ID vừa tự tăng (AUTO_INCREMENT) sinh ra trong MySQL qua PDO?
5. Phương thức `$stmt->rowCount()` trả về thông tin gì sau khi thực thi câu lệnh `UPDATE` hoặc `DELETE`?

---

### PHẦN B – ĐỌC CODE & BẮT BUG (2 đoạn code x 1.0đ = 2.0đ)

#### Bug 1:
```php
$id = $_GET['id'];
$sql = "DELETE FROM users WHERE id = " . $id;
$pdo->exec($sql);
header("Location: list.php");
```
*Chỉ ra 2 lỗ hổng bảo mật và sự cố tiềm ẩn trong đoạn code xóa trên.*

#### Bug 2:
```php
$stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = :cat LIMIT :limit");
$stmt->execute([
    'cat' => 3,
    'limit' => 10
]);
```
*Tại sao trong một số phiên bản MySQL, đoạn code trên lại bị lỗi syntax ở mệnh đề `LIMIT`? Cách sửa chuẩn bằng `bindValue()` là gì?*

---

### PHẦN C – VIẾT CODE THỰC HÀNH (3.0đ)
Viết một hàm `updateUserEmail(PDO $pdo, int $userId, string $newEmail): array`:
- Kiểm tra `$newEmail` có đúng định dạng email không.
- Thực hiện Prepared Statement cập nhật email của user có ID tương ứng.
- Bắt ngoại lệ nếu `$newEmail` bị trùng lặp với người dùng khác trong hệ thống (`23000`).
- Trả về `['success' => true, 'message' => 'Cập nhật thành công']` hoặc `['success' => false, 'error' => '...']`.

---

### PHẦN D – BIẾN THỂ ĐỀ THI (2.0đ)
Nếu đề bài yêu cầu: *"Xóa một danh mục sản phẩm, nhưng nếu trong danh mục đó ĐANG CÓ sản phẩm thì không được phép xóa và phải thông báo 'Danh mục này đang chứa sản phẩm, không thể xóa!'"*.  
Bạn sẽ thiết kế logic kiểm tra và truy vấn SQL như thế nào trước khi thực hiện lệnh DELETE?
