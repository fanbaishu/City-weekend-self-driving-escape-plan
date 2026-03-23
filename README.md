# 🚗 Drive Escape CN - 周末自驾逃离计划 (国内增强版)

Pick a city, see how far you can drive in 5 hours. 

一键查询城市周边 1-5 小时自驾逃离圈，告别人挤人，探索真正的小众自驾目的地。

**Live Demo (体验地址)** → [https://tljh.fbswlkj.com](https://tljh.fbswlkj.com)

---

## 📬 Contact & Follow Me (联系作者)

If you like this project or want to discuss further, feel free to connect with me!

如果你喜欢这个项目，或者想探讨更多独立开发与流量增长玩法，欢迎来找我玩：

* 🆇 **Twitter (X):** [@klkwruirui](https://x.com/klkwruirui)
* 
* 📸 **Instagram:** [@fanba_ishu](https://instagram.com/fanba_ishu)
* 
* 🎵 **抖音 (Douyin):** 搜 (抖音号: fbsgfzh)
* 
* 💬 **微信 (WeChat):**  (fbs0709)

---

## 🌟 What's New in this Version? (本版本的核心升级)

While the original project provides a fantastic global isochrone mapping solution, this fork focuses heavily on **localizing the experience for the Chinese market**, enhancing the UI/UX, and adding operational features for independent developers.

原版提供了极佳的全球化自驾探索方案。本分支则致力于**国内本土化体验**的极致打磨，大幅强化了视觉交互与独立运营能力。

**🚀 核心增强点 (Key Enhancements):**

* **⚡ 极速本地化引擎 (Localized Map Engine):** 将底图与搜索核心切换为**高德地图 (AMAP)**，彻底解决国内访问延迟，实现秒级定位与地名精准搜索。
* **✨ 高颜值 UI 重构 (Glassmorphism UI):** 全新设计了毛玻璃风格的操作面板，并加入了丝滑的暗色/亮色模式 (Dark/Light Mode) 切换。
* **⛅ 动态天气系统 (Real-time Weather):** 新增高德实时天气接口，点击地图任意区县，即可弹出带动态 Emoji 的精准天气与气温信息。
* **📱 移动端与社交裂变适配 (Mobile & Social Ready):** 重新设计了移动端底部悬浮操作台，并深度打通微信小程序分享逻辑，便于社交传播。
* **📊 极简运营后台 (Zero-DB Admin):** 为独立开发者内置了纯 PHP + JSON 的无数据库轻量级管理系统（包含访客统计、意见反馈收集、全网公告推送）。

---

## 🛠️ Tech Stack (技术栈)

* **Map & Routing:** Leaflet + AMAP + OSRM Table API
* **Boundaries:** DataV GeoJSON (Accelerated via local PHP Proxy)
* **Backend:** Native PHP 7.0+ (No MySQL required)

## 💻 Deploy (如何部署)

1. 下载或 Clone 本仓库。
2. 上传至任何支持 PHP 的 Web 服务器（如宝塔面板）。
3. 确保 `/data` 和 `/api/data` 文件夹拥有写入权限（`755` 或 `777`）。
4. 打开 index.html：搜索 const amapKey = "在此处填入你的高德Web服务Key";
5. 同样打开 weather.js：搜索这个const amapKey = 改成你自己的的"在此处填入你的高德Web服务Key";
6. 打开你的 后台管理文件（比如 admin.php）：把你真实的后台登录密码，改成如 admin123 这种密码。
7. 如果你喜欢本项目麻烦点个小⭐哦

---

## 🏆 Acknowledgements (特别致谢)

This project is deeply inspired by and built upon the brilliant open-source repository [drive-escape](https://github.com/qiaoshouqing/drive-escape) created 

本项目最核心创意思维，完全来自于原作者的无私开源。在此向原作者致以最深的敬意与感谢！

## 📄 License

MIT License
