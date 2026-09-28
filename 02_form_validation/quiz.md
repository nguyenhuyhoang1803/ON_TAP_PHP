# CÂU HỎI TRẮC NGHIỆM & TƯ DUY: FORM & VALIDATION

### Câu 1: Tại sao nên dùng `trim($_POST['username'] ?? '')` thay vì chỉ dùng `$_POST['username']`?

### Câu 2: Giả sử một hacker nhập vào ô `fullname` chuỗi: `"><script>alert('XSS')</script>`. Nếu ta render ra HTML bằng dòng code:
`<input type="text" name="fullname" value="<?php echo $fullname; ?>">`  
Chuyện gì sẽ xảy ra? Sửa lại thế nào để triệt tiêu lỗ hổng này?

### Câu 3: Hàm `filter_var('0', FILTER_VALIDATE_INT)` trả về giá trị gì? Có bằng `false` không?

### Câu 4: Khi nào nên sử dụng `GET` thay vì `POST` cho form? Hãy nêu 2 trường hợp cụ thể trong đề thi.
