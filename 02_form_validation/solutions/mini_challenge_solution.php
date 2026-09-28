<?php
// 02_form_validation/solutions/mini_challenge_solution.php

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

$roomPrices = [
    'Standard' => 500000,
    'Deluxe'   => 800000,
    'Suite'    => 1500000
];

$errors = [];
$customerName = '';
$numNights    = '';
$roomType     = 'Standard';
$totalCost    = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = trim($_POST['customer_name'] ?? '');
    $numNights    = trim($_POST['num_nights'] ?? '');
    $roomType     = trim($_POST['room_type'] ?? '');

    if ($customerName === '') {
        $errors['customer_name'] = 'Tên khách hàng không được để trống.';
    } elseif (mb_strlen($customerName) < 3) {
        $errors['customer_name'] = 'Tên khách hàng phải có tối thiểu 3 ký tự.';
    }

    if ($numNights === '') {
        $errors['num_nights'] = 'Số đêm ở không được để trống.';
    } elseif (!filter_var($numNights, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
        $errors['num_nights'] = 'Số đêm phải là số nguyên lớn hơn hoặc bằng 1.';
    }

    if (!array_key_exists($roomType, $roomPrices)) {
        $errors['room_type'] = 'Loại phòng không hợp lệ.';
    }

    if (empty($errors)) {
        $nights = (int)$numNights;
        $pricePerNight = $roomPrices[$roomType];
        $totalCost = $nights * $pricePerNight;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Đặt Phòng Khách Sạn</title></head>
<body>
    <h2>Form Đặt Phòng Khách Sạn</h2>

    <?php if ($totalCost !== null): ?>
        <div style="background: #e6ffed; border: 1px solid #34d058; padding: 10px; margin-bottom: 15px;">
            <strong>Đặt phòng thành công!</strong><br>
            Khách hàng: <?= e($customerName) ?><br>
            Loại phòng: <?= e($roomType) ?> (<?= number_format($roomPrices[$roomType], 0, ',', '.') ?> VNĐ/đêm)<br>
            Thời gian ở: <?= (int)$numNights ?> đêm<br>
            <strong>Tổng số tiền cần thanh toán: <?= number_format($totalCost, 0, ',', '.') ?> VNĐ</strong>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div>
            <label>Họ tên khách hàng:</label><br>
            <input type="text" name="customer_name" value="<?= e($customerName) ?>">
            <?php if (isset($errors['customer_name'])): ?>
                <span style="color: red;"><?= e($errors['customer_name']) ?></span>
            <?php endif; ?>
        </div>
        <br>
        <div>
            <label>Số đêm lưu trú:</label><br>
            <input type="number" name="num_nights" value="<?= e($numNights) ?>">
            <?php if (isset($errors['num_nights'])): ?>
                <span style="color: red;"><?= e($errors['num_nights']) ?></span>
            <?php endif; ?>
        </div>
        <br>
        <div>
            <label>Loại phòng:</label><br>
            <select name="room_type">
                <?php foreach ($roomPrices as $type => $price): ?>
                    <option value="<?= e($type) ?>" <?= ($roomType === $type) ? 'selected' : '' ?>>
                        <?= e($type) ?> - <?= number_format($price, 0, ',', '.') ?> VNĐ/đêm
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['room_type'])): ?>
                <span style="color: red;"><?= e($errors['room_type']) ?></span>
            <?php endif; ?>
        </div>
        <br>
        <button type="submit">Xác Nhận Đặt Phòng</button>
    </form>
</body>
</html>
