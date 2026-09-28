# CHECKPOINT 07: JAVASCRIPT, FETCH API & JSON

> **Điều kiện vượt qua:** Đạt từ 75% trở lên (>= 7.5/10 điểm).

---

### PHẦN A – LÝ THUYẾT NHANH (5 câu x 0.6đ = 3.0đ)
1. Kỹ thuật Debounce hoạt động như thế nào và tại sao lại dùng `clearTimeout()` trước khi gán `setTimeout()`?
2. Nếu người dùng tìm kiếm từ khóa có dấu `&` (ví dụ `Tom & Jerry`), nếu không bọc `encodeURIComponent()` thì backend PHP sẽ nhận được giá trị gì?
3. Tại sao trong Fetch API, việc kiểm tra `if (!response.ok)` lại quan trọng trước khi gọi `response.json()`?
4. Trình bày sự khác nhau về cơ chế bảo mật giữa `element.textContent = data;` và `element.innerHTML = data;`.
5. Trong file PHP trả về JSON cho Fetch API, tại sao phải có lệnh `exit;` ngay sau `echo json_encode($data);`?

---

### PHẦN B – ĐỌC CODE & BẮT BUG (2 đoạn code x 1.0đ = 2.0đ)

#### Bug 1:
```javascript
input.addEventListener('input', (e) => {
    setTimeout(async () => {
        const res = await fetch('/api?q=' + e.target.value);
        const data = await res.json();
        render(data);
    }, 300);
});
```
*Đoạn code trên có thực hiện đúng Debounce không? Tại sao?*

#### Bug 2:
```php
<?php
// api.php
include 'db.php';
echo "<!-- Bắt đầu truy vấn -->";
$stmt = $pdo->query("SELECT * FROM items");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($data);
```
*Frontend gọi API này sẽ gặp lỗi gì? Sửa lại thế nào?*

---

### PHẦN C – VIẾT CODE THỰC HÀNH (3.0đ)
Viết đoạn mã JavaScript hoàn chỉnh:
- Có hàm `debounce(fn, 300)`.
- Lắng nghe ô `<select id="categorySelect">` (sự kiện `change`).
- Khi người dùng đổi danh mục, gọi Fetch API `get_items.php?cat_id=X`.
- Render dữ liệu dạng bảng có các cột: `ID`, `Tên món`, `Đơn giá`. Dùng `createElement` và `textContent`.

---

### PHẦN D – BIẾN THỂ ĐỀ THI (2.0đ)
Nếu đề bài yêu cầu: *"Trong lúc hệ thống đang fetch dữ liệu từ server, phải hiển thị một biểu tượng hoặc dòng chữ 'Đang tải dữ liệu...' và vô hiệu hóa ô tìm kiếm (disabled) để người dùng không gõ tiếp, sau khi fetch xong thì mở lại"*.  
Hãy viết bổ sung khối `try / catch / finally` để xử lý trạng thái UI này.
