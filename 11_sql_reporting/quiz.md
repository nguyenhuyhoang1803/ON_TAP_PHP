# CÂU HỎI TRẮC NGHIỆM & TƯ DUY: SQL REPORTING

### Câu 1: Tại sao việc đặt điều kiện lọc ngày của bảng phụ vào mệnh đề `WHERE` lại làm mất các dòng `NULL` của bảng chính trong `LEFT JOIN`?

### Câu 2: Sự khác nhau giữa `COUNT(DISTINCT column_name)` và `COUNT(column_name)` trong câu lệnh thống kê nhiều bảng là gì?

### Câu 3: Hãy giải thích tại sao câu lệnh sau bị lỗi logic khi có 2 khách hàng đồng hạng 1:
`SELECT customer_id, SUM(amount) FROM orders GROUP BY customer_id ORDER BY SUM(amount) DESC LIMIT 1;`
