# CÂU HỎI TRẮC NGHIỆM & TƯ DUY: SQL CORE

### Câu 1: Tại sao trong mệnh đề `LEFT JOIN`, nếu ta dùng `COUNT(*)` thay vì `COUNT(bảng_phụ.id)` thì kết quả lại bị sai đối với các bản ghi không có quan hệ?

### Câu 2: Chỉ ra sự khác biệt giữa hai câu lệnh sau:
- Câu A: `SELECT * FROM A LEFT JOIN B ON A.id = B.a_id WHERE B.status = 1;`
- Câu B: `SELECT * FROM A LEFT JOIN B ON A.id = B.a_id AND B.status = 1;`

### Câu 3: Mệnh đề `HAVING` có thể sử dụng được khi không có mệnh đề `GROUP BY` không?

### Câu 4: Khi nào nên sử dụng `COALESCE(val, 0)` hoặc `IFNULL(val, 0)` trong câu lệnh SQL tính toán?
