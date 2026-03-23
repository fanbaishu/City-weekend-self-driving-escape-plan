// weather.js 
// 专门负责处理天气请求，保持主文件清爽

async function fetchWeather(adcode) {
  const weatherEl = document.getElementById('info-weather');
  weatherEl.style.display = 'block';
  weatherEl.innerHTML = '<span style="color:#9ca3af; font-size:12px;">🌤️ 获取天气中...</span>';
  
  try {
    const amapKey = "在此处填入你的高德Web服务Key";
    const url = `https://restapi.amap.com/v3/weather/weatherInfo?city=${adcode}&key=${amapKey}`;
    
    const res = await fetch(url);
    const data = await res.json();
    
    if (data.status === "1" && data.lives.length > 0) {
      const w = data.lives[0];
      
      let emoji = "⛅";
      if (w.weather.includes("晴")) emoji = "☀️";
      if (w.weather.includes("阴")) emoji = "☁️";
      if (w.weather.includes("雨")) emoji = "🌧️";
      if (w.weather.includes("雪")) emoji = "❄️";
      
      let color = (w.weather.includes("雨") || w.weather.includes("雪")) ? "#ef4444" : "#10b981";
      
      weatherEl.innerHTML = `<span style="color:${color}">${emoji} ${w.weather} · ${w.temperature}℃</span>`;
    } else {
      weatherEl.style.display = 'none'; 
    }
  } catch(e) {
    weatherEl.style.display = 'none';
  }
}