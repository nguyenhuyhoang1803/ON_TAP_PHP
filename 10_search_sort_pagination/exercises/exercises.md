# BÀI TẬP RÈN LUYỆN: SEARCH, SORT & PAGINATION

## Đề bài: Quản Lý Đơn Hàng Thương Mại Điện Tử
Xây dựng trang `orders_list.php`:
1. Tìm kiếm theo `customer_name` hoặc `order_code`.
2. Lọc theo trạng thái đơn hàng (`pending`, `shipped`, `cancelled`, `all`).
3. Lọc theo khoảng ngày đặt hàng: `from_date` đến `to_date`.
4. Sắp xếp Whitelist: `order_date`, `total_amount`, `id`.
5. Phân trang 8 đơn / trang.
6. Luôn giữ nguyên trạng thái tìm kiếm và lọc khi click chuyển trang hoặc thay đổi sắp xếp.
