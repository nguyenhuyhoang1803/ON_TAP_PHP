# SỔ TAY 28 BẪY LỖI KINH ĐIỂN DỄ MẤT ĐIỂM (COMMON ERRORS)
> Trong phòng thi 60 phút, hơn 70% sinh viên mất điểm không phải vì "không biết làm", mà vì vướng phải các bẫy ngớ ngẩn dưới đây. Hãy đọc thật kỹ triệu chứng, nguyên nhân, cách sửa và mẹo nhớ!

---

## 1. Nhóm Lỗi PHP Cơ bản, Form & Output

### 1. Dùng `empty()` sai lầm với số 0 hoặc chuỗi `"0"`
- **Triệu chứng:** Người dùng nhập số lượng là `0`, điểm số là `0`, nhưng hệ thống lại báo lỗi: "Vui lòng không để trống!".
- **Nguyên nhân:** Trong PHP, `empty(0)`, `empty("0")`, `empty(false)` đều trả về `true`.
- **Cách sửa:**
  ```php
  // SAI:
  if (empty($_POST['score'])) { $errors[] = "Điểm không được để trống"; }
  
  // ĐÚNG:
  $score = trim($_POST['score'] ?? '');
  if ($score === '') { $errors[] = "Điểm không được để trống"; }
  ```
- **Mẹo nhớ:** *Kiểm tra chuỗi rỗng thì so sánh chính xác `=== ''`, đừng dùng `empty()` cho số.*

---

### 2. Quên Escape HTML (`htmlspecialchars`) gây lỗi XSS
- **Triệu chứng:** Khi người dùng nhập tên là `<script>alert(1)</script>` hoặc `Patrick O'Connor`, giao diện bị popup hoặc bị vỡ layout (mất chữ sau dấu nháy đơn trong ô input).
- **Nguyên nhân:** Dữ liệu in thẳng ra HTML mà không qua xử lý các ký tự `<`, `>`, `"`, `'`, `&`.
- **Cách sửa:**
  ```php
  // SAI:
  <input type="text" name="name" value="<?= $name ?>">
  
  // ĐÚNG:
  <input type="text" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>">
  ```
- **Mẹo nhớ:** *Cứ hễ thấy `<?= $bien ?>` là phải bọc `htmlspecialchars` hoặc viết hàm tắt `e($bien)`.*

---

### 3. Không validate `$_GET` / `$_POST` trước khi dùng
- **Triệu chứng:** Server báo cảnh báo `Warning: Undefined array key "page"` hoặc `TypeError`.
- **Nguyên nhân:** Giả định người dùng luôn truyền tham số lên URL hoặc Form.
- **Cách sửa:** Luôn dùng toán tử Null Coalescing `??` và ép kiểu/validate:
  ```php
  $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT) ?: 1;
  ```
- **Mẹo nhớ:** *Mọi input từ client đều là "chất độc" cho tới khi được validate.*

---

## 2. Nhóm Lỗi Session, Cookie & Điều Hướng

### 4. Quên gọi `session_start()` ở đầu file
- **Triệu chứng:** Mảng `$_SESSION` luôn rỗng, đăng nhập xong chuyển trang vẫn bắt đăng nhập lại.
- **Nguyên nhân:** PHP cần `session_start()` để đọc Cookie `PHPSESSID` và load dữ liệu phiên làm việc từ ổ cứng vào bộ nhớ.
- **Cách sửa:** Đặt `session_start();` ở dòng đầu tiên của file (trước mọi mã PHP khác).
- **Mẹo nhớ:** *Cứ đụng đến `$_SESSION` là dòng đầu tiên phải có `session_start()`.*

---

