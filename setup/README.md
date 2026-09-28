# THƯ MỤC CẤU HÌNH & KIỂM TRA MÔI TRƯỜNG (SETUP)

Thư mục này chứa các kịch bản kiểm tra tự động nhằm đảm bảo máy thi hoặc môi trường học tập của bạn đáp ứng đầy đủ yêu cầu của bài thi Lập trình mã nguồn mở (PHP 8.x + MySQL + Laragon).

## Danh mục file:
1. `environment_check.md`: Hướng dẫn chi tiết cách kiểm tra thủ công và tự động.
2. `php_check.php`: Script kiểm tra version PHP, các extensions cần thiết (`pdo_mysql`, `mbstring`, `fileinfo`, `session`, `json`) và Composer.
3. `database_check.php`: Script kiểm tra kết nối PDO đến MySQL local, test tạo DB, tạo bảng, insert dữ liệu tiếng Việt có dấu nháy đơn, select kiểm tra và drop dọn dẹp.

## Cách chạy nhanh:
Trong VS Code Terminal:
```powershell
& "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" setup/php_check.php
& "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" setup/database_check.php
```
