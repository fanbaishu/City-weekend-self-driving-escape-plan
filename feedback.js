// ==========================================
// 💬 周末自驾逃离计划 - 独立反馈插件 (强制留联版)
// ==========================================

// 核心魔法：耐心等待网页所有元素都准备好，再开始注入按钮
window.addEventListener('DOMContentLoaded', function() {
  
  // 1. 注入 CSS 样式
  const style = document.createElement('style');
  style.innerHTML = `
    .modal-overlay {
      position: fixed; top: 0; left: 0; width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
      z-index: 3000; display: none; align-items: center; justify-content: center;
      opacity: 0; transition: opacity 0.3s;
    }
    .modal-overlay.show { display: flex; opacity: 1; }
    
    .feedback-box { width: 90%; max-width: 400px; padding: 24px; display: flex; flex-direction: column; gap: 16px; }
    .feedback-box h3 { font-size: 18px; color: #111827; margin-bottom: 4px; }
    .feedback-input { width: 100%; padding: 12px; border: 1px solid rgba(0,0,0,0.1); border-radius: 10px; background: rgba(255,255,255,0.6); font-family: inherit; font-size: 14px; color: #111827; outline: none; resize: none; }
    .feedback-input:focus { border-color: #10b981; background: rgba(255,255,255,0.9); }
    .feedback-input::placeholder { color: #9ca3af; }
    
    .modal-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 8px; }
    .btn { padding: 8px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; transition: all 0.2s; }
    .btn-cancel { background: transparent; color: #6b7280; }
    .btn-cancel:hover { background: rgba(0,0,0,0.05); }
    .btn-submit { background: #10b981; color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
    .btn-submit:hover { background: #059669; transform: translateY(-1px); }

    body.dark-mode .feedback-box h3 { color: #f3f4f6; }
    body.dark-mode .feedback-input { background: rgba(0,0,0,0.2); border-color: rgba(255,255,255,0.1); color: #e5e7eb; }
    body.dark-mode .feedback-input:focus { border-color: #34d399; background: rgba(0,0,0,0.4); }
    body.dark-mode .btn-cancel { color: #9ca3af; }
    body.dark-mode .btn-cancel:hover { background: rgba(255,255,255,0.1); }
  `;
  document.head.appendChild(style);

  // 2. 注入弹窗 HTML 骨架 (💡 提示语已改为必填)
  const modalHTML = `
    <div class="modal-overlay" id="feedback-modal">
      <div class="glass-panel feedback-box">
        <h3>提个建议 🚗</h3>
        <p style="font-size:12px; color:#6b7280; margin-top:-10px; margin-bottom: 5px;">想要新功能？或者发现了 Bug？</p>
        <textarea id="fb-content" class="feedback-input" rows="4" maxlength="200" placeholder="写下你的想法... (最多200字)"></textarea>
        <input type="text" id="fb-contact" class="feedback-input" placeholder="联系方式：微信/邮箱 (必填)" />
        <div class="modal-actions">
          <button class="btn btn-cancel" id="fb-cancel">取消</button>
          <button class="btn btn-submit" id="fb-submit">发送</button>
        </div>
      </div>
    </div>
  `;
  document.body.insertAdjacentHTML('beforeend', modalHTML);

  // 3. 寻找右上角的容器，注入呼出按钮
  const btnHTML = `
    <button class="theme-btn glass-panel" id="feedback-toggle" title="提个建议" style="margin-left: -4px;">
      <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
      </svg>
    </button>
  `;
  
  const topRightContainer = document.querySelector('.top-right');
  if (topRightContainer) {
    topRightContainer.insertAdjacentHTML('beforeend', btnHTML);
  } else {
    const fallbackDiv = document.createElement('div');
    fallbackDiv.innerHTML = btnHTML;
    fallbackDiv.style.cssText = "position:fixed; top:16px; right:120px; z-index:9999;";
    document.body.appendChild(fallbackDiv);
  }

  // 4. 绑定点击事件
  const fbModal = document.getElementById('feedback-modal');
  const fbToggle = document.getElementById('feedback-toggle');
  const fbCancel = document.getElementById('fb-cancel');
  const fbSubmit = document.getElementById('fb-submit');

  if (fbToggle) fbToggle.addEventListener('click', () => fbModal.classList.add('show'));
  
  if (fbCancel) fbCancel.addEventListener('click', () => { 
    fbModal.classList.remove('show'); 
    document.getElementById('fb-content').value = ''; 
    document.getElementById('fb-contact').value = ''; // 取消时顺便清空联系方式
  });

  if (fbModal) fbModal.addEventListener('click', (e) => {
    if(e.target === fbModal) fbModal.classList.remove('show');
  });

  // 5. 调用接口发送通知
  if (fbSubmit) {
    fbSubmit.addEventListener('click', async () => {
      const content = document.getElementById('fb-content').value.trim();
      const contactInput = document.getElementById('fb-contact');
      const contact = contactInput.value.trim();
      
      // 💡 拦截规则 1：没写内容
      if (!content) { alert("还没写建议呢~"); return; }
      
      // 💡 拦截规则 2：没写联系方式（强制拦截器）
      if (!contact) {
        alert("老铁，留个联系方式（微信号或邮箱）呗！不然修好了/发礼品怎么通知你？😎");
        contactInput.focus(); // 让光标自动跳回输入框，逼迫他填！
        return; 
      }
      
      fbSubmit.textContent = "发送中...";
      fbSubmit.disabled = true;

      try {
        const response = await fetch('/api/feedback.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ 
            city: typeof currentCityName !== 'undefined' ? currentCityName : '未知', 
            contact: contact, 
            content: content 
          })
        });
        
        if (response.ok) {
          alert("收到啦！感谢你的建议~");
          fbModal.classList.remove('show');
          document.getElementById('fb-content').value = '';
          contactInput.value = ''; // 成功后清空
        } else {
          try {
            const errData = await response.json();
            alert(errData.message || "'你发得太快啦，喝口水歇50分钟吧！ ☕'");
          } catch(e) {
            alert("您已经提交过了，请不要频繁提交哦！");
          }
        }
      } catch(error) {
        alert("服务器连不上啦，稍后再试吧！");
      }
      
      fbSubmit.textContent = "发送";
      fbSubmit.disabled = false;
    });
  }
});