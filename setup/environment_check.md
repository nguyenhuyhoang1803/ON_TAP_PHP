# HƯỚNG DẪN KIỂM TRA MÔI TRƯỜNG THI (ENVIRONMENT CHECK)

Trước khi bắt đầu ôn luyện hoặc bước vào phòng thi, việc đầu tiên cần làm là kiểm tra xem máy tính của bạn đã sẵn sàng chạy PHP, MySQL và Composer chưa.

---

## 1. Cấu hình Chuẩn trên Laragon

- **Web Server:** Apache (cổng mặc định 80)
- **Database:** MySQL 8.x (cổng mặc định 3306, user `root`, mật khẩu rỗng `""`)
- **PHP:** Phiên bản 8.0 trở lên (khuyên dùng PHP 8.1 / 8.2 / 8.3)
- **Công cụ quản trị CSDL:** phpMyAdmin (truy cập tại `http://localhost/phpmyadmin`)

---

## 2. Các bước Kiểm tra Tự động

Mở Terminal trong VS Code (hoặc Laragon Terminal) và chạy lần lượt 2 script kiểm tra:

### Bước 1: Kiểm tra PHP & Tiện ích mở rộng
```powershell
& "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" setup/php_check.php
# Hoặc nếu máy bạn đã có PHP trong PATH:
php setup/php_check.php
```

Script này sẽ tự động kiểm tra:
1. Phiên bản PHP hiện tại (có hỗ trợ cú pháp PHP 8.x không).
2. Các extension sống còn: `pdo`, `pdo_mysql`, `mbstring`, `session`, `json`, `fileinfo`.
3. Giới hạn upload file trong `php.ini`.

---

### Bước 2: Kiểm tra Kết nối MySQL & Bảng mã utf8mb4
```powershell
& "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" setup/database_check.php
# Hoặc nếu máy bạn đã có PHP trong PATH:
php setup/database_check.php
```

Script này sẽ tự động:
1. Kết nối thử đến MySQL với tài khoản `root` và mật khẩu rỗng.
2. Kiểm tra xem MySQL có đang chạy không (nếu chưa bật thì nhắc khởi động Laragon).
3. Tạo thử một bảng tạm, chèn dữ liệu tiếng Việt có dấu nháy đơn (`Patrick O'Connor - Tiếng Việt`) để kiểm tra `utf8mb4`.
4. Dọn dẹp bảng tạm và trả về kết quả `SUCCESS` hoặc hướng dẫn khắc phục.

---

## 3. Khắc Phục Các Sự Cố Thường Gặp

| Hiện tượng | Nguyên nhân | Cách khắc phục |
|---|---|---|
| `php : The term 'php' is not recognized...` | Chưa thêm đường dẫn PHP vào biến môi trường PATH của Windows | Dùng đường dẫn đầy đủ của Laragon: `& "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe"` hoặc mở Menu Laragon -> `Tools` -> `Quick Settings` -> bật `Path` |
| `SQLSTATE[HY000] [2002] No connection could be made...` | Dịch vụ MySQL chưa được khởi động | Mở bảng điều khiển Laragon và bấm nút **Start All** |
| `Access denied for user 'root'@'localhost'` | MySQL trên máy bạn đã được đặt mật khẩu khác rỗng | Mở file `setup/database_check.php` và điền mật khẩu hiện tại vào biến `$password` |
