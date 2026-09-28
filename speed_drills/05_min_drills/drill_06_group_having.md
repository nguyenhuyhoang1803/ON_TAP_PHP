# SPEED DRILL 5M-06: SQL GROUP BY + HAVING LỌC TỔNG HỢP
> **Thời gian tối đa:** 5 phút | **Mục tiêu:** Phân biệt chính xác giữa WHERE và HAVING khi gom nhóm dữ liệu.

---

## 1. ĐỀ BÀI
Cho 2 bảng:
- `suppliers(id, company_name)`
- `products(id, supplier_id, product_name, price, stock_qty)`
Viết câu truy vấn:
- Tính tổng số lượng hàng tồn kho (`SUM(stock_qty)`) và giá sản phẩm đắt nhất (`MAX(price)`) theo từng nhà cung cấp.
- Chỉ hiển thị các nhà cung cấp có tổng hàng tồn kho $\ge 100$ sản phẩm VÀ có ít nhất 1 sản phẩm giá $> 500,000$ đ.
- Sắp xếp theo tổng hàng tồn kho giảm dần.

---

## 2. LỜI GIẢI MẪU (SOLUTION)
```sql
SELECT 
    s.id,
    s.company_name,
    SUM(p.stock_qty) AS total_stock,
    MAX(p.price) AS max_price
FROM suppliers s
JOIN products p ON s.id = p.supplier_id
GROUP BY s.id, s.company_name
HAVING total_stock >= 100 AND max_price > 500000
ORDER BY total_stock DESC;
```
