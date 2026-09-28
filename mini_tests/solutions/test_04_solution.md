# LỜI GIẢI MINI TEST 04: REALTIME FETCH API & DEBOUNCE

### File 1: `api_phones.php`
```php
<?php
header('Content-Type: application/json; charset=utf-8');

$phones = [
    ['id' => 1, 'name' => 'iPhone 15 Pro Max', 'brand' => 'Apple', 'price' => 29990000, 'ram_gb' => 8],
    ['id' => 2, 'name' => 'Samsung Galaxy S24 Ultra', 'brand' => 'Samsung', 'price' => 27500000, 'ram_gb' => 12],
    ['id' => 3, 'name' => 'Xiaomi 14 Pro', 'brand' => 'Xiaomi', 'price' => 18990000, 'ram_gb' => 12],
    ['id' => 4, 'name' => 'iPhone 13 128GB', 'brand' => 'Apple', 'price' => 13500000, 'ram_gb' => 4],
    ['id' => 5, 'name' => 'OPPO Reno 11 5G', 'brand' => 'OPPO', 'price' => 9990000, 'ram_gb' => 8],
    ['id' => 6, 'name' => '<script>alert("XSS")</script> Test Phone', 'brand' => 'HackerBrand', 'price' => 1000000, 'ram_gb' => 1],
];

$kw = mb_strtolower(trim($_GET['kw'] ?? ''));

if ($kw === '') {
    echo json_encode($phones);
    exit;
}

$filtered = array_values(array_filter($phones, function ($item) use ($kw) {
    return str_contains(mb_strtolower($item['name']), $kw) ||
           str_contains(mb_strtolower($item['brand']), $kw);
}));

echo json_encode($filtered);
exit;
```

---

### File 2: `search_phone.html`
```html
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tìm Kiếm Điện Thoại Realtime</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        #keywordInput { width: 350px; padding: 8px; font-size: 14px; }
        #loadingStatus { display: none; color: #0066cc; margin-left: 10px; font-style: italic; }
        table { width: 650px; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h2>TRA CỨU ĐIỆN THOẠI DI ĐỘNG</h2>
    <input type="text" id="keywordInput" placeholder="Nhập tên máy hoặc hãng sản xuất...">
    <span id="loadingStatus">⏳ Đang tìm kiếm dữ liệu...</span>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên sản phẩm</th>
                <th>Thương hiệu</th>
                <th>RAM</th>
                <th class="text-right">Giá bán</th>
            </tr>
        </thead>
        <tbody id="resultBody">
            <tr><td colspan="5">Đang nạp dữ liệu...</td></tr>
        </tbody>
    </table>

    <script>
        // 1. Kỹ thuật Debounce chuẩn 300ms
        function debounce(fn, delay = 300) {
            let timerId;
            return (...args) => {
                clearTimeout(timerId);
                timerId = setTimeout(() => fn(...args), delay);
            };
        }

        const inputEl = document.getElementById('keywordInput');
        const tbodyEl = document.getElementById('resultBody');
        const loadingEl = document.getElementById('loadingStatus');

        // 2. Hàm fetch và render an toàn
        async function fetchPhones(keyword = '') {
            loadingEl.style.display = 'inline';
            try {
                const url = `api_phones.php?kw=${encodeURIComponent(keyword)}`;
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`Lỗi máy chủ HTTP ${response.status}`);
                }
                const data = await response.json();

                // Làm sạch bảng cũ
                tbodyEl.textContent = '';

                if (data.length === 0) {
                    tbodyEl.innerHTML = '<tr><td colspan="5" style="color:red; text-align:center;">Không tìm thấy điện thoại nào phù hợp!</td></tr>';
                    return;
                }

                // Render bằng DOM API + textContent chống XSS
                data.forEach(item => {
                    const tr = document.createElement('tr');

                    const tdId = document.createElement('td');
                    tdId.textContent = item.id;

                    const tdName = document.createElement('td');
                    tdName.textContent = item.name; // Miễn nhiễm XSS 100%

                    const tdBrand = document.createElement('td');
                    tdBrand.textContent = item.brand;

                    const tdRam = document.createElement('td');
                    tdRam.textContent = item.ram_gb + ' GB';

                    const tdPrice = document.createElement('td');
                    tdPrice.className = 'text-right';
                    tdPrice.textContent = Number(item.price).toLocaleString('vi-VN') + ' đ';

                    tr.appendChild(tdId);
                    tr.appendChild(tdName);
                    tr.appendChild(tdBrand);
                    tr.appendChild(tdRam);
                    tr.appendChild(tdPrice);

                    tbodyEl.appendChild(tr);
                });
            } catch (err) {
                tbodyEl.innerHTML = `<tr><td colspan="5" style="color:red;">Lỗi kết nối: ${err.message}</td></tr>`;
            } finally {
                loadingEl.style.display = 'none';
            }
        }

        // Tải dữ liệu ban đầu
        fetchPhones('');

        // Lắng nghe sự kiện input với Debounce
        inputEl.addEventListener('input', debounce((e) => {
            fetchPhones(e.target.value.trim());
        }, 300));
    </script>
</body>
</html>
```
