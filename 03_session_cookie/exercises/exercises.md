# BÀI TẬP RÈN LUYỆN: SESSION & COOKIE

## Bài tập: Giỏ Hàng Session Mini (Shopping Cart)
Xây dựng tính năng giỏ hàng lưu hoàn toàn trong `$_SESSION['cart']`:
1. Danh sách sản phẩm mẫu:
   - ID 1: Bút bi Thiên Long - 5.000 VNĐ
   - ID 2: Vở kẻ ngang 200 trang - 15.000 VNĐ
   - ID 3: Balo học sinh - 250.000 VNĐ
2. Các chức năng cần có:
   - `add_to_cart.php?id=1`: Thêm sản phẩm vào giỏ. Nếu sản phẩm đã có trong giỏ thì tăng số lượng lên 1. Sau đó redirect về `cart.php`.
   - `cart.php`: Hiển thị bảng các sản phẩm trong giỏ: Tên, Số lượng, Đơn giá, Thành tiền. Tính Tổng tiền giỏ hàng.
   - `update_cart.php`: Cập nhật lại số lượng các món trong giỏ (nếu số lượng = 0 thì xóa khỏi giỏ).
   - `clear_cart.php`: Xóa sạch giỏ hàng.
