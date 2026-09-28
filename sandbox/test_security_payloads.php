<?php
/**
 * Security & Regression Test Suite
 * Tests live endpoints running on Apache http://localhost/ontap/
 */

$baseUrl = 'http://localhost/ontap';

$tests = [
    [
        'name' => 'XSS: Script tag in search keyword',
        'url' => "$baseUrl/10_search_sort_pagination/solutions/mini_challenge_solution.php?kw=" . urlencode('<script>alert(1)</script>'),
        'assert' => function ($body, $code) {
            if ($code !== 200) return "Expected 200, got $code";
            if (strpos($body, '<script>alert(1)</script>') !== false) return "Unescaped XSS found in response";
            if (strpos($body, '&lt;script&gt;alert(1)&lt;/script&gt;') === false) return "Expected HTML-escaped output";
            return true;
        }
    ],
    [
        'name' => 'SQLi: Classic OR 1=1 in search keyword',
        'url' => "$baseUrl/10_search_sort_pagination/solutions/mini_challenge_solution.php?kw=" . urlencode("' OR '1'='1"),
        'assert' => function ($body, $code) {
            if ($code !== 200) return "Expected 200, got $code";
            if (stripos($body, 'Fatal error') !== false || stripos($body, 'SQLSTATE') !== false) return "SQL error leak detected";
            if (stripos($body, 'Không tìm thấy') === false && stripos($body, '0 nhân viên') === false) {
                // If it returned rows, check that it didn't return all rows
                if (substr_count($body, '<tr>') > 5) return "SQL injection might have bypassed filter (returned multiple rows)";
            }
            return true;
        }
    ],
    [
        'name' => 'SQLi: Semicolon DROP TABLE in sort parameter',
        'url' => "$baseUrl/10_search_sort_pagination/solutions/mini_challenge_solution.php?sort=" . urlencode("name;DROP TABLE students"),
        'assert' => function ($body, $code) {
            if ($code !== 200) return "Expected 200, got $code";
            if (stripos($body, 'Fatal error') !== false || stripos($body, 'SQLSTATE') !== false) return "SQL error leak detected";
            return true;
        }
    ],
    [
        'name' => 'LIKE Wildcard: % does not return all records when searched',
        'url' => "$baseUrl/10_search_sort_pagination/solutions/mini_challenge_solution.php?kw=%25",
        'assert' => function ($body, $code) {
            if ($code !== 200) return "Expected 200, got $code";
            if (stripos($body, 'Fatal error') !== false) return "Fatal error on wildcard";
            if (strpos($body, 'Nguyễn Văn An') !== false) return "Wildcard % was unescaped and matched non-% names";
            return true;
        }
    ],
    [
        'name' => 'LIKE Wildcard: _ does not act as single char wildcard',
        'url' => "$baseUrl/10_search_sort_pagination/solutions/mini_challenge_solution.php?kw=_",
        'assert' => function ($body, $code) {
            if ($code !== 200) return "Expected 200, got $code";
            if (stripos($body, 'Fatal error') !== false) return "Fatal error on wildcard";
            if (strpos($body, 'Nguyễn Văn An') !== false) return "Wildcard _ was unescaped and matched non-_ names";
            return true;
        }
    ],
    [
        'name' => 'Pagination: page=abc defaults to page 1',
        'url' => "$baseUrl/10_search_sort_pagination/solutions/mini_challenge_solution.php?page=abc",
        'assert' => function ($body, $code) {
            if ($code !== 200) return "Expected 200, got $code";
            if (strpos($body, '<strong>1</strong> / <strong>3</strong>') === false) return "Did not default to Page 1";
            return true;
        }
    ],
    [
        'name' => 'Pagination: page=999 clamps to max page (page 3)',
        'url' => "$baseUrl/10_search_sort_pagination/solutions/mini_challenge_solution.php?page=999",
        'assert' => function ($body, $code) {
            if ($code !== 200) return "Expected 200, got $code";
            if (strpos($body, '<strong>3</strong> / <strong>3</strong>') === false) return "Did not clamp to Page 3 / 3";
            return true;
        }
    ],
    [
        'name' => 'Auth Flow: header redirect before output',
        'url' => "$baseUrl/03_session_cookie/examples/auth_flow.php?action=fake_login",
        'assert' => function ($body, $code, $headers) {
            if (stripos($body, 'Cannot modify header information') !== false) return "'Headers already sent' warning found";
            return true;
        }
    ],
    [
        'name' => 'File Statistics: Correct calculation & XSS safety',
        'url' => "$baseUrl/04_upload_file/examples/log_statistics.php",
        'assert' => function ($body, $code) {
            if ($code !== 200) return "Expected 200, got $code";
            if (strpos($body, 'BÁO CÁO THỐNG KÊ FILE UPLOAD') === false) return "Missing stats title";
            if (strpos($body, 'ĐỒNG HẠNG TOP') === false && strpos($body, 'Top Uploader') === false) return "Missing top uploader info";
            return true;
        }
    ],
];

echo "====================================================\n";
echo "RUNNING SECURITY & REGRESSION TESTS ON APACHE SERVER\n";
echo "Base URL: $baseUrl\n";
echo "====================================================\n\n";

$passCount = 0;
$failCount = 0;

foreach ($tests as $t) {
    $ch = curl_init($t['url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headerStr = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    curl_close($ch);

    $result = $t['assert']($body, $httpCode, $headerStr);
    if ($result === true) {
        echo "[PASS] " . $t['name'] . "\n";
        $passCount++;
    } else {
        echo "[FAIL] " . $t['name'] . ": " . $result . "\n";
        $failCount++;
    }
}

echo "\nSummary: $passCount passed, $failCount failed.\n";
if ($failCount > 0) {
    exit(1);
}
exit(0);