### 5. Gọi `header('Location: ...')` sau khi đã có output (echo/HTML)
- **Triệu chứng:** Báo lỗi `Warning: Cannot modify header information - headers already sent by (output started at ...)`.
- **Nguyên nhân:** Header HTTP phải được gửi trước phần Body. Nếu đã có khoảng trắng, thẻ HTML hoặc lệnh `echo`, header không gửi được nữa.
- **Cách sửa:**
  - Đưa toàn bộ khối xử lý logic redirect lên trên cùng của file, trước thẻ `<!DOCTYPE html>`.
  - Kiểm tra xem file có khoảng trắng thừa trước thẻ `<?php` hay không.
- **Mẹo nhớ:** *Xử lý chuyển trang trước, in HTML sau.*

---

### 6. Quên `exit` sau `header('Location: ...')` (Lỗ hổng bảo mật nghiêm trọng)
- **Triệu chứng:** Người chưa đăng nhập vẫn thấy server thực hiện xóa dữ liệu, gửi email hoặc hiển thị một phần trang bên dưới.
- **Nguyên nhân:** Lệnh `header()` chỉ gửi chỉ thị chuyển trang cho trình duyệt, script PHP bên dưới vẫn tiếp tục chạy đến hết file!
- **Cách sửa:**
  ```php
  if (!isset($_SESSION['user'])) {
      header('Location: login.php');
      exit; // BẮT BUỘC
  }
  ```
- **Mẹo nhớ:** *Đã chuyển hướng (`header`) thì phải dừng xe (`exit`).*

---

## 3. Nhóm Lỗi File Upload & Đọc/Ghi File

### 7. Upload không kiểm tra mã lỗi `$_FILES['file']['error']`
- **Triệu chứng:** Không chọn file nhưng vẫn bấm submit dẫn đến lỗi `Undefined index` hoặc file rác được tạo.
- **Nguyên nhân:** Khi submit form rỗng, `$_FILES['file']['error']` sẽ có giá trị `UPLOAD_ERR_NO_FILE` (giá trị 4).
- **Cách sửa:**
  ```php
  if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
      die("Chưa chọn file hoặc có lỗi upload!");
  }
  ```
- **Mẹo nhớ:** *Bước 1 của upload luôn là kiểm tra `['error'] === UPLOAD_ERR_OK`.*

---

### 8. Upload bị trùng tên file hoặc giữ nguyên tên gốc
- **Triệu chứng:** User A upload ảnh `avatar.png`, User B sau đó upload ảnh `avatar.png` đè mất ảnh của User A; hoặc tên file chứa ký tự tiếng Việt/khoảng trắng làm lỗi đường dẫn ảnh.
- **Nguyên nhân:** Lấy trực tiếp `$_FILES['avatar']['name']` để lưu.
- **Cách sửa:** Đổi tên bằng `uniqid()` kết hợp đuôi mở rộng:
  ```php
  $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
  $filename = uniqid('file_', true) . '.' . strtolower($ext);
  ```
- **Mẹo nhớ:** *Không bao giờ tin tên file gốc của client. Luôn sinh tên ngẫu nhiên.*

---

### 9. Dùng `file_put_contents` bị ghi đè file log
- **Triệu chứng:** Ghi log hoạt động nhưng file log chỉ có duy nhất 1 dòng cuối cùng.
- **Nguyên nhân:** `file_put_contents($file, $data)` mặc định sẽ xóa toàn bộ nội dung cũ và ghi mới.
- **Cách sửa:** Thêm cờ `FILE_APPEND | LOCK_EX`:
  ```php
  file_put_contents('log.txt', $message . PHP_EOL, FILE_APPEND | LOCK_EX);
  ```
- **Mẹo nhớ:** *Ghi log = Ghi nối đuôi (`FILE_APPEND`) + Khóa độc quyền (`LOCK_EX`).*

---

## 4. Nhóm Lỗi SQL & PDO

