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
    html, body {
      margin: 0;
      padding: 0;
      background: #000;
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
      overflow: hidden; /* يمنع الscroll */
      height: 100%;
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    .video-container {
      position: relative;
      background: black;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 0 20px rgba(255,255,255,0.2);
    }
    video {
      width: 100%;
      height: 100%;
      display: block;
      object-fit: contain;
      transition: transform 0.5s ease;
      transform-origin: center center;
    }
    h1 {
      margin: 10px 0;
    }
    .back-btn {
      margin-top: 10px;
      text-decoration: none;
      background: red;
      color: white;
      padding: 10px 25px;
      border-radius: 8px;
      font-weight: bold;
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

  <div class="video-container" id="videoContainer">
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

    const container = document.getElementById('videoContainer');
    const videoElement = document.getElementById('player');
    let angle = 0;

    function resizeContainer() {
      const vw = window.innerWidth;
      const vh = window.innerHeight;
      const maxWidth = vw * 0.95;
      const maxHeight = vh * 0.85;

      if (angle === 90 || angle === 270) {
        // فيديو عمودي
        let height = maxHeight;
        let width = height * 9 / 16;
        if (width > maxWidth) {
          width = maxWidth;
          height = width * 16 / 9;
        }
        container.style.width = width + 'px';
        container.style.height = height + 'px';
      } else {
        // فيديو أفقي
        let width = maxWidth;
        let height = width * 9 / 16;
        if (height > maxHeight) {
          height = maxHeight;
          width = height * 16 / 9;
        }
        container.style.width = width + 'px';
        container.style.height = height + 'px';
      }
    }

    function rotateVideo() {
      angle = (angle + 90) % 360;
      videoElement.style.transform = `rotate(${angle}deg)`;
      resizeContainer();
    }

    window.addEventListener('resize', resizeContainer);
    window.addEventListener('load', resizeContainer);
  </script>

</body>
</html>
