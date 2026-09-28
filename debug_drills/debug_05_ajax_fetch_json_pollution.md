# DEBUG-05: PHP WARNING LÀM HỎNG JSON VÀ LỖ HỔNG INNERHTML
> **Thời gian:** 4 phút | **Nhiệm vụ:** Tìm 3 lỗi làm ứng dụng AJAX không hoạt động hoặc bị tấn công XSS.

---

## 1. ĐOẠN CODE BỊ LỖI (BUGGY CODE)

**File `api.php`:**
```php
<?php
// Lấy dữ liệu
echo "Đang xử lý..."; // Debug log
$data = ['status' => 'ok', 'user' => $_GET['name']];
echo json_encode($data);
```

**File `app.js`:**
```javascript
const input = document.getElementById('search-box');
const result = document.getElementById('result');

input.addEventListener('keyup', () => {
    fetch('api.php?name=' + input.value)
        .then(res => res.json())
        .then(data => {
            result.innerHTML = "Xin chào: " + data.user;
        });
});
```

---

## 2. PHÂN TÍCH 3 LỖI TRÍ MẠNG
1. **Lỗi 1 (Làm hỏng chuỗi JSON bằng `echo` thừa):** Lệnh `echo "Đang xử lý...";` khiến response trả về trở thành `Đang xử lý...{"status":"ok",...}`. Khi JavaScript gọi `res.json()`, hàm parse sẽ bị văng lỗi cú pháp JSON `SyntaxError: Unexpected token 'Đ', "Đang xử lý"...`.
2. **Lỗi 2 (Thiếu Header Content-Type & encodeURIComponent):** File `api.php` không đặt `header('Content-Type: application/json; charset=utf-8');`. Phía client không bọc `encodeURIComponent(input.value)` dẫn đến lỗi khi từ khóa có ký tự đặc biệt (`&`, `?`, `#`).
3. **Lỗi 3 (Lỗ hổng XSS qua `innerHTML`):** Đưa trực tiếp `data.user` vào `result.innerHTML`. Nếu người dùng nhập `<img src=x onerror=alert(1)>`, đoạn mã độc sẽ được thực thi ngay trong trình duyệt.

---

## 3. CODE ĐÃ SỬA CHUẨN (FIXED)

**File `api.php`:**
```php
<?php
// SỬA: Đặt Header JSON và KHÔNG echo bất kỳ ký tự nào trước/sau json_encode
header('Content-Type: application/json; charset=utf-8');

$name = trim($_GET['name'] ?? '');
$data = [
    'status' => 'ok', 
    'user'   => $name
];

echo json_encode($data, JSON_UNESCAPED_UNICODE);
exit;
```

**File `app.js`:**
```javascript
let timer = null;
const input = document.getElementById('search-box');
const result = document.getElementById('result');

// SỬA: Dùng sự kiện input + Debounce 300ms
input.addEventListener('input', () => {
    clearTimeout(timer);
    const val = input.value.trim();

    timer = setTimeout(() => {
        // SỬA: Bọc encodeURIComponent
        fetch('api.php?name=' + encodeURIComponent(val))
            .then(res => {
                if (!res.ok) throw new Error('HTTP error ' + res.status);
                return res.json();
            })
            .then(data => {
                // SỬA: Dùng textContent an toàn tuyệt đối với XSS
                result.textContent = "Xin chào: " + data.user;
            })
            .catch(err => console.error('Fetch error:', err));
    }, 300);
});
```