### 10. Nối chuỗi trực tiếp vào câu lệnh SQL (Dính SQL Injection)
- **Triệu chứng:** Người dùng nhập `' OR '1'='1` vào ô tìm kiếm thì lộ toàn bộ database, hoặc nhập `Patrick O'Connor` thì bị vỡ cú pháp SQL syntax error.
- **Nguyên nhân:** Ghép biến PHP trực tiếp vào câu SQL: `"SELECT * FROM users WHERE name = '$name'"`.
- **Cách sửa:** 100% dùng Prepared Statements:
  ```php
  $stmt = $pdo->prepare("SELECT * FROM users WHERE name = :name");
  $stmt->execute(['name' => $name]);
  ```
- **Mẹo nhớ:** *Trong SQL, không bao giờ dùng dấu `.` để nối biến dữ liệu.*

---

### 11. Bẫy SQLi ở mệnh đề `ORDER BY` (Không thể bind param!)
- **Triệu chứng:** Cố gắng viết `$stmt->prepare("SELECT * FROM products ORDER BY :col :dir")` nhưng câu lệnh không có tác dụng hoặc văng lỗi syntax.
- **Nguyên nhân:** PDO Prepared Statements chỉ tham số hóa được **GIÁ TRỊ (Values)**, không thể tham số hóa tên bảng, tên cột hoặc từ khóa SQL (`ASC`/`DESC`).
- **Cách sửa:** Dùng cơ chế **Whitelist**:
  ```php
  $allowed = ['id', 'price', 'created_at'];
  $col = in_array($_GET['sort'] ?? '', $allowed, true) ? $_GET['sort'] : 'id';
  $dir = (strtoupper($_GET['dir'] ?? '') === 'DESC') ? 'DESC' : 'ASC';
  $stmt = $pdo->prepare("SELECT * FROM products ORDER BY $col $dir");
  ```
- **Mẹo nhớ:** *Cột Sort và Chiều Sort: Bắt buộc dùng whitelist `in_array`.*

---

### 12. Không escape ký tự `%` và `_` trong tìm kiếm `LIKE`
- **Triệu chứng:** Người dùng nhập `%` vào ô tìm kiếm với ý định tìm ký tự phần trăm, nhưng kết quả trả về toàn bộ database.
- **Nguyên nhân:** Trong SQL `LIKE`, `%` là đại diện cho chuỗi bất kỳ, `_` đại diện cho 1 ký tự bất kỳ.
- **Cách sửa:**
  ```php
  $escapedKw = strtr($keyword, ['%' => '\%', '_' => '\_']);
  $stmt->execute(['kw' => "%$escapedKw%"]);
  ```
- **Mẹo nhớ:** *Tìm kiếm LIKE: Dùng `strtr` escape `%` và `_` trước khi bọc `%kw%`.*

---

### 13. Dùng `WHERE` thay cho `HAVING` khi lọc dữ liệu tổng hợp
- **Triệu chứng:** Viết `SELECT dept, COUNT(*) FROM emp WHERE COUNT(*) > 5 GROUP BY dept` bị MySQL báo lỗi `Invalid use of group function`.
- **Nguyên nhân:** Mệnh đề `WHERE` lọc các dòng dữ liệu thô **TRƯỚC KHI** gom nhóm; còn `HAVING` lọc các nhóm **SAU KHI** đã gom nhóm và tính toán hàm tổng hợp (`COUNT`, `SUM`, `AVG`).
- **Cách sửa:** Chuyển điều kiện chứa hàm tổng hợp sang `HAVING`:
  ```sql
  SELECT dept, COUNT(*) FROM emp GROUP BY dept HAVING COUNT(*) > 5;
  ```
- **Mẹo nhớ:** *Điều kiện cột thường để ở `WHERE`, điều kiện hàm tổng hợp (`COUNT/SUM/AVG`) phải đưa vào `HAVING`.*

---

