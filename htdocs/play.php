<?php
session_start();

if (!isset($_GET['id']) || !isset($_SESSION['access_token'])) {
    die("معرف الفيديو غير موجود أو لم يتم تسجيل الدخول.");
}

$videoId = htmlspecialchars($_GET['id']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title>📽️ تشغيل الفيديو</title>
  <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
  <style>
    body {
      background: #000;
      color: #fff;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      margin: 0;
      padding: 20px;
      font-family: 'Segoe UI', sans-serif;
      min-height: 100vh;
    }
    .video-container {
      position: relative;
      width: 90vw;
      max-width: 960px;
      aspect-ratio: 16 / 9; /* للحفاظ على نسبة العرض إلى الارتفاع */
      overflow: hidden;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(255,255,255,0.2);
      background: black;
    }
    video {
      width: 100%;
      height: 100%;
      object-fit: contain; /* يحافظ على نسبة العرض للارتفاع بدون تشويه */
      transition: transform 0.5s ease;
      display: block;
      margin: 0 auto;
      transform-origin: center center; /* مركز التدوير */
    }
    h1 {
      margin-bottom: 20px;
    }
    .back-btn {
      margin-top: 20px;
      text-decoration: none;
      background: red;
      color: white;
      padding: 10px 25px;
      border-radius: 8px;
      font-weight: bold;
      transition: background 0.3s;
    }
    .back-btn:hover {
      background: darkred;
    }
    .rotate-btn {
      position: absolute;
      top: 10px;
      left: 10px;
      background: rgba(255,255,255,0.8);
      color: #000;
      border: none;
      padding: 8px 12px;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      z-index: 10;
      user-select: none;
    }
  </style>
</head>
<body>

  <h1>🎬 تشغيل الفيديو</h1>

  <div class="video-container">
    <button class="rotate-btn" onclick="rotateVideo()">↻ تدوير</button>
    <video id="player" controls crossorigin playsinline preload="metadata" loop>
      <source src="proxyv.php?id=<?= urlencode($videoId) ?>" type="video/mp4" />
      متصفحك لا يدعم تشغيل الفيديو.
    </video>
  </div>

  <a href="videos.php" class="back-btn">🔙 العودة للمعرض</a>

  <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
  <script>
    const player = new Plyr('#player', {
      controls: [
        'play', 'progress', 'current-time',
        'mute', 'volume', 'settings', 'fullscreen'
      ]
    });

    let angle = 0;
    function rotateVideo() {
      angle = (angle + 90) % 360;
      const videoElement = document.querySelector('#player');
      videoElement.style.transform = `rotate(${angle}deg)`;

      // إذا الزاوية 90 أو 270، نقلب الaspect ratio بالحاوية عشان يناسب الفيديو
      const container = document.querySelector('.video-container');
      if(angle === 90 || angle === 270) {
        container.style.aspectRatio = '9 / 16';
      } else {
        container.style.aspectRatio = '16 / 9';
      }
    }
  </script>

</body>
</html>
