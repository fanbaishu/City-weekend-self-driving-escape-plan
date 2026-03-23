<?php
// api/geo.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=86400'); // 缓存一天

$code = isset($_GET['code']) ? $_GET['code'] : '';

// 基础的安全校验，确保传入的是纯数字
if (!$code || !preg_match('/^\d+$/', $code)) {
    http_response_code(400);
    echo json_encode(['error' => 'invalid code']);
    exit;
}

$target = "https://geo.datav.aliyun.com/areas_v3/bound/{$code}_full.json";

// 伪造请求头，绕过阿里云的防盗链
$options = [
    'http' => [
        'method' => "GET",
        'header' => "Referer: https://datav.aliyun.com/\r\n" .
                    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"
    ]
];

$context = stream_context_create($options);
$response = @file_get_contents($target, false, $context);

if ($response === FALSE) {
    http_response_code(502);
    echo json_encode(['error' => 'Failed to fetch DataV API']);
    exit;
}

echo $response;
?>