# SPEED DRILL 10M-04: AJAX FETCH + ENDPOINT PHP TRẢ JSON TỪ DATABASE
> **Thời gian tối đa:** 10 phút | **Mục tiêu:** Xây dựng đồng thời 2 file: 1 file API PHP kết nối cơ sở dữ liệu trả JSON và 1 file giao diện HTML gọi Fetch.

---

## 1. ĐỀ BÀI
Tạo 2 file:
1. `api_search_user.php`:
   - Kết nối PDO tới database `company_db`.
   - Nhận tham số GET `name`.
   - Lọc theo `fullname LIKE :kw` giới hạn 10 dòng.
   - Trả về JSON UTF-8.
2. `search_user.html`:
   - Input nhập tên.
   - Bắt sự kiện `input` với Debounce 300ms.
   - Render danh sách `<ul><li>` an toàn bằng `textContent`.

---

## 2. LỜI GIẢI MẪU (SOLUTION)

**File `api_search_user.php`:**
```php
<?php
header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = new PDO('mysql:host=localhost;dbname=company_db;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $name = trim($_GET['name'] ?? '');
    if ($name === '') {
        echo json_encode([]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, fullname, email FROM users WHERE fullname LIKE :kw LIMIT 10");
    $stmt->execute([':kw' => "%$name%"]);
    echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
exit;
```

**File `search_user.html`:**
```html
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Tìm Người Dùng</title></head>
<body>
    <h2>Tìm Nhân Viên</h2>
    <input type="text" id="kw" placeholder="Nhập tên..." style="width: 250px; padding: 6px;">
    <ul id="results"></ul>

    <script>
        let timer = null;
        const kw = document.getElementById('kw');
        const list = document.getElementById('results');

        kw.addEventListener('input', function() {
            clearTimeout(timer);
            const val = this.value.trim();

            timer = setTimeout(() => {
                if (val === '') {
                    list.innerHTML = '';
                    return;
                }
                fetch('api_search_user.php?name=' + encodeURIComponent(val))
                    .then(r => r.json())
                    .then(data => {
                        list.innerHTML = '';
                        if (data.length === 0) {
                            list.innerHTML = '<li>Không tìm thấy</li>';
                            return;
                        }
                        data.forEach(u => {
                            const li = document.createElement('li');
                            li.textContent = `${u.fullname} (${u.email})`;
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
