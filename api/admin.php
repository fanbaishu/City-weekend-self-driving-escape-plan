<?php
session_start();

// =========================================
// 🔒 后台安全与文件配置
// =========================================
$ADMIN_PASSWORD = 'admin123'; // <--- ⚠️ 记得把 admin123  换成你的后台密码
$LOG_FILE = 'feedback_log.txt';
$STATS_FILE = 'stats.json';
$NOTICE_FILE = 'notice.json'; // 👈 新增：公告数据文件

// 处理登录和退出
$error_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === $ADMIN_PASSWORD) {
        $_SESSION['is_admin'] = true;
        header("Location: admin.php"); exit;
    } else { $error_msg = '密码不对哦，再想想？🤨'; }
}
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy(); header("Location: admin.php"); exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'clear' && isset($_SESSION['is_admin'])) {
    file_put_contents($LOG_FILE, ""); header("Location: admin.php"); exit;
}

$is_logged_in = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// =========================================
// 📢 处理发布新公告的请求
// =========================================
if ($is_logged_in && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_notice') {
    $enable = isset($_POST['enable']) ? true : false;
    $version = "v" . time(); // 自动生成时间戳版本号，确保前端必弹！
    $data = [
        "version" => $version,
        "enable" => $enable,
        "title" => $_POST['title'] ?? '',
        "content" => $_POST['content'] ?? ''
    ];
    file_put_contents($NOTICE_FILE, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    $notice_msg = "✅ 公告发布成功！(版本号: $version) 全网用户已更新！";
}

// 🌟 读取公告当前数据
$current_notice = ['enable' => false, 'title' => '', 'content' => ''];
if (file_exists($NOTICE_FILE)) {
    $current_notice = json_decode(file_get_contents($NOTICE_FILE), true);
}

// 🌟 读取访问统计数据
$stats = ['total_pv' => 0, 'today_pv' => 0, 'today_uv' => []];
if ($is_logged_in && file_exists($STATS_FILE)) {
    $stats_data = json_decode(file_get_contents($STATS_FILE), true);
    if ($stats_data) $stats = $stats_data;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>逃离计划 - 极简控制台</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, -apple-system, sans-serif; background: #eef1ec; color: #333; min-height: 100vh; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        
        .glass-panel {
            background: rgba(255, 255, 255, 0.65); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6); border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            padding: 24px; margin-bottom: 20px;
        }

        .login-box { max-width: 350px; margin: 100px auto; text-align: center; }
        .login-box h2 { margin-bottom: 20px; color: #111827; }
        .input-field { width: 100%; padding: 12px; border: 1px solid rgba(0,0,0,0.1); border-radius: 10px; background: rgba(255,255,255,0.8); font-size: 16px; margin-bottom: 16px; outline: none; text-align: center; }
        .input-field:focus { border-color: #10b981; }
        
        .btn { display: inline-block; padding: 10px 24px; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; border: none; transition: all 0.2s; text-decoration: none; }
        .btn-primary { background: #10b981; color: white; width: 100%; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
        .btn-primary:hover { background: #059669; transform: translateY(-2px); }
        .btn-danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .btn-danger:hover { background: #ef4444; color: white; }
        .btn-outline { background: transparent; border: 1px solid #d1d5db; color: #4b5563; }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #111827; }
        .actions { display: flex; gap: 10px; }

        /* 🌟 数据看板 */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: rgba(255,255,255,0.8); border-radius: 12px; padding: 20px; text-align: center; border: 1px solid rgba(0,0,0,0.05); }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: 0.2s; }
        .stat-title { font-size: 13px; color: #6b7280; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;}
        .stat-value { font-size: 32px; font-weight: 900; color: #10b981; }
        @media (max-width: 640px) { .stats-grid { grid-template-columns: 1fr; } }

        /* 📝 公告表单 */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 8px; color: #4b5563; font-size: 14px;}
        .form-input { width: 100%; padding: 12px; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; font-family: inherit; font-size: 14px; outline: none; background: rgba(255,255,255,0.8); resize: vertical; }
        .form-input:focus { border-color: #10b981; background: #fff; }
        .toggle-label { display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 15px; font-weight: bold; color: #111827; }
        .toggle-label input { width: 18px; height: 18px; accent-color: #10b981; cursor: pointer; }

        /* 💬 反馈列表 */
        .feedback-card { background: rgba(255,255,255,0.8); border-radius: 12px; padding: 16px; margin-bottom: 16px; border: 1px solid rgba(0,0,0,0.05); }
        .meta-info { display: flex; justify-content: space-between; font-size: 12px; color: #6b7280; margin-bottom: 12px; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 8px; }
        .tag { background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 2px 8px; border-radius: 20px; font-weight: bold; }
        .content-box { font-size: 15px; line-height: 1.6; color: #1f2937; white-space: pre-wrap; }
        .empty-state { text-align: center; color: #9ca3af; padding: 40px 0; }
    </style>
</head>
<body>

<?php if (!$is_logged_in): ?>
    <div class="glass-panel login-box">
        <h2>🚗 逃离计划后台</h2>
        <?php if($error_msg): ?><p style="color: #ef4444; font-size: 13px; margin-bottom: 10px;"><?= $error_msg ?></p><?php endif; ?>
        <form method="POST"><input type="password" name="password" class="input-field" placeholder="请输入暗号" required autofocus><button type="submit" class="btn btn-primary">进入控制台</button></form>
    </div>
<?php else: ?>
    <div class="container">
        <div class="header">
            <h1>🚗 逃离计划·指挥中心</h1>
            <div class="actions">
                <a href="?action=clear" class="btn btn-danger" onclick="return confirm('确定要清空所有反馈吗？');">清空留言</a>
                <a href="?action=logout" class="btn btn-outline">退出</a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">今日访客 (UV)</div>
                <div class="stat-value"><?= count($stats['today_uv']) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">今日浏览 (PV)</div>
                <div class="stat-value"><?= $stats['today_pv'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">历史总浏览</div>
                <div class="stat-value" style="color:#6366f1;"><?= $stats['total_pv'] ?></div>
            </div>
        </div>

        <div class="glass-panel" style="border-left: 4px solid #10b981;">
            <h3 style="margin-bottom: 16px; color:#111827; font-size:18px;">📢 全局弹窗公告发布</h3>
            <?php if(isset($notice_msg)) echo "<p style='color:#059669; background:#d1fae5; padding:10px; border-radius:8px; font-weight:bold; margin-bottom:15px; font-size:14px;'>$notice_msg</p>"; ?>
            
            <form method="POST">
                <input type="hidden" name="action" value="save_notice">
                <div class="form-group" style="background: rgba(255,255,255,0.5); padding: 12px; border-radius: 8px;">
                    <label class="toggle-label">
                        <input type="checkbox" name="enable" value="1" <?= empty($current_notice['enable']) ? '' : 'checked' ?>>
                        开启全局强推弹窗功能
                    </label>
                </div>
                <div class="form-group">
                    <label>弹窗大标题 (支持 Emoji)</label>
                    <input type="text" name="title" class="form-input" value="<?= htmlspecialchars($current_notice['title'] ?? '') ?>" placeholder="例如：🎉 逃离计划重磅更新">
                </div>
                <div class="form-group">
                    <label>公告正文内容</label>
                    <textarea name="content" class="form-input" rows="4" placeholder="写下要告诉用户的话..."><?= htmlspecialchars($current_notice['content'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">💾 保存并立即全网推送</button>
            </form>
        </div>

        <div class="glass-panel">
            <h3 style="margin-bottom: 16px; color:#111827; font-size:18px;">📥 最新反馈</h3>
            <?php
            if (file_exists($LOG_FILE)) {
                $logData = file_get_contents($LOG_FILE);
                $entries = array_filter(explode("------------------------\n", $logData));
                
                if (empty($entries)) {
                    echo '<div class="empty-state">目前还没有收到任何反馈哦~ 🍃</div>';
                } else {
                    $entries = array_reverse($entries);
                    foreach ($entries as $entry) {
                        $entry = trim($entry); if(empty($entry)) continue;
                        preg_match('/\[(.*?)\] \[IP: (.*?)\] \[(.*?)\]/', $entry, $headerMatch);
                        $time = $headerMatch[1] ?? '未知时间'; $city = $headerMatch[3] ?? '未知城市';
                        $parts = explode("内容: ", $entry); $headerPart = $parts[0]; $contentPart = $parts[1] ?? '无内容';
                        preg_match('/联系人: (.*)/', $headerPart, $contactMatch); $contact = $contactMatch[1] ?? '未留联系方式';

                        echo '<div class="feedback-card"><div class="meta-info"><span><span class="tag">' . htmlspecialchars($city) . '</span> &nbsp; 🕒 ' . htmlspecialchars($time) . '</span><span>📱 ' . htmlspecialchars($contact) . '</span></div><div class="content-box">' . htmlspecialchars(trim($contentPart)) . '</div></div>';
                    }
                }
            } else {
                echo '<div class="empty-state">还没有留言文件哦，快去前台发一条测试一下吧~ 🍃</div>';
            }
            ?>
        </div>
    </div>
<?php endif; ?>

</body>
</html>