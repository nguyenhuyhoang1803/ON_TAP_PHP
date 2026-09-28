# SPEED DRILL 7M-03: VANILLA JS FETCH + DEBOUNCE 300MS
> **Thời gian tối đa:** 7 phút | **Mục tiêu:** Viết chuẩn xác thuật toán Debounce và hiển thị DOM an toàn bằng JavaScript thuần.

---

## 1. ĐỀ BÀI
Tạo file `live_search.html`:
- Có 1 ô input `#search-box` và 1 thẻ `ul#result-box`.
- Bắt sự kiện `input` trên `#search-box`.
- Triển khai Debounce 300ms bằng `clearTimeout` và `setTimeout`.
- Gửi `fetch('search_api.php?kw=' + encodeURIComponent(keyword))` lấy mảng JSON danh sách sản phẩm `[{id, name, price}, ...]`.
- Render danh sách `<li>` an toàn bằng `textContent`. Nếu rỗng, hiển thị "Không có kết quả".

---

## 2. LỜI GIẢI MẪU (SOLUTION)
```html
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Debounce Search</title></head>
<body>
    <input type="text" id="search-box" placeholder="Tìm kiếm nhanh..." style="padding: 6px; width: 300px;">
    <ul id="result-box"></ul>

    <script>
        let timer = null;
        const input = document.getElementById('search-box');
        const list = document.getElementById('result-box');

        input.addEventListener('input', function() {
            clearTimeout(timer);
            const kw = this.value.trim();

            timer = setTimeout(() => {
                fetch('search_api.php?kw=' + encodeURIComponent(kw))
                    .then(res => res.json())
                    .then(data => {
                        list.innerHTML = '';
                        if (!data || data.length === 0) {
                            list.innerHTML = '<li>Không có kết quả</li>';
                            return;
                        }
                        data.forEach(item => {
                            const li = document.createElement('li');
                            li.textContent = `${item.name} - ${item.price} đ`;
                            list.appendChild(li);
                        });
                    })
                    .catch(err => console.error('Fetch error:', err));
            }, 300);
        });
    </script>
</body>
</html>
```
