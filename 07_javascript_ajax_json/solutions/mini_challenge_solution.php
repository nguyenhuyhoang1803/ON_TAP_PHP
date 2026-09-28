<?php
// 07_javascript_ajax_json/solutions/mini_challenge_solution.php

// Mô phỏng cả Endpoint API và Giao diện HTML trong cùng file demo
if (isset($_GET['api'])) {
    header('Content-Type: application/json; charset=utf-8');
    
    // CSDL mẫu giả lập
    $allStudents = [
        ['id' => 1, 'code' => 'SV001', 'name' => 'Nguyễn Văn An', 'gpa' => 3.65],
        ['id' => 2, 'code' => 'SV002', 'name' => 'Lê Thị Bình', 'gpa' => 3.82],
        ['id' => 3, 'code' => 'SV003', 'name' => 'Phạm Quốc Cường', 'gpa' => 2.95],
        ['id' => 4, 'code' => 'SV004', 'name' => 'Đặng Thùy Dương', 'gpa' => 3.40],
        ['id' => 5, 'code' => 'SV005', 'name' => 'Vũ Hoàng Nam', 'gpa' => 3.90],
    ];

    $kw = mb_strtolower(trim($_GET['kw'] ?? ''));

    if ($kw === '') {
        echo json_encode($allStudents);
    } else {
        $filtered = array_values(array_filter($allStudents, function ($s) use ($kw) {
            return str_contains(mb_strtolower($s['name']), $kw) || 
                   str_contains(mb_strtolower($s['code']), $kw);
        }));
        echo json_encode($filtered);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Live Search Sinh Viên</title>
</head>
<body>
    <h2>Live Search Sinh Viên (Mini Challenge Solution)</h2>
    <input type="text" id="searchBox" placeholder="Nhập tên hoặc mã sinh viên..." style="width: 300px; padding: 6px;">
    
    <table border="1" cellpadding="6" style="margin-top: 15px; border-collapse: collapse; width: 500px;">
        <thead>
            <tr bgcolor="#eee"><th>Mã SV</th><th>Họ và Tên</th><th>Điểm GPA</th></tr>
        </thead>
        <tbody id="studentTableBody"></tbody>
    </table>

    <script>
        function debounce(fn, delay = 300) {
            let t;
            return (...args) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...args), delay);
            };
        }

        const input = document.getElementById('searchBox');
        const tbody = document.getElementById('studentTableBody');

        async function fetchStudents(kw = '') {
            try {
                const res = await fetch(`?api=1&kw=${encodeURIComponent(kw)}`);
                if (!res.ok) throw new Error('Lỗi máy chủ');
                const data = await res.json();

                tbody.textContent = '';
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" align="center" style="color: red;">Không tìm thấy sinh viên nào!</td></tr>';
                    return;
                }

                data.forEach(item => {
                    const tr = document.createElement('tr');
                    
                    const tdCode = document.createElement('td');
                    tdCode.textContent = item.code;

                    const tdName = document.createElement('td');
                    tdName.textContent = item.name;

                    const tdGpa = document.createElement('td');
                    tdGpa.textContent = item.gpa;

                    tr.appendChild(tdCode);
                    tr.appendChild(tdName);
                    tr.appendChild(tdGpa);
                    tbody.appendChild(tr);
                });
            } catch (e) {
                console.error(e);
            }
        }

        // Tải dữ liệu ban đầu
        fetchStudents('');

        input.addEventListener('input', debounce((e) => {
            fetchStudents(e.target.value.trim());
        }, 300));
    </script>
</body>
</html>