### 14. Dùng `INNER JOIN` khi bài toán yêu cầu `LEFT JOIN`
- **Triệu chứng:** Đề yêu cầu "Liệt kê tất cả khách hàng và số đơn đã mua (kể cả khách chưa mua đơn nào)". Nhưng kết quả lại thiếu mất các khách hàng mới đăng ký.
- **Nguyên nhân:** `INNER JOIN` chỉ giữ lại các dòng có quan hệ khớp ở cả 2 bảng. Khách chưa có đơn thì bị loại bỏ.
- **Cách sửa:** Đổi sang `LEFT JOIN` và dùng `COUNT(o.id)` (không dùng `COUNT(*)` vì `COUNT(*)` sẽ đếm dòng NULL thành 1).
  ```sql
  SELECT c.id, c.name, COUNT(o.id) AS total_orders
  FROM customers c
  LEFT JOIN orders o ON c.id = o.customer_id
  GROUP BY c.id, c.name;
  ```
- **Mẹo nhớ:** *Thấy chữ "tất cả kể cả chưa có" -> Chắc chắn là `LEFT JOIN`.*

---

### 15. Đặt điều kiện bảng phụ trong `WHERE` biến `LEFT JOIN` thành `INNER JOIN`
- **Triệu chứng:** Viết `LEFT JOIN orders o ON c.id = o.customer_id WHERE o.status = 'completed'` nhưng khách chưa có đơn vẫn biến mất hoàn toàn.
- **Nguyên nhân:** Khi khách chưa có đơn, các cột của bảng `orders` nhận giá trị `NULL`. Mệnh đề `WHERE o.status = 'completed'` sẽ loại bỏ các dòng `NULL` này, vô tình biến câu query thành `INNER JOIN`.
- **Cách sửa:** Đưa điều kiện của bảng phụ vào ngay trong mệnh đề `ON`:
  ```sql
  SELECT c.id, c.name, COUNT(o.id)
  FROM customers c
  LEFT JOIN orders o ON c.id = o.customer_id AND o.status = 'completed'
  GROUP BY c.id, c.name;
  ```
- **Mẹo nhớ:** *Lọc bảng phụ trong `LEFT JOIN`: Đặt điều kiện ở `ON`, không đặt ở `WHERE`.*

---

### 16. In trực tiếp lỗi Database ra màn hình khi bắt Exception
- **Triệu chứng:** Viết `catch (PDOException $e) { echo $e->getMessage(); }` làm lộ thông tin database, tên bảng, cấu trúc cột khi có lỗi.
- **Nguyên nhân:** Lỗi này bị trừ điểm bảo mật rất nặng trong barem chấm thi.
- **Cách sửa:** Ghi log vào file và hiển thị thông báo thân thiện cho người dùng:
  ```php
  catch (PDOException $e) {
      error_log($e->getMessage());
      $userError = "Có lỗi xảy ra trong quá trình xử lý. Vui lòng thử lại!";
  }
  ```
- **Mẹo nhớ:** *Giấu lỗi kỹ thuật với người dùng, ghi vào log của lập trình viên.*

---

## 5. Nhóm Lỗi Phân Trang (Pagination)

### 17. Tính sai `OFFSET`
- **Triệu chứng:** Bấm sang trang 1 bị mất dữ liệu, hoặc trang 2 nhảy cóc dữ liệu.
- **Nguyên nhân:** Viết `$offset = $page * $limit` thay vì `($page - 1) * $limit`.
- **Cách sửa:**
  ```php
  $offset = ($page - 1) * $limit; // Trang 1 -> offset 0
  ```
- **Mẹo nhớ:** *Trang 1 luôn bắt đầu từ vị trí số 0.*

---

### 18. Lỗi Total Page bằng 0 khi bảng không có dữ liệu
- **Triệu chứng:** Khi tìm kiếm không ra kết quả, `$totalRows = 0` dẫn đến `$totalPages = ceil(0 / 10) = 0`, làm giao diện phân trang bị âm hoặc vỡ vòng lặp `for`.
- **Cách sửa:** Luôn ép tối thiểu là 1 trang:
  ```php
  $totalPages = max(1, (int)ceil($totalRows / $limit));
  ```
- **Mẹo nhớ:** *Dù không có dữ liệu vẫn phải tính là có 1 trang rỗng.*

