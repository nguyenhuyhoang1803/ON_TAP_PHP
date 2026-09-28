# THƯ MỤC THỬ NGHIỆM TỰ DO (SANDBOX)

Đây là không gian dành riêng cho bạn để tự do gõ code, thử nghiệm các đoạn mã PHP, HTML, CSS hoặc JavaScript trước khi áp dụng vào bài tập chính thức.

## Cách chạy thử nhanh một file PHP:
Trong VS Code Terminal, bạn có thể chạy bất kỳ file PHP nào bằng lệnh:
```powershell
& "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" sandbox/test.php
```

Hoặc nếu bạn muốn xem giao diện web trên trình duyệt:
1. Mở Laragon, đảm bảo Apache đang chạy.
2. Hoặc bạn có thể dùng built-in web server cực nhanh của PHP ngay tại thư mục này:
```powershell
& "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" -S localhost:8000 -t sandbox
```
Sau đó truy cập: `http://localhost:8000` trên trình duyệt!
