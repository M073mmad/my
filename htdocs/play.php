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
      height: 100vh;
      overflow: hidden;
    }

    .video-container {
      position: relative;
      width: 100vw;
      height: 100vh;
      background: black;
    }

    .video-wrapper {
      width: 100%;
      height: 100%;
      position: relative;
      overflow: hidden;
    }

    video {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(0deg);
      transform-origin: center center;
      object-fit: contain;
      transition: transform 0.4s ease, width 0.4s ease, height 0.4s ease;
    }

    .rotate-btn {
      position: absolute;
      top: 10px;
      left: 10px;
      background: rgba(255,255,255,0.8);
      color: black;
      padding: 8px 12px;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      z-index: 10;
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
  </style>
</head>
<body>

  <div class="video-container">
    <div class="video-wrapper">
      <video id="player" controls crossorigin playsinline preload="metadata" loop>
        <source src="proxyv.php?id=<?= urlencode($videoId) ?>" type="video/mp4" />
        متصفحك لا يدعم تشغيل الفيديو.
      </video>
    </div>

    <button class="rotate-btn" onclick="rotateVideo()">↻ تدوير</button>
    <h1>🎬 تشغيل الفيديو</h1>
    <a href="videos.php" class="back-btn">🔙 العودة للمعرض</a>
  </div>

  <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
  <script>
    const player = new Plyr('#player');
    const video = document.getElementById('player');
    let angle = 0;

    function updateVideoSize() {
      if (angle % 180 === 90) {
        // الفيديو عمودي، خله يعبي عرض الشاشة كـ ارتفاع
        video.style.width = window.innerHeight + 'px';
        video.style.height = window.innerWidth + 'px';
      } else {
        // الفيديو أفقي، خله يغطي الشاشة كاملة
        video.style.width = '100vw';
        video.style.height = '100vh';
      }
    }

    function rotateVideo() {
      angle = (angle + 90) % 360;
      video.style.transform = `translate(-50%, -50%) rotate(${angle}deg)`;
      updateVideoSize();
    }

    window.addEventListener('resize', updateVideoSize);
    window.addEventListener('load', updateVideoSize);
  </script>

</body>
</html>
