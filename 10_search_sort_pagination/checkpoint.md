# CHECKPOINT 10: TÌM KIẾM, SẮP XẾP & PHÂN TRANG

> **Điều kiện vượt qua:** Đạt từ 75% trở lên (>= 7.5/10 điểm).

---

### PHẦN A – LÝ THUYẾT NHANH (5 câu x 0.6đ = 3.0đ)
1. Tại sao tham số `LIMIT` và `OFFSET` trong câu lệnh PDO lại cần dùng `bindValue(':limit', $limit, PDO::PARAM_INT)` thay vì truyền thẳng mảng trong `execute(['limit' => $limit])`?
2. Nếu trang web có 26 sản phẩm, mỗi trang hiển thị 10 sản phẩm thì có tất cả bao nhiêu trang? Công thức toán học trong PHP là gì?
3. Trình bày rủi ro nếu lập trình viên viết: `$sql = "SELECT * FROM users ORDER BY " . $_GET['order'];`. Hacker có thể khai thác điều gì?
4. Kỹ thuật Whitelist cho chiều sắp xếp (Direction) được viết tối giản trong 1 dòng code như thế nào?
5. Tại sao cần dùng hàm `strtr($kw, ['%' => '\%', '_' => '\_'])` trước khi ghép vào `%kw%`?

---

### PHẦN B – ĐỌC CODE & BẮT BUG (2 đoạn code x 1.0đ = 2.0đ)

#### Bug 1:
```php
$page = $_GET['page'];
$limit = 10;
$offset = $page * $limit;
$stmt = $pdo->prepare("SELECT * FROM news LIMIT $limit OFFSET $offset");
```
*Chỉ ra 2 lỗi nghiêm trọng trong việc tính toán offset và bảo mật câu lệnh.*

#### Bug 2:
```php
<a href="?page=<?= $page + 1 ?>">Trang kế tiếp</a>
```
*Khi người dùng bấm vào link trên, chuyện gì sẽ xảy ra với các tiêu chí tìm kiếm và sắp xếp trước đó? Sửa lại thế nào?*

---

### PHẦN C – VIẾT CODE THỰC HÀNH (3.0đ)
Viết đoạn mã PHP thực hiện truy vấn danh sách sinh viên:
- Lọc theo từ khóa `kw` tìm kiếm trong cột `fullname` hoặc `email`.
- Sắp xếp Whitelist theo cột `gpa` hoặc `id`.
- Phân trang mỗi trang 10 sinh viên, lấy đúng dữ liệu của trang hiện tại `$page`.
- Ràng buộc an toàn 100% Prepared Statements.

---

### PHẦN D – BIẾN THỂ ĐỀ THI (2.0đ)
Nếu đề bài yêu cầu: *"Hiển thị thanh phân trang thông minh có nút 'Trang đầu', 'Trang trước', 'Trang sau', 'Trang cuối'. Nút 'Trang trước' sẽ bị ẩn hoặc disabled nếu đang ở trang 1, nút 'Trang sau' sẽ bị ẩn nếu đang ở trang cuối cùng"*.  
Hãy viết đoạn mã HTML/PHP render các nút điều hướng này.
