# BÀI TẬP RÈN LUYỆN: COMPOSER & PSR-4

## Đề bài: Dựng Khung Dự Án Quản Lý Học Tập với Composer
1. Khởi tạo file `composer.json` cho dự án:
   - Namespace chính: `EduApp\` trỏ vào thư mục `app/`.
2. Tạo các class:
   - `app/Models/Student.php` (namespace `EduApp\Models`): Thuộc tính `id`, `name`, `email`.
   - `app/Repositories/StudentRepository.php` (namespace `EduApp\Repositories`): Chứa mảng dữ liệu sinh viên mẫu, phương thức `getAll()` và `findByEmail($email)`.
   - `app/Controllers/StudentController.php` (namespace `EduApp\Controllers`): Nhận `StudentRepository` qua constructor, in danh sách sinh viên ra bảng.
3. Chạy lệnh `composer dump-autoload` và viết file `public/index.php` chạy toàn bộ luồng.
