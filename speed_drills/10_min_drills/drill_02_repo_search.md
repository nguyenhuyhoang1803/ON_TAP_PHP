# SPEED DRILL 10M-02: REPOSITORY TÌM KIẾM ĐIỀU KIỆN ĐỘNG AN TOÀN
> **Thời gian tối đa:** 10 phút | **Mục tiêu:** Xây dựng Class Repository nhận PDO, ghép chuỗi SQL động an toàn và trả về kết quả mảng.

---

## 1. ĐỀ BÀI
Tạo file `ProductRepository.php`:
- Class `ProductRepository`:
  - `__construct(PDO $pdo)` nhận đối tượng PDO.
  - Phương thức `search(string $keyword = '', ?float $minPrice = null, ?float $maxPrice = null): array`.
  - Tự động ghép điều kiện `WHERE 1=1`:
    - Nếu có `$keyword`: tìm theo `product_name LIKE :kw`.
    - Nếu có `$minPrice`: `price >= :minPrice`.
    - Nếu có `$maxPrice`: `price <= :maxPrice`.
  - Thực thi bằng Prepared Statement, bind đúng tham số và trả về mảng kết quả `fetchAll()`.

---

## 2. LỜI GIẢI MẪU (SOLUTION)
```php
<?php
class ProductRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function search(string $keyword = '', ?float $minPrice = null, ?float $maxPrice = null): array {
        $sql = "SELECT id, product_name, price, stock_qty FROM products WHERE 1=1";
        $params = [];

        if (trim($keyword) !== '') {
            $sql .= " AND product_name LIKE :kw";
            $params[':kw'] = '%' . trim($keyword) . '%';
        }

        if ($minPrice !== null) {
            $sql .= " AND price >= :minPrice";
            $params[':minPrice'] = $minPrice;
        }

        if ($maxPrice !== null) {
            $sql .= " AND price <= :maxPrice";
            $params[':maxPrice'] = $maxPrice;
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
```