---

### 19. Trang `page` vượt quá giới hạn trên/dưới
- **Triệu chứng:** Người dùng cố tình gõ `?page=-5` hoặc `?page=999999` trên URL thì query bị lỗi hoặc trang trống rỗng.
- **Cách sửa:** Thuật toán Kẹp (Clamp):
  ```php
  $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT) ?: 1;
  $page = max(1, min($page, $totalPages));
  ```
- **Mẹo nhớ:** *Kẹp `page` trong đoạn `[1, $totalPages]`.*

---

### 20. Bị mất từ khóa tìm kiếm khi chuyển trang phân trang
- **Triệu chứng:** Đang ở kết quả tìm kiếm "iPhone", bấm sang trang 2 thì danh sách lại hiển thị toàn bộ sản phẩm của hệ thống!
- **Nguyên nhân:** Thẻ link trang 2 viết cứng `<a href="?page=2">` làm mất tham số `keyword` trên URL.
- **Cách sửa:** Dùng `http_build_query()` kết hợp `array_merge()`:
  ```php
  <a href="?<?= http_build_query(array_merge($_GET, ['page' => $p])) ?>"><?= $p ?></a>
  ```
- **Mẹo nhớ:** *Link phân trang: Luôn merge với `$_GET` hiện tại qua `http_build_query`.*

---

## 6. Nhóm Lỗi JavaScript, AJAX & JSON

### 21. Quên `encodeURIComponent()` khi truyền dữ liệu qua URL trong Fetch
- **Triệu chứng:** Tìm kiếm từ khóa có dấu `&` hoặc `+` (ví dụ: `C++` hoặc `Tom & Jerry`) thì server nhận sai chuỗi (mất dấu `+`, tách chuỗi ở dấu `&`).
- **Nguyên nhân:** Các ký tự đặc biệt trong URL không được mã hóa chuẩn URI.
- **Cách sửa:**
  ```javascript
  fetch(`api.php?kw=${encodeURIComponent(keyword)}`)
  ```
- **Mẹo nhớ:** *Cứ ghép biến vào query string của fetch là phải bọc `encodeURIComponent`.*

---

### 22. Dùng `.innerHTML` để render dữ liệu API trả về (Dính XSS)
- **Triệu chứng:** Nếu tên sản phẩm trong CSDL là `<img src=x onerror=alert('Hacked')>`, bảng kết quả sẽ kích hoạt mã độc ngay lập tức.
- **Nguyên nhân:** Dùng `.innerHTML = '<td>' + item.name + '</td>'` khiến trình duyệt thực thi chuỗi như HTML thực thụ.
- **Cách sửa:** Tạo phần tử DOM và dùng `.textContent`:
  ```javascript
  const td = document.createElement('td');
  td.textContent = item.name; // Tự động escape 100%
  tr.appendChild(td);
  ```
- **Mẹo nhớ:** *Render dữ liệu động từ API: Luôn dùng `createElement` + `textContent`.*

---

### 23. Quên không `clearTimeout` trong hàm Debounce
- **Triệu chứng:** Người dùng gõ 5 ký tự liên tiếp, hệ thống gửi đủ cả 5 request Fetch tới server, làm server quá tải và các request phản hồi không theo thứ tự gây giật lag kết quả.
- **Nguyên nhân:** Không hủy timer cũ trước khi tạo timer mới.
- **Cách sửa:**
  ```javascript
  function debounce(fn, delay = 300) {
      let timer;
      return (...args) => {
          clearTimeout(timer); // Xóa bộ đếm cũ nếu người dùng vẫn đang gõ
          timer = setTimeout(() => fn(...args), delay);
      };
  }
  ```
- **Mẹo nhớ:** *Debounce chuẩn: `clearTimeout` trước, `setTimeout` sau.*

---

