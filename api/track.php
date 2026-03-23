<?php
// api/track.php - 轻量级访客探针 
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

// 移除了过于严格的防盗链限制，防止微信浏览器误伤
$file = 'stats.json';
$ip = $_SERVER['REMOTE_ADDR'];
$today = date('Y-m-d');

$stats = file_exists($file) ? json_decode(file_get_contents($file), true) : [
    'total_pv' => 0, 'today_pv' => 0, 'today_uv' => [], 'last_date' => $today
];

if ($stats['last_date'] !== $today) {
    $stats['today_pv'] = 0; $stats['today_uv'] = []; $stats['last_date'] = $today;
}

$stats['total_pv']++;
$stats['today_pv']++;
if (!in_array($ip, $stats['today_uv'])) {
    $stats['today_uv'][] = $ip;
}

file_put_contents($file, json_encode($stats));
echo json_encode(['status' => 'tracked']);
?>