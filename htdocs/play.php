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
      overflow: hidden;
      width: 100vw;
      height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .video-container {
      position: relative;
      width: 100vw;
      height: 100vh;
      background: black;
      overflow: hidden;
    }

    video {
      position: absolute;
      top: 50%;
      left: 50%;
      width: auto;
      height: auto;
      max-width: 100vw;
      max-height: 100vh;
      transform: translate(-50%, -50%) rotate(0deg);
      transition: transform 0.5s ease;
      object-fit: contain;
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

    const videoElement = document.getElementById('player');
    let angle = 0;

    function rotateVideo() {
      angle = (angle + 90) % 360;
      videoElement.style.transform = `translate(-50%, -50%) rotate(${angle}deg)`;
    }

    // تضمن أنه حتى عند تغيير اتجاه الجهاز، يبقى الإطار ثابت بالحجم الكامل
    function fixSize() {
      document.querySelector('.video-container').style.width = window.innerWidth + 'px';
      document.querySelector('.video-container').style.height = window.innerHeight + 'px';
    }

    window.addEventListener('resize', fixSize);
    window.addEventListener('orientationchange', fixSize);
    window.addEventListener('load', fixSize);
  </script>

</body>
</html>
