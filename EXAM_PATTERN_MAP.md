# BẢN ĐỒ MẪU TƯ DUY & PHẢN XẠ ĐỀ THI (EXAM PATTERN MAP)
> **Nguyên tắc cốt tử:** Bất kể đề thi đổi tên đề tài thành bán vé máy bay, quản lý bệnh viện, khách sạn hay thư viện, **BẢN CHẤT KỸ THUẬT VÀ LUỒNG XỬ LÝ KHÔNG HỀ THAY ĐỔI**. Hãy kích hoạt ngay chuỗi phản xạ tương ứng dưới đây!

---

## 1. Chuỗi Phản Xạ Kỹ Thuật (Architecture Pipelines)

| Nhóm chức năng | Chuỗi phản xạ xử lý chuẩn (Pipeline) | Cú pháp then chốt |
|---|---|---|
| **Form Xử Lý** | `Form` → `Input` → `Validate` → `Process` → `Output` | `$_SERVER['REQUEST_METHOD'] === 'POST'`, `trim()`, `htmlspecialchars()` |
| **Xác Thực** | `Login` → `Session` → `Guard` → `Redirect` → `Logout` | `session_start()`, `$_SESSION['user']`, `header('Location: ...'); exit;` |
| **Upload Tập Tin** | `Upload` → `Validate` → `Unique Filename` → `Move` → `Append Log` | `$_FILES['f']['error'] === UPLOAD_ERR_OK`, `uniqid()`, `move_uploaded_file()`, `LOCK_EX` |
| **Lập Trình OOP** | `OOP` → `Abstract` → `Extends` → `Interface` → `Polymorphism` | `abstract class`, `interface`, `implements`, `instanceof`, `usort($list, fn($a, $b) => ...)` |
| **Tự Động Nạp** | `Autoload` → `Namespace` → `Path Mapping` → `PSR-4` | `spl_autoload_register()`, `composer.json`, `composer dump-autoload` |
| **Giao Tiếp AJAX** | `AJAX` → `Input` → `Debounce` → `Fetch` → `PHP` → `JSON` → `DOM` | `clearTimeout`, `setTimeout`, `encodeURIComponent`, `json_encode`, `textContent` |
| **Dữ Liệu CRUD** | `CRUD` → `Validate` → `PDO` → `Prepared Statement` → `PRG` | `prepare()`, `execute()`, `header('Location: list.php?msg=ok'); exit;` |
| **Báo Cáo SQL** | `Reporting` → `JOIN` → `GROUP BY` → `HAVING` → `Aggregate` | `LEFT JOIN`, `COALESCE(SUM(...), 0)`, `HAVING COUNT(...) >= X` |
| **Tìm Kiếm Động** | `Search` → `Normalize` → `Build Conditions` → `Prepared Statement` | `strtr($kw, ['%' => '\%', '_' => '\_'])`, `WHERE $whereSql`, `bindValue()` |
| **Sắp Xếp An Toàn** | `Sort` → `Whitelist` | `in_array($_GET['sort'], ['id', 'price'], true) ? $_GET['sort'] : 'id'` |
| **Phân Trang** | `Pagination` → `Count` → `totalPages` → `Clamp Page` → `LIMIT/OFFSET` | `ceil($total / $limit)`, `max(1, min($page, $totalPages))`, `($page - 1) * $limit` |

---

## 2. Từ Khóa Đề Bài → Ánh Xạ Cú Pháp Trong Não

| Đề bài xuất hiện cụm từ... | Ánh xạ ngay tới Cú pháp / Kỹ thuật |
|---|---|
| *"Kể cả không có dữ liệu liên quan"* | `LEFT JOIN` (Bắt buộc dùng `COUNT(bảng_phụ.id)` thay vì `COUNT(*)`) |
| *"Chưa từng phát sinh / Chưa từng mua / Chưa có"* | `LEFT JOIN ... WHERE bảng_phụ.id IS NULL` hoặc `NOT EXISTS (...)` |
| *"Tổng / Số lượng / Trung bình theo từng nhóm"* | `GROUP BY cột_nhóm` |
| *"Lọc kết quả sau khi đã tính tổng/đếm"* | `HAVING` (Tuyệt đối không dùng `WHERE`) |
| *"Cao hơn mức trung bình của toàn trường"* | Subquery so sánh: `WHERE score > (SELECT AVG(score) FROM ...)` |
| *"Đồng hạng cao nhất / Bằng với mức cao nhất"* | **KHÔNG DÙNG `LIMIT 1` MÙ QUÁNG!** Dùng Subquery so sánh với `MAX`: `HAVING total = (SELECT MAX(...) FROM ...)` |
| *"Sau khi dừng gõ 300ms mới tìm"* | Kỹ thuật `Debounce` bằng `clearTimeout` và `setTimeout` |
| *"Không tải lại trang"* | JavaScript `fetch()` nhận response JSON và cập nhật DOM |
| *"Giữ nguyên điều kiện khi sang trang 2"* | `http_build_query(array_merge($_GET, ['page' => $p]))` |
| *"Chống submit lặp khi F5"* | Mô hình PRG: Xử lý POST xong bắt buộc `header('Location: ...'); exit;` |
| *"Bắt lỗi khi thêm email đã tồn tại"* | `catch (PDOException $e) { if ($e->getCode() == 23000) { ... } }` |

