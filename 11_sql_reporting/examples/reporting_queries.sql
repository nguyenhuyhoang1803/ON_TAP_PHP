-- 11_sql_reporting/examples/reporting_queries.sql
SELECT 
    sup.id AS supplier_id,
    sup.name AS supplier_name,
    COUNT(DISTINCT p.id) AS total_supplied_products,
    COALESCE(SUM(oi.quantity), 0) AS total_units_sold,
    COALESCE(SUM(oi.quantity * oi.unit_price), 0) AS total_gross_revenue
FROM suppliers sup
LEFT JOIN products p ON sup.id = p.supplier_id
LEFT JOIN order_items oi ON p.id = oi.product_id
GROUP BY sup.id, sup.name
ORDER BY total_gross_revenue DESC;
