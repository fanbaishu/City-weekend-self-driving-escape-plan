# 🚗 Drive Escape Pro (周末自驾逃离计划)

Pick a city, see how far you can drive in 5 hours. Say goodbye to crowded places and explore niche destinations around you! 
一键查询城市周边 1-5 小时自驾逃离圈，告别人挤人，探索真正的小众自驾目的地。

**Live Demo (Pro Version)** → [https://tljh.fbswlkj.com](https://tljh.fbswlkj.com)

---

## 🌟 Pro Version vs. Original (版本核心升级对比)

This "Pro" version has been heavily optimized for the Chinese market, focusing on ultra-fast loading, stunning UI, and marketing growth capabilities.
本版本在原版优秀的算法基础上，针对国内环境、UI 交互以及运营裂变进行了深度重构与升级。

| Feature (功能点) | Original Version (原版) | Drive Escape Pro (升级版) 🚀 |
| :--- | :--- | :--- |
| **UI Design (界面视觉)** | Basic HTML layout | **Glassmorphism (毛玻璃) UI** + Smooth Dark/Light Mode toggle |
| **Map Engine (底图引擎)** | OpenStreetMap (OSM) | **AMAP (高德地图)** (Significantly richer details & faster in CN) |
| **Search (城市搜索)** | Nominatim API (Slow in CN) | **AMAP Geocoding API** (Lightning fast + Auto IP Positioning) |
| **Weather (天气系统)** | ❌ None | **Dynamic Emoji Weather** (Built-in temperature & wind data on click) |
| **Operations (运营支撑)** | ❌ None | **Admin Dashboard** + User Feedback System + Global Announcement |
| **Social (社交裂变)** | Standard Web | **WeChat Mini-Program Ready** (Built-in share & timeline triggers) |
| **Cache & CDN (静态加速)**| Standard | **Anti-Cache Versioning** + Global unpkg CDN for Leaflet |

---

## 🛠️ Tech Stack (全新技术栈)

| Component | Solution |
|-----------|----------|
| **Base Map (底图)** | Leaflet + AMAP (高德地图) |
| **Boundaries (边界数据)**| DataV GeoJSON (via local PHP Proxy to bypass limits) |
| **Driving Time (算路)** | OSRM Table API |
| **Search & Weather** | AMAP REST API (高德 Web 服务接口) |
| **Backend (轻后台)** | Native PHP + JSON (Zero Database Architecture) |

## 🚀 Features (核心功能)

- 🔍 **Instant Search & Auto-Location:** Instantly locate your city via IP or AMAP lightning search.
- 🗺️ **High-Res Heatmap:** District-level detail with a 10-tier color scale (green → red).
- ⛅ **Real-time Weather:** Click on any district to see dynamic weather and temperature.
- ⏱️ **Real driving time:** Powered by OSRM + local cache for instant reloading without freezing.
- 📱 **Mobile & Mini-Program Friendly:** Floating operation bar tailored for mobile devices and WeChat.
- 💬 **Feedback & Notice System:** Built-in lightweight PHP feedback collection and global notice popups.

## 💻 Run Locally & Deploy

**Requirements:** Nginx/Apache + PHP 7.0+ (No MySQL needed).

1. Clone or download the repository.
2. Upload the files to your web server (e.g., Baota Panel / 宝塔面板).
3. Ensure the `/data` and `/api/data` folders have write permissions (`755` or `777`) for the feedback JSON files.
4. Open your browser and visit your domain!

*(Note: Replace the `amapKey` in `index.html` and `weather.js` with your own AMAP Web Service Key for production).*

---

## 🏆 Acknowledgements & Original Author Declaration

This project's core polygon-stitching logic and the brilliant Isochrone routing idea are heavily inspired by and built upon the open-source repository by **[@benshandebiao](https://x.com/benshandebiao)**. 

Huge thanks to the original creator for the open-source spirit and the foundational architecture!
**Original Project:** [drive-escape](https://github.com/qiaoshouqing/drive-escape)

## 📄 License

MIT License
