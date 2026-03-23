// ==========================================
// 📢 周末自驾逃离计划 - 全局公告系统 (只弹一次)
// ==========================================

window.addEventListener('DOMContentLoaded', async function() {
  try {
    // 1. 去服务器拉取最新的公告配置 (加时间戳防止浏览器缓存死不更新)
    const res = await fetch(`/notice.json?t=${new Date().getTime()}`);
    if (!res.ok) return;
    const notice = await res.json();

    // 2. 检查是否需要弹窗 (总开关是否打开？用户是否已经看过了当前版本？)
    const seenVersion = localStorage.getItem('drive_escape_notice_version');
    if (!notice.enable || seenVersion === notice.version) {
      return; // 没开启，或者已经看过了，静默退出，不打扰用户
    }

    // 3. 注入毛玻璃 UI 样式
    const style = document.createElement('style');
    style.innerHTML = `
      .notice-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
        z-index: 4000; display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.4s ease;
      }
      .notice-overlay.show { opacity: 1; }
      
      .notice-box {
        width: 85%; max-width: 360px; padding: 24px; display: flex; flex-direction: column;
        border-radius: 16px; position: relative; transform: translateY(20px); transition: transform 0.4s ease;
      }
      .notice-overlay.show .notice-box { transform: translateY(0); }
      
      .notice-title { font-size: 18px; color: #111827; margin-bottom: 12px; font-weight: 800; text-align: center; }
      .notice-content { font-size: 14px; color: #4b5563; line-height: 1.6; white-space: pre-wrap; margin-bottom: 20px; }
      
      .btn-know { 
        width: 100%; padding: 12px; border-radius: 10px; font-size: 15px; font-weight: bold; 
        background: #10b981; color: white; border: none; cursor: pointer; 
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); transition: all 0.2s;
      }
      .btn-know:hover { background: #059669; }

      body.dark-mode .notice-title { color: #f3f4f6; }
      body.dark-mode .notice-content { color: #d1d5db; }
    `;
    document.head.appendChild(style);

    // 4. 构建弹窗 HTML (支持换行符 \n)
    const formattedContent = notice.content.replace(/\n/g, '<br>');
    const noticeHTML = `
      <div class="notice-overlay" id="global-notice-modal">
        <div class="glass-panel notice-box">
          <div class="notice-title">${notice.title}</div>
          <div class="notice-content">${formattedContent}</div>
          <button class="btn-know" id="btn-know-notice">朕知道了</button>
        </div>
      </div>
    `;
    document.body.insertAdjacentHTML('beforeend', noticeHTML);

    // 5. 动画展示与关闭逻辑
    const modal = document.getElementById('global-notice-modal');
    const btnKnow = document.getElementById('btn-know-notice');

    // 延迟一丢丢展示，动画更顺滑
    setTimeout(() => modal.classList.add('show'), 100);

    // 点击“朕知道了”
    btnKnow.addEventListener('click', () => {
      modal.classList.remove('show');
      // 核心：把这个版本号盖个章，存进本地！下次再打开就不会弹了
      localStorage.setItem('drive_escape_notice_version', notice.version);
      setTimeout(() => modal.remove(), 400); // 动画结束后把节点清掉，不占内存
    });

  } catch (e) {
    console.log("公告加载失败，静默跳过");
  }
});