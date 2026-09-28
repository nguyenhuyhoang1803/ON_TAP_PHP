# BÀI 01: PHP CĂN BẢN (PHP CORE & SYNTAX)

## A. Mục tiêu
Sau bài này, bạn có thể:
1. Thao tác thuần thục với biến, các kiểu dữ liệu cơ bản (int, float, string, boolean, array, null).
2. Xử lý mảng tuần tự (indexed array) và mảng kết hợp (associative array) bằng `foreach`.
3. Phân biệt chính xác toán tử so sánh lỏng lẻo `==` và so sánh nghiêm ngặt `===`.
4. Làm chủ toán tử Null Coalescing `??` để lấy giá trị mặc định an toàn cho `$_GET` / `$_POST`.
5. Tự viết hàm tái sử dụng có khai báo kiểu dữ liệu (type hinting) và giá trị trả về.

---

## B. Bản chất
- **Tại sao cần PHP?** PHP là ngôn ngữ kịch bản chạy phía server (Backend), nhận request từ trình duyệt, tính toán logic, kết nối CSDL, rồi sinh ra mã HTML/JSON gửi trả về client.
- **Khi nào dùng mảng kết hợp (Associative Array)?** Khi dữ liệu biểu diễn một đối tượng có thuộc tính đặt tên (ví dụ: một dòng trong CSDL: `['id' => 1, 'name' => 'Laptop', 'price' => 15000000]`).
- **Nó giải quyết vấn đề gì?** Giúp cấu trúc dữ liệu rõ ràng, dễ truy xuất qua `key` thay vì phải nhớ chỉ số số nguyên `0, 1, 2`.

---

## C. Cú pháp cốt lõi

### 1. Toán tử Null Coalescing (`??`)
```php
// Tránh lỗi "Undefined array key" khi client không truyền biến
$keyword = $_GET['keyword'] ?? '';
$page = (int)($_GET['page'] ?? 1);
```

### 2. So sánh `==` vs `===`
```php
// BẪY CHẾT NGƯỜI:
0 == '0'       // true
0 == 'abc'     // true (trong PHP < 8) hoặc false (PHP 8)
0 === '0'      // false (khác kiểu dữ liệu: int vs string)
null == ''     // true
null === ''    // false
```
*Nguyên tắc:* Luôn dùng `===` và `!==` để tránh ép kiểu ngầm định gây lỗi logic.

### 3. Vòng lặp `foreach` với mảng kết hợp
```php
$students = [
    ['name' => 'Nguyễn Văn An', 'score' => 8.5],
    ['name' => 'Lê Thị Bình', 'score' => 9.2],
];

foreach ($students as $index => $item) {
    echo ($index + 1) . ". " . $item['name'] . " - " . $item['score'] . "\n";
}
```

### 4. Hàm có Type Hinting
```php
function calculateDiscount(float $price, float $percent = 10.0): float {
    return $price * (1 - $percent / 100);
}
```

---

## D. Ví dụ tối giản

```php
<?php
// File: simple_calc.php
function rankScore(float $score): string {
    if ($score >= 8.5) return 'Giỏi';
    if ($score >= 7.0) return 'Khá';
    if ($score >= 5.0) return 'Trung bình';
    return 'Yếu';
}

$candidates = [
    ['name' => 'Hoàng', 'score' => 8.5],
    ['name' => 'Tuấn', 'score' => 6.5],
    ['name' => 'Hương', 'score' => 4.5],
];

foreach ($candidates as $c) {
    echo $c['name'] . ": " . rankScore($c['score']) . "\n";
}
```

---

## E. Luồng tư duy
```text
Khởi tạo dữ liệu (Mảng/Biến)
       ↓
Kiểm tra điều kiện hợp lệ (isset / ?? / type check)
       ↓
Duyệt hoặc Xử lý tính toán (Hàm logic / Vòng lặp foreach)
       ↓
Định dạng kết quả (number_format / Ghép chuỗi)
       ↓
Xuất dữ liệu ra màn hình (echo / HTML template)
```

---

## F. Những lỗi hay gặp
1. **Quên gán giá trị mặc định cho biến từ `$_GET`:** Gây cảnh báo `Warning: Undefined array key`.
2. **Dùng nhầm `for` thay vì `foreach` cho mảng kết hợp:** Mảng kết hợp không có index `0, 1, 2...` liên tục, dùng `for ($i = 0...)` sẽ bị lỗi ngay lập tức.
3. **Quên từ khóa `return` trong hàm:** Hàm không có `return` sẽ trả về `null`.
4. **Nhầm lẫn biến toàn cục và cục bộ:** Trong PHP, hàm không tự nhìn thấy biến bên ngoài trừ khi dùng `global` (hoặc truyền qua tham số - cách khuyên dùng).

---

## G. Cách nhận dạng câu hỏi
- Đề cho một danh sách dữ liệu cứng dạng mảng: *"Cho mảng sản phẩm gồm tên, số lượng, đơn giá. Hãy in ra bảng tổng tiền và tìm sản phẩm có giá cao nhất"* -> **Dùng `foreach`, tạo biến phụ `$maxProduct` và `$totalMoney`**.
- Đề yêu cầu tính toán logic lặp đi lặp lại: *"Viết hàm tính cước vận chuyển theo khối lượng và khoảng cách"* -> **Tạo `function calcShipping(float $weight, float $distance): float`**.

---

## H. Mini challenge
> **Đề bài:** Cho mảng `$orders = [ ['code' => 'HD01', 'qty' => 5, 'price' => 200000], ['code' => 'HD02', 'qty' => 0, 'price' => 150000], ['code' => 'HD03', 'qty' => 2, 'price' => 500000] ];`  
> Viết đoạn code:
> 1. Bỏ qua các đơn hàng có `qty <= 0`.
> 2. Tính tổng tiền của tất cả các đơn hợp lệ (Thành tiền = `qty * price`). Nếu đơn có thành tiền $\ge 1.000.000$, giảm giá 10%.
> 3. In ra mã đơn, thành tiền sau giảm của từng đơn và tổng doanh thu cuối cùng.  
> *(Lời giải có trong thư mục `solutions/mini_challenge_solution.php`). Tự làm trước khi xem!*
