# CHECKPOINT 01: SÁT HẠCH NỀN TẢNG PHP

> **Điều kiện vượt qua:** Đạt từ 75% trở lên (>= 7.5/10 điểm).

---

### PHẦN A – LÝ THUYẾT NHANH (5 câu x 0.6đ = 3.0đ)
1. Trong PHP 8.x, biểu thức `0 == ""` trả về `true` hay `false`? Tại sao?
2. Giả sử biến `$_GET['category']` không tồn tại trên URL, lệnh `$cat = $_GET['category'];` sẽ gây ra cảnh báo gì? Cách viết chuẩn là gì?
3. Khi duyệt mảng kết hợp bằng `foreach ($arr as $k => $v)`, nếu ta thay đổi giá trị `$v = $v * 2;` bên trong vòng lặp thì mảng gốc `$arr` có bị thay đổi không? Muốn thay đổi mảng gốc thì phải làm gì?
4. Nêu sự khác nhau giữa lệnh `break;` và `continue;` trong vòng lặp `foreach`.
5. Tại sao trong bài thi thực hành ta nên dùng hàm `number_format($num, 0, ',', '.')` để hiển thị tiền thay vì in thẳng số thô?

---

### PHẦN B – ĐỌC CODE & BẮT BUG (2 đoạn code x 1.0đ = 2.0đ)

#### Bug 1:
```php
$cart = [
    ['name' => 'Bút', 'price' => 5000],
    ['name' => 'Vở', 'price' => 12000]
];
for ($i = 0; $i < count($cart); $i++) {
    $item = $cart[$i];
    echo $item.name . " giá " . $item.price;
}
```
*Lỗi ở đâu và sửa lại như thế nào?*

#### Bug 2:
```php
function getDiscountRate($memberType) {
    if ($memberType === 'VIP') {
        $discount = 0.2;
    } elseif ($memberType === 'GOLD') {
        $discount = 0.15;
    }
    return $discount;
}
echo getDiscountRate('STANDARD');
```
*Đoạn code trên gặp lỗi gì khi truyền loại thành viên chưa đăng ký? Sửa thế nào?*

---

### PHẦN C – VIẾT CODE THỰC HÀNH (3.0đ)
Viết một hàm `filterAndSortEmployees(array $employees, float $minSalary): array`.
- Mảng `$employees` chứa các phần tử có cấu trúc: `['name' => string, 'salary' => float]`.
- Hàm lọc ra các nhân viên có `salary >= $minSalary`.
- Sắp xếp danh sách nhân viên này theo `salary` giảm dần.
- Trả về danh sách đã lọc và sắp xếp.

---

### PHẦN D – BIẾN THỂ ĐỀ THI (2.0đ)
Nếu đề thi yêu cầu: *"Tìm 3 nhân viên có mức lương cao nhất, nhưng nếu có nhiều người cùng mức lương đứng ở vị trí thứ 3 thì lấy tất cả những người đó"*.  
Hãy mô tả thuật toán giải quyết tình huống đồng hạng (Ties) này bằng lời hoặc mã giả.