---

## 3. Đề Thay Đổi Nghiệp Vụ Nhưng Bản Chất Kỹ Thuật Không Đổi

Nhiều thí sinh bị hoảng khi thấy đề thi đổi bối cảnh. Bảng đối chiếu dưới đây chứng minh: **Mọi đề thi đều cùng một bản chất kỹ thuật**:

| Nghiệp vụ A: Bán Hàng | Nghiệp vụ B: Đào Tạo | Nghiệp vụ C: Bệnh Viện | Bản chất kỹ thuật & SQL |
|---|---|---|---|
| Khách hàng (`customers`) | Sinh viên (`students`) | Bác sĩ (`doctors`) | Bảng thực thể chính |
| Đơn hàng (`orders`) | Điểm thi (`grades`) | Toa thuốc (`prescriptions`) | Bảng quan hệ phụ thuộc |
| Khách chưa mua đơn nào | Sinh viên chưa có điểm | Bác sĩ chưa kê toa nào | `LEFT JOIN ... WHERE o.id IS NULL` |
| Doanh thu theo khách hàng | Điểm trung bình sinh viên | Tiền thuốc theo bác sĩ | `LEFT JOIN ... GROUP BY c.id` kèm `SUM/AVG` |
| Khách tiêu nhiều tiền nhất | Sinh viên thủ khoa | Bác sĩ kê nhiều tiền nhất | Subquery tìm `MAX` xử lý đồng hạng |
| Tìm theo tên khách hàng | Tìm theo mã/tên sinh viên | Tìm theo tên bác sĩ/CCCD | Prepared Statement: `WHERE name LIKE :kw` |
| Phân trang danh sách đơn | Phân trang danh sách SV | Phân trang hồ sơ bệnh án | `LIMIT :limit OFFSET :offset` + Whitelist |
| Thêm sản phẩm (trùng mã) | Thêm SV (trùng MSSV) | Tiếp nhận (trùng CCCD) | Bắt ngoại lệ PDO mã lỗi `23000` |
| Tải ảnh sản phẩm | Tải chứng chỉ SV | Tải hồ sơ bệnh án PDF | `$_FILES`, `uniqid()`, `move_uploaded_file` |

---

## 4. Các Pattern Kết Hợp Thường Gặp (Combined Pipelines)

Đề thi 6 câu / 60 phút rất chuộng ghép từ 2 kỹ năng trở lên vào một câu để rút ngắn số lượng câu hỏi mà vẫn phủ đủ kiến thức:

### 4.1. Pattern: Session + Upload (Xác thực & Tải tệp bảo vệ)
- **Luồng xử lý:** `Login` → `Session Start` → `Auth Guard Check` → `Validate File ($_FILES, ext, size)` → `Generate Unique Name` → `move_uploaded_file` → `Append Log (LOCK_EX)`
- **Điểm chốt:** Nếu chưa login thì `header('Location: ...'); exit;`. Đổi tên file ngẫu nhiên trước khi lưu, ghi log có cờ `FILE_APPEND | LOCK_EX`.

### 4.2. Pattern: AJAX + PHP (Tìm kiếm tức thì & DOM An toàn)
- **Luồng xử lý:** `Input Event` → `Debounce (clearTimeout/setTimeout 300ms)` → `fetch(url + encodeURIComponent(kw))` → `PHP Endpoint (JSON header + json_encode)` → `JavaScript Response Parse` → `Render via .textContent / createElement`
- **Điểm chốt:** Client debounce 300ms, Server cấm echo HTML trước `json_encode`, DOM cấm dùng `.innerHTML` với dữ liệu người dùng.

### 4.3. Pattern: PDO + Search + Pagination (Phân trang động)
- **Luồng xử lý:** `Normalize Input (kw, sort, dir, page)` → `Build Shared WHERE & Params` → `Execute COUNT Query` → `Calculate totalPages & Clamp page` → `Calculate OFFSET` → `Execute DATA Query with LIMIT/OFFSET (bind PDO::PARAM_INT)` → `Render Table + Pagination Links (http_build_query)`
- **Điểm chốt:** Dùng chung `$where` và `$params` cho cả 2 câu query. Luôn clamp `$page = max(1, min($page, $totalPages))`.

