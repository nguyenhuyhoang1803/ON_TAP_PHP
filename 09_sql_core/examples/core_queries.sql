-- 09_sql_core/examples/core_queries.sql

-- 1. Báo cáo doanh số theo khách hàng (kể cả khách chưa từng mua)
SELECT 
    c.id AS customer_id,
    c.name AS customer_name,
    COUNT(o.id) AS total_orders,
    COALESCE(SUM(o.total_amount), 0) AS total_spent
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id
GROUP BY c.id, c.name
ORDER BY total_spent DESC;

-- 2. Tìm danh sách sản phẩm tồn kho chưa từng bán được chiếc nào
SELECT 
    p.id,
    p.name,
    p.price,
    p.stock_quantity
FROM products p
LEFT JOIN order_items oi ON p.id = oi.product_id
WHERE oi.id IS NULL;

-- 3. Top các danh mục có doanh thu trên 50 triệu
SELECT 
    cat.name AS category_name,
    COUNT(DISTINCT p.id) AS active_products,
    SUM(oi.quantity * oi.unit_price) AS category_revenue
FROM categories cat
INNER JOIN products p ON cat.id = p.category_id
INNER JOIN order_items oi ON p.id = oi.product_id
GROUP BY cat.id, cat.name
HAVING SUM(oi.quantity * oi.unit_price) >= 50000000
ORDER BY category_revenue DESC;
