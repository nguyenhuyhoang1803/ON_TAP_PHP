# MINI TEST 06: SQL CORE & BÁO CÁO THỐNG KÊ
- **Thời gian làm bài:** 15 phút
- **Tổng điểm:** 10 điểm
- **Mục tiêu:** Kiểm tra phản xạ phân biệt `INNER JOIN` vs `LEFT JOIN`, gom nhóm `GROUP BY`, lọc điều kiện nhóm `HAVING`, xử lý `NULL` và truy vấn con Subquery xử lý đồng hạng.

---

## CSDL Mẫu: Cửa Hàng Bán Lẻ Thiết Bị Số
```sql
CREATE DATABASE IF NOT EXISTS exam_shop CHARACTER SET utf8mb4;
USE exam_shop;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    order_date DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'completed',
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

---

## Yêu Cầu Thực Hiện (3 Câu hỏi x 3.33đ = 10 điểm)

### Câu 1: Báo Cáo Doanh Thu Theo Danh Mục (Kể Cả Chưa Bán Được)
Viết câu SQL hiển thị:
- Mã danh mục (`category_id`), Tên danh mục (`category_name`), Tổng số lượng sản phẩm bán ra (`total_qty`), Tổng doanh thu (`total_revenue`).
- **Bắt buộc:** Phải hiển thị cả các danh mục hiện chưa bán được sản phẩm nào (với số lượng = 0 và doanh thu = 0).
- Sắp xếp giảm dần theo tổng doanh thu.

### Câu 2: Tìm Danh Sách Khách Hàng "Chưa Từng Đặt Đơn Hàng Nào"
Viết 2 cách khác nhau để tìm ra danh sách các khách hàng chưa từng mua bất kỳ đơn hàng nào trong hệ thống:
- Cách 1: Sử dụng `LEFT JOIN ... WHERE ... IS NULL`.
- Cách 2: Sử dụng `NOT EXISTS`.

### Câu 3: Tìm Khách Hàng Chi Tiêu Nhiều Tiền Nhất (Xử lý Đồng Hạng)
Viết câu truy vấn tìm các khách hàng có tổng số tiền mua hàng nhiều nhất trong hệ thống.
- **Ràng buộc:** Nếu có 2 hoặc nhiều khách hàng có cùng số tiền chi tiêu cao nhất, câu truy vấn phải trả về TẤT CẢ bọn họ (Không được dùng `LIMIT 1`).

---
*(Xem lời giải chi tiết tại [mini_tests/solutions/test_06_solution.md](file:///d:/MNM/OnTap/opensource_exam_training/mini_tests/solutions/test_06_solution.md))*