### 4.4. Pattern: Repository Design (Mẫu thiết kế kho dữ liệu)
- **Luồng xử lý:** `class Repository(PDO $pdo)` → `Normalize search & filter parameters` → `Build condition string & parameters array` → `COUNT query (tổng bản ghi)` → `DATA query (lấy dữ liệu có sắp xếp và phân trang)` → `Return structured array ['total' => ..., 'data' => ...]`
- **Điểm chốt:** Tách rời logic truy vấn DB ra khỏi tầng hiển thị (View/Controller).

### 4.5. Pattern: SQL Reporting (Báo cáo tổng hợp đa bảng)
- **Luồng xử lý:** `FROM Primary Table` → `LEFT JOIN Secondary Tables` → `WHERE (lọc dòng thô)` → `GROUP BY Primary Key` → `HAVING (lọc theo kết quả aggregate SUM/COUNT/AVG)` → `ORDER BY Aggregated Column DESC`
- **Điểm chốt:** Bảng phụ có thể rỗng nên bắt buộc dùng `LEFT JOIN` và bọc `COALESCE(SUM(...), 0)`.

---

## 5. Các Pattern Nâng Cao Mở Rộng (Kế Thừa Từ Đề 150 Phút)

> [!NOTE]
> Các pattern dưới đây được đúc kết từ các đề thi mở rộng 150 phút. Dù đề 60 phút có thể không ra trọn vẹn cả bài lớn, các mảnh ghép kỹ thuật này rất hay được đưa vào làm câu hỏi điểm 9 - 10 hoặc bẫy logic:

### 5.1. File Statistics (Thống kê và phân tích file log)
- **Luồng:** Đọc file log theo dòng (`file()` hoặc `fgets()`) → `explode()` hoặc Regex parse từng trường → Đưa vào mảng đếm tần suất (`$stats[$key]++`) → Tìm giá trị xuất hiện nhiều nhất (`max()`) → Xử lý đồng hạng (lấy tất cả các key có tần suất bằng `max`).

### 5.2. Unicode Search (Tìm kiếm tiếng Việt không dấu / không phân biệt hoa thường)
- **Vấn đề:** Các hàm `strtolower` hay `stripos` chuẩn của PHP có thể bị lỗi khi xử lý ký tự Unicode tiếng Việt có dấu.
- **Giải pháp:** Luôn sử dụng các hàm đa byte:
  ```php
  mb_stripos($haystack, $needle) !== false
  mb_strtolower($string, 'UTF-8')
  ```

### 5.3. Order Aggregation (Tổng hợp chi tiết đơn hàng đa cấp)
- **Cấu trúc:** `orders` LEFT JOIN `order_details`
- **Cú pháp chuẩn:**
  ```sql
  SELECT 
      o.id,
      o.order_code,
      COUNT(od.id) AS total_items,
      COALESCE(SUM(od.quantity), 0) AS total_quantity,
      COALESCE(SUM(od.quantity * od.price), 0) AS total_amount
  FROM orders o
  LEFT JOIN order_details od ON o.id = od.order_id
  GROUP BY o.id, o.order_code;
  ```

### 5.4. Filter by Aggregate (Lọc theo giá trị sau tổng hợp)
- Khi đề yêu cầu: *"Chỉ lấy những đơn hàng có tổng tiền > 1.000.000 đ"* hoặc *"Những khách hàng mua trên 5 sản phẩm"*:
- **Bắt buộc dùng `HAVING`:**
  ```sql
  HAVING total_amount > 1000000 AND total_quantity > 5
  ```
  *(Tuyệt đối không đưa điều kiện này vào mệnh đề `WHERE`)*.

### 5.5. Grand Total (Tổng doanh thu toàn bộ tập kết quả tìm kiếm)
- **Bẫy phổ biến:** Thí sinh thường dùng vòng lặp `foreach` cộng dồn tiền của danh sách sản phẩm hiển thị trên trang hiện tại (`$items`) để in dòng "Tổng doanh thu".
- **Hậu quả:** Sai bản chất! Người dùng đang xem trang 1 của 10 trang, số tiền đó chỉ là tổng của trang 1 chứ không phải tổng doanh thu của toàn bộ kết quả lọc.
- **Giải pháp đúng:** Chạy một câu query tính tổng riêng biệt trên toàn bộ điều kiện `$where`:
  ```php
  $sumStmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders $whereSql");
  $sumStmt->execute($params);
  $grandTotal = (float)$sumStmt->fetchColumn();
  ```

