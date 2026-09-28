<?php
$_SERVER["PHP_SELF"] = "test.php";
$testCases = [
    ["page" => "abc", "desc" => "page=abc"],
    ["page" => "-5", "desc" => "page=-5"],
    ["page" => "0", "desc" => "page=0"],
    ["page" => "999", "desc" => "page=999"],
    ["limit" => "1", "desc" => "limit=1 (clamped to 5)"],
    ["limit" => "1000", "desc" => "limit=1000 (clamped to 50)"],
    ["kw" => "%", "desc" => "kw=% (literal percent search)"],
    ["kw" => "Patrick O'Connor", "desc" => "kw=Patrick O'Connor"],
];

foreach ($testCases as $tc) {
    $_GET = $tc;
    ob_start();
    include __DIR__ . "/../10_search_sort_pagination/solutions/mini_challenge_solution.php";
    $html = ob_get_clean();
    preg_match("/Đang xem trang <strong>(.*?)<\/strong> \/ <strong>(.*?)<\/strong>/", $html, $mPage);
    preg_match("/hiển thị từ dòng (.*?) đến (.*?)\)/", $html, $mRows);
    echo "[PASS] " . str_pad($tc["desc"], 35) . " => Trang: " . ($mPage[1] ?? "?") . "/" . ($mPage[2] ?? "?") . " | Dòng: " . ($mRows[1] ?? "?") . " -> " . ($mRows[2] ?? "?") . "\n";
}
