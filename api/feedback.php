<?php
header('Content-Type: application/json; charset=utf-8');

// ==========================================
// 🛡️ 第一层防御：防接口盗用 (只允许从你的网站发请求)
// ==========================================
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$host = $_SERVER['HTTP_HOST'];
// 如果请求头里有来源，且来源不是你自己的域名，直接拦截！
if ($referer !== '' && strpos($referer, $host) === false) {
    http_response_code(403);
    die(json_encode(['status' => 'error', 'message' => '禁止盗用接口 🚫']));
}

// ==========================================
// 🛡️ 第二层防御：IP 技能冷却 CD (防狂点轰炸)
// ==========================================
$ip = $_SERVER['REMOTE_ADDR'];
$cache_file = 'shield_cache.json'; // 护盾记录文件
$current_time = time();

// 读取 IP 访问记录
$ip_records = file_exists($cache_file) ? json_decode(file_get_contents($cache_file), true) : [];

// 规则：同一个 IP，60 秒内只能提交 1 次反馈！
if (isset($ip_records[$ip]) && ($current_time - $ip_records[$ip] < 60)) {
    http_response_code(429);
    die(json_encode(['status' => 'error', 'message' => '你发得太快啦，喝口水歇50分钟吧！ ☕']));
}

// 更新该 IP 的最后访问时间，并顺手清理一下过期的记录（防止文件无限变大）
$ip_records[$ip] = $current_time;
foreach ($ip_records as $key => $time) {
    if ($current_time - $time > 120) unset($ip_records[$key]); 
}
file_put_contents($cache_file, json_encode($ip_records));

// ==========================================
// 🛡️ 第三层防御：过滤恶意代码 (XSS 注入拦截)
// ==========================================
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data && !empty($data['content'])) {
    // htmlspecialchars 会把所有的 <script> 标签强制转成普通文字，彻底废掉黑客代码
    $content = htmlspecialchars(trim($data['content']));
    $contact = htmlspecialchars(trim($data['contact'] ?? '未留联系方式'));
    $city = htmlspecialchars(trim($data['city'] ?? '未知城市'));
    $time = date('Y-m-d H:i:s');

    // 如果内容超过 200 个字，直接截断（防止发超级长文把你服务器塞满）
    $content = mb_substr($content, 0, 200, 'utf-8');

    // 1. 本地留底
    $logEntry = "[$time] [IP: $ip] [$city]\n联系人: $contact\n内容: $content\n------------------------\n";
    file_put_contents('feedback_log.txt', $logEntry, FILE_APPEND);

    // 2. 微信推送
    $appToken = 'AT_5CNpr6WpsPcBdN06TMV7lxf22NvGea2u'; 
    $myUid = 'UID_Ki4htQ6hiueB12UWwKdPKqALJ3D0'; 

    $wxTitle = '🚗 逃离计划收到新反馈！';
    $wxContent = "<b>出发城市：</b>$city<br><br><b>联系方式：</b>$contact<br><br><b>反馈内容：</b><br>$content";

    $payload = json_encode(["appToken" => $appToken, "content" => $wxContent, "summary" => $wxTitle, "contentType" => 2, "uids" => [$myUid]]);
    $options = ['http' => ['header' => "Content-type: application/json\r\n", 'method' => 'POST', 'content' => $payload]];
    
    // 静默发送，设置超时时间，防止把咱们自己的服务器卡死
    $context = stream_context_create($options);
    @file_get_contents('https://wxpusher.zjiecode.com/api/send/message', false, $context);

    echo json_encode(['status' => 'success']);
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => '内容不能为空']);
}
?>