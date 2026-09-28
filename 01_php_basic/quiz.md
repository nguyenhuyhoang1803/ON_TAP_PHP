# CÂU HỎI TRẮC NGHIỆM & TƯ DUY: PHP CĂN BẢN

### Câu 1: Đoạn code sau in ra gì?
```php
$val = "0";
if (empty($val)) {
    echo "A";
} else {
    echo "B";
}
```
- A. In ra "A"
- B. In ra "B"
- C. Lỗi cú pháp
- D. Không in ra gì
*(Giải thích: Tại sao?)*

### Câu 2: Sự khác nhau cơ bản giữa `isset($x)` và `!empty($x)` là gì?

### Câu 3: Toán tử `$a ?? $b` tương đương với cấu trúc toán tử 3 ngôi nào?

### Câu 4: Đoạn code sau có lỗi gì?
```php
$rate = 1.2;
function convertUsdToVnd(float $usd): float {
    return $usd * 25000 * $rate;
}
echo convertUsdToVnd(10);
```

### Câu 5: Đoạn code sau in ra kết quả gì trong PHP 8?
```php
echo (0 == "hello") ? "True" : "False";
```
