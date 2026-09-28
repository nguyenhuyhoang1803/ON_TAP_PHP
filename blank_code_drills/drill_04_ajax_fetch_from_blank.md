# BLANK-04: XÂY DỰNG FULLSTACK AJAX FETCH TỪ FILE TRẮNG
> **Thời gian:** 8 phút | **Yêu cầu:** Tự viết 1 file PHP trả JSON và 1 file HTML gọi Fetch với Debounce 300ms.

---

## 1. YÊU CẦU BÀI TẬP
1. Tạo file `api_filter.php`:
   - Mảng mẫu: `[['id'=>1, 'name'=>'iPhone 15'], ['id'=>2, 'name'=>'Samsung S24'], ['id'=>3, 'name'=>'iPad Air']]`.
   - Nhận GET `q`. Nếu có `q`, lọc theo tên không phân biệt hoa thường bằng `mb_stripos`.
   - Đặt header JSON UTF-8 và `echo json_encode()`.
2. Tạo file `index_search.html`:
   - Input tìm kiếm. Lắng nghe `input`.
   - Viết debounce 300ms.
   - Dùng `fetch()` gọi API, xóa danh sách cũ và thêm `li` an toàn bằng `textContent`.

---

## 2. LỜI GIẢI MẪU ĐỐI CHIẾU

**File `api_filter.php`:**
```php
<?php
header('Content-Type: application/json; charset=utf-8');

$items = [
    ['id' => 1, 'name' => 'iPhone 15 Pro'],
    ['id' => 2, 'name' => 'Samsung Galaxy S24'],
    ['id' => 3, 'name' => 'Xiaomi 14 Ultra'],
    ['id' => 4, 'name' => 'iPad Air M2'],
];

$q = trim($_GET['q'] ?? '');
if ($q === '') {
    echo json_encode($items, JSON_UNESCAPED_UNICODE);
    exit;
}

$matched = array_filter($items, fn($item) => mb_stripos($item['name'], $q) !== false);
echo json_encode(array_values($matched), JSON_UNESCAPED_UNICODE);
exit;
```

**File `index_search.html`:**
```html
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Ajax Search</title></head>
<body>
    <input type="text" id="kw" placeholder="Tìm sản phẩm..." style="padding: 6px; width: 250px;">
    <ul id="list"></ul>

    <script>
        let timer = null;
        const kw = document.getElementById('kw');
        const list = document.getElementById('list');

        kw.addEventListener('input', function() {
            clearTimeout(timer);
            const val = this.value.trim();

            timer = setTimeout(() => {
                fetch('api_filter.php?q=' + encodeURIComponent(val))
                    .then(res => res.json())
                    .then(data => {
                        list.innerHTML = '';
                        if (!data || data.length === 0) {
                            list.innerHTML = '<li>Không tìm thấy</li>';
                            return;
                        }
                        data.forEach(item => {
                            const li = document.createElement('li');
                            li.textContent = item.name;
                            list.appendChild(li);
                        });
                    })
                    .catch(err => console.error(err));
            }, 300);
        });
    </script>
</body>
</html>
```
