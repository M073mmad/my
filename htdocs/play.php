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
      width: 100vw;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .video-container {
      width: 100vw;
      height: 100vh;
      background: black;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }

    video {
      width: 100%;
      height: 100%;
      object-fit: contain;
      background: black;
      transition: transform 0.5s ease;
    }

    h1, .back-btn {
      position: absolute;
      z-index: 5;
      background: rgba(0,0,0,0.5);
      padding: 8px 16px;
      border-radius: 8px;
    }

    h1 {
      top: 10px;
      right: 10px;
      font-size: 18px;
    }

    .back-btn {
      bottom: 10px;
      right: 10px;
      color: white;
      text-decoration: none;
      font-weight: bold;
    }

    .back-btn:hover {
      background: rgba(255, 0, 0, 0.6);
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

  <div class="video-container">
    <button class="rotate-btn" onclick="rotateVideo()">↻ تدوير</button>

    <video id="player" controls crossorigin playsinline preload="metadata" loop>
      <source src="proxyv.php?id=<?= urlencode($videoId) ?>" type="video/mp4" />
      متصفحك لا يدعم تشغيل الفيديو.
    </video>

    <h1>🎬 تشغيل الفيديو</h1>
    <a href="videos.php" class="back-btn">🔙 العودة للمعرض</a>
  </div>

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
      const video = document.getElementById('player');
      video.style.transform = `rotate(${angle}deg)`;
    }
  </script>

</body>
</html>
