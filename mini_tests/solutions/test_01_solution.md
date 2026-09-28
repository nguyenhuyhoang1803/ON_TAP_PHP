# LỜI GIẢI MINI TEST 01: SHIPPING CALCULATOR

```php
<?php
function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

$errors = [];
$name      = '';
$weight    = '';
$distance  = '';
$isFragile = false;
$result    = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = trim($_POST['receiver_name'] ?? '');
    $weight    = trim($_POST['weight'] ?? '');
    $distance  = trim($_POST['distance'] ?? '');
    $isFragile = isset($_POST['is_fragile']);

    // 1. Validate receiver_name
    if ($name === '') {
        $errors['receiver_name'] = 'Tên người nhận không được để trống.';
    } elseif (mb_strlen($name) < 3) {
        $errors['receiver_name'] = 'Tên người nhận phải có ít nhất 3 ký tự.';
    }

    // 2. Validate weight
    if ($weight === '') {
        $errors['weight'] = 'Khối lượng không được để trống.';
    } elseif (!is_numeric($weight) || (float)$weight <= 0) {
        $errors['weight'] = 'Khối lượng phải là số thực lớn hơn 0.';
    }

    // 3. Validate distance
    if ($distance === '') {
        $errors['distance'] = 'Khoảng cách không được để trống.';
    } elseif (!filter_var($distance, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
        $errors['distance'] = 'Khoảng cách phải là số nguyên từ 1 km trở lên.';
    }

    // 4. Tính toán khi không có lỗi
    if (empty($errors)) {
        $w = (float)$weight;
        $d = (int)$distance;

        $baseFee = 20000;
        $weightFee = $w * 5000;
        $distanceFee = $d * 2000;
        $fragileFee = $isFragile ? 30000 : 0;

        $subTotal = $baseFee + $weightFee + $distanceFee + $fragileFee;
        $discount = ($subTotal >= 200000) ? ($subTotal * 0.10) : 0;
        $afterDiscount = $subTotal - $discount;
        $vat = $afterDiscount * 0.08;
        $finalTotal = $afterDiscount + $vat;

        $result = [
            'baseFee'       => $baseFee,
            'weightFee'     => $weightFee,
            'distanceFee'   => $distanceFee,
            'fragileFee'    => $fragileFee,
            'subTotal'      => $subTotal,
            'discount'      => $discount,
            'vat'           => $vat,
            'finalTotal'    => $finalTotal,
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Tính Cước Vận Chuyển</title></head>
<body>
    <h2>TÍNH CƯỚC PHÍ VẬN CHUYỂN HÀNG HÓA</h2>

    <?php if ($result !== null): ?>
        <div style="background: #f0fff4; border: 1px solid #38a169; padding: 12px; margin-bottom: 20px; width: 500px;">
            <h3>KẾT QUẢ TÍNH CƯỚC</h3>
            <p>Người nhận: <strong><?= e($name) ?></strong></p>
            <p>Cước cơ bản: <?= number_format($result['baseFee'], 0, ',', '.') ?> VNĐ</p>
            <p>Cước khối lượng: <?= number_format($result['weightFee'], 0, ',', '.') ?> VNĐ</p>
            <p>Cước cự ly: <?= number_format($result['distanceFee'], 0, ',', '.') ?> VNĐ</p>
            <p>Phụ phí hàng dễ vỡ: <?= number_format($result['fragileFee'], 0, ',', '.') ?> VNĐ</p>
            <p>Tổng cước tạm tính: <?= number_format($result['subTotal'], 0, ',', '.') ?> VNĐ</p>
            <?php if ($result['discount'] > 0): ?>
                <p style="color: green;">Giảm giá (10%): -<?= number_format($result['discount'], 0, ',', '.') ?> VNĐ</p>
            <?php endif; ?>
            <p>Thuế VAT (8%): <?= number_format($result['vat'], 0, ',', '.') ?> VNĐ</p>
            <hr>
            <h4>TỔNG TIỀN PHẢI THANH TOÁN: <?= number_format($result['finalTotal'], 0, ',', '.') ?> VNĐ</h4>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div>
            <label>Tên người nhận (*):</label><br>
            <input type="text" name="receiver_name" value="<?= e($name) ?>">
            <?php if (isset($errors['receiver_name'])): ?><div style="color:red"><?= e($errors['receiver_name']) ?></div><?php endif; ?>
        </div><br>

        <div>
            <label>Khối lượng hàng (kg) (*):</label><br>
            <input type="text" name="weight" value="<?= e($weight) ?>">
            <?php if (isset($errors['weight'])): ?><div style="color:red"><?= e($errors['weight']) ?></div><?php endif; ?>
        </div><br>

        <div>
            <label>Khoảng cách vận chuyển (km) (*):</label><br>
            <input type="number" name="distance" value="<?= e($distance) ?>">
            <?php if (isset($errors['distance'])): ?><div style="color:red"><?= e($errors['distance']) ?></div><?php endif; ?>
        </div><br>

        <div>
            <label>
                <input type="checkbox" name="is_fragile" value="1" <?= $isFragile ? 'checked' : '' ?>>
                Hàng dễ vỡ / Cần bảo quản cẩn thận (+30.000 VNĐ)
            </label>
        </div><br>

        <button type="submit">Tính Cước Phí</button>
    </form>
</body>
</html>
```
