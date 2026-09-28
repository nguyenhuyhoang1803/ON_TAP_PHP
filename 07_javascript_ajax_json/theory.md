# BÀI 07: JAVASCRIPT, FETCH API & REALTIME SEARCH

## A. Mục tiêu
Sau bài này, bạn có thể:
1. Bắt sự kiện người dùng gõ vào ô tìm kiếm qua `addEventListener('input', ...)`.
2. Kỹ thuật Debounce (trì hoãn ~300ms) bằng `clearTimeout` và `setTimeout` để chống spam request.
3. Gửi yêu cầu bất đồng bộ (Asynchronous) lên Backend bằng `fetch()` có `encodeURIComponent()`.
4. Xử lý backend PHP trả về định dạng JSON chuẩn với header `Content-Type: application/json`.
5. Bắt lỗi mạng qua `try/catch` hoặc `.catch()`, hiển thị thông báo thân thiện.
6. Render bảng kết quả an toàn bằng DOM API (`createElement` và `textContent`), triệt tiêu 100% lỗ hổng XSS trong JavaScript.

---

## B. Bản chất
- **Tại sao cần Fetch API?** Thay vì mỗi lần tìm kiếm phải bấm nút Submit và tải lại toàn bộ trang (mất vị trí cuộn, giật màn hình), Fetch API âm thầm gửi request ngầm đến server, nhận dữ liệu JSON và cập nhật đúng bảng dữ liệu.
- **Tại sao cần Debounce?** Nếu người dùng gõ chữ "LAPTOP" (6 ký tự) trong 1 giây, nếu không có debounce thì trình duyệt sẽ bắn 6 request liên tiếp. Debounce đảm bảo: *Chỉ khi nào người dùng ngừng gõ đủ 300ms thì mới gửi duy nhất 1 request*.
- **`textContent` vs `innerHTML`:** `innerHTML` coi chuỗi đưa vào là mã HTML -> Nếu dữ liệu chứa `<img src=x onerror=alert(1)>` thì trang web sẽ bị hack ngay lập tức. `textContent` chỉ coi dữ liệu là văn bản thuần -> Miễn nhiễm hoàn toàn với XSS.

---

## C. Cú pháp cốt lõi

### 1. Hàm Debounce Chuẩn Phòng Thi
```javascript
function debounce(callback, delay = 300) {
    let timerId;
    return (...args) => {
        clearTimeout(timerId); // Hủy hẹn giờ cũ nếu user vẫn đang gõ
        timerId = setTimeout(() => callback(...args), delay);
    };
}
```

### 2. Fetch API & Render An Toàn
```javascript
async function searchProducts(keyword) {
    const tbody = document.getElementById('result-body');
    try {
        const res = await fetch(`api_search.php?kw=${encodeURIComponent(keyword)}`);
        if (!res.ok) throw new Error(`Lỗi HTTP: ${res.status}`);
        const data = await res.json();

        tbody.textContent = ''; // Xóa sạch dữ liệu cũ

        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3">Không tìm thấy sản phẩm nào!</td></tr>';
            return;
        }

        data.forEach(item => {
            const tr = document.createElement('tr');
            
            const tdId = document.createElement('td');
            tdId.textContent = item.id;
            
            const tdName = document.createElement('td');
            tdName.textContent = item.name; // textContent tự escape 100%

            const tdPrice = document.createElement('td');
            tdPrice.textContent = Number(item.price).toLocaleString('vi-VN') + ' đ';

            tr.appendChild(tdId);
            tr.appendChild(tdName);
            tr.appendChild(tdPrice);
            tbody.appendChild(tr);
        });
    } catch (err) {
        console.error('Lỗi khi tải dữ liệu:', err);
    }
}
```

### 3. Backend PHP (API Endpoint)
```php
// File: api_search.php
header('Content-Type: application/json; charset=utf-8');

$kw = trim($_GET['kw'] ?? '');
// Truy vấn CSDL...
$stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE name LIKE :kw LIMIT 10");
$stmt->execute(['kw' => "%$kw%"]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($products);
exit;
```

---

## D. Ví dụ tối giản

```html
<!-- search.html -->
<input type="text" id="kw" placeholder="Gõ tên tìm kiếm...">
<ul id="list"></ul>

<script>
let t;
document.getElementById('kw').addEventListener('input', (e) => {
    clearTimeout(t);
    t = setTimeout(async () => {
        const r = await fetch('api.php?q=' + encodeURIComponent(e.target.value));
        const items = await r.json();
        const ul = document.getElementById('list');
        ul.textContent = '';
        items.forEach(x => {
            const li = document.createElement('li');
            li.textContent = x.name;
            ul.appendChild(li);
        });
    }, 300);
});
</script>
```

---

## E. Luồng tư duy
```text
User gõ ký tự vào ô <input>
          ↓
Sự kiện input kích hoạt Debounce (clearTimeout → setTimeout 300ms)
          ↓
Hết 300ms không gõ thêm
          ↓
fetch(`api.php?kw=${encodeURIComponent(kw)}`)
          ↓
Server PHP nhận $_GET['kw'] → Query PDO → json_encode($data)
          ↓
Trình duyệt nhận Response → res.json()
          ↓
Làm sạch bảng cũ (tbody.textContent = '')
          ↓
Duyệt mảng kết quả → document.createElement('td') → td.textContent = item.name
          ↓
Append vào DOM hiển thị cho người dùng
```

---

## F. Những lỗi hay gặp
1. **Dùng `.innerHTML` gán chuỗi từ server:** Dính lỗ hổng Reflected/Stored XSS.
2. **Không dùng `encodeURIComponent()`:** Khi tìm kiếm các từ khóa có ký tự `&`, `+`, `%`, query string bị cắt cụt hoặc sai lệch dữ liệu.
3. **File PHP API in ra khoảng trắng hoặc `var_dump()`:** Trình duyệt báo lỗi `SyntaxError: Unexpected token < in JSON at position 0`.
4. **Không gọi `clearTimeout(timerId)` trong debounce:** Tất cả các lần gõ phím đều kích hoạt request sau 300ms, không có tác dụng debounce.

---

## G. Cách nhận dạng câu hỏi
- Đề có câu: *"Xây dựng chức năng tìm kiếm thời gian thực (realtime search), kết quả tự cập nhật bên dưới mà không tải lại trang"* -> **JavaScript Fetch API + PHP json_encode + DOM render**.
- Đề có câu: *"Yêu cầu gửi request sau khi người dùng dừng nhập ít nhất 300ms để giảm tải máy chủ"* -> **Kỹ thuật Debounce với setTimeout & clearTimeout**.

---

## H. Mini challenge
> **Đề bài:** Xây dựng tính năng Live Search Sinh Viên:
> 1. File `api_students.php`: Nhận `kw` qua GET. Nếu rỗng thì trả về 5 sinh viên đầu tiên. Nếu có từ khóa thì tìm theo tên hoặc mã sinh viên. Trả về JSON gồm `id`, `code`, `name`, `gpa`.
> 2. File `students.html`: Có ô input `id="searchBox"`. Khi người dùng gõ, áp dụng debounce 300ms, gọi Fetch API và render danh sách ra thẻ `<table>`. Nếu không có kết quả, in một dòng "Không tìm thấy sinh viên nào!".  
> *(Lời giải có trong thư mục `solutions/mini_challenge_solution.php`). Tự làm trước khi xem!*