### 24. Lệnh Debug (`echo`, `var_dump`, khoảng trắng) làm hỏng chuỗi JSON API
- **Triệu chứng:** Frontend báo lỗi `SyntaxError: Unexpected token < in JSON at position 0`.
- **Nguyên nhân:** File PHP xử lý API có lệnh `var_dump($_GET)` hoặc có ký tự thừa bên ngoài thẻ `<?php`, khiến output trả về không phải là JSON thuần.
- **Cách sửa:**
  - Xóa mọi lệnh `var_dump`, `print_r`, `echo`.
  - Khai báo header chuẩn: `header('Content-Type: application/json; charset=utf-8');`.
  - Kết thúc bằng `echo json_encode($data); exit;`.
- **Mẹo nhớ:** *Endpoint API JSON: Chỉ được echo duy nhất 1 lần bằng `json_encode`.*

---

## 7. Nhóm Lỗi OOP, Namespace & Composer

### 25. Nhầm lẫn giữa Abstract Class và Interface
- **Triệu chứng:** Cố tình viết phương thức có nội dung xử lý (body `{...}`) bên trong `interface`, hoặc dùng từ khóa `implements` cho `abstract class`.
- **Nguyên nhân:** Không nắm rõ bản chất:
  - `Interface`: Chỉ là bản hợp đồng (chỉ khai báo chữ ký hàm, không có code thân hàm, mọi hàm đều là `public`). Dùng `implements`. Một class có thể implement nhiều interface.
  - `Abstract class`: Là lớp cha chưa hoàn chỉnh (có thể chứa biến, hàm có thân code, và hàm abstract). Dùng `extends`. Một class chỉ kế thừa được duy nhất 1 lớp cha.
- **Mẹo nhớ:** *Interface là hợp đồng (implements), Abstract Class là một nửa lớp cha (extends).*

---

### 26. Quên chạy `composer dump-autoload` sau khi tạo file class mới
- **Triệu chứng:** Báo lỗi `Fatal error: Uncaught Error: Class "App\Services\CartService" not found`.
- **Nguyên nhân:** Khi tạo file mới hoặc đổi namespace, classmap của Composer chưa được đồng bộ lại.
- **Cách sửa:** Mở terminal chạy:
  ```bash
  composer dump-autoload
  ```
- **Mẹo nhớ:** *Tạo class mới hoặc đổi tên namespace -> Chạy ngay `composer dump-autoload`.*

---

### 27. Sai lệch đường dẫn thư mục và Namespace PSR-4
- **Triệu chứng:** Chạy `composer dump-autoload` rồi nhưng PHP vẫn không tìm thấy class.
- **Nguyên nhân:** Trong `composer.json` cấu hình `"App\\": "src/"`, nhưng thư mục thật trên đĩa lại là `src/models/User.php` (chữ `m` thường) trong khi namespace lại khai báo `namespace App\Models;` (chữ `M` hoa). Trên môi trường Windows có thể không báo lỗi, nhưng trên môi trường chấm bài Linux sẽ báo lỗi ngay lập tức.
- **Cách sửa:** Đảm bảo chính xác từng ký tự hoa/thường giữa Namespace và Thư mục.
- **Mẹo nhớ:** *PSR-4: Tên namespace hoa/thường phải khớp từng milimet với tên folder trên ổ cứng.*

---

### 28. Quên set charset `utf8mb4` trong chuỗi kết nối PDO
- **Triệu chứng:** Dữ liệu tiếng Việt lưu vào MySQL hoặc hiển thị ra màn hình bị biến thành dấu hỏi chấm `???` hoặc `Nguy?n V?n A`.
- **Nguyên nhân:** Thiếu tham số `charset=utf8mb4` trong DSN khi khởi tạo đối tượng PDO.
- **Cách sửa:**
  ```php
  $dsn = "mysql:host=localhost;dbname=mydb;charset=utf8mb4";
  ```
- **Mẹo nhớ:** *DSN của PDO bắt buộc phải có `charset=utf8mb4`.*
