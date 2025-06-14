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
  <title>📽️ مشغل الفيديو</title>
  <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
  <style>
    body {
      background: black;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      color: white;
      overflow: hidden;
    }

    .video-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: black;
    }

    .video-wrapper {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: black;
      overflow: hidden;
      width: 100vw;
      height: 100vh;
    }

    .video-wrapper video {
      position: absolute;
      top: 50%;
      left: 50%;
      transform-origin: center center;
      transform: translate(-50%, -50%) rotate(0deg);
      width: 100vw;
      height: 100vh;
      transition: transform 0.4s ease, object-fit 0.2s ease;
      object-fit: contain; /* افتراضي */
    }

    .rotate-btn,
    .back-btn,
    h1 {
      z-index: 1000;
      position: fixed;
      background: rgba(0, 0, 0, 0.5);
      color: white;
      text-decoration: none;
      padding: 8px 16px;
      border-radius: 8px;
      font-weight: bold;
      user-select: none;
      cursor: pointer;
    }

    .rotate-btn {
      top: 20px;
      right: 20px;
    }

    .back-btn {
      top: 20px;
      left: 20px;
    }

    .back-btn:hover {
      background: rgba(255, 0, 0, 0.6);
    }

    h1 {
      top: 10px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 18px;
      pointer-events: none;
    }
  </style>
</head>
<body>
  <div class="video-container">
    <div class="video-wrapper" id="wrapper">
      <video id="player" controls playsinline preload="metadata" loop>
        <source src="proxyv.php?id=<?= urlencode($videoId) ?>" type="video/mp4" />
        متصفحك لا يدعم تشغيل الفيديو.
      </video>
    </div>

    <button class="rotate-btn" onclick="rotateVideo()">↻ تدوير</button>
    <h1>🎬 مشغل الفيديو</h1>
  </div>

  <a onclick="window.history.back()" class="back-btn">← العودة</a>

  <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
  <script>
    const player = new Plyr('#player');
    const video = document.getElementById('player');
    let angle = 0;

    function updateVideoStyle() {
      // لو الزاوية 90 أو 270 خلي object-fit: cover و غير العرض والارتفاع لعكس الأبعاد
      if (angle % 180 === 90) {
        video.style.objectFit = 'cover';
        video.style.width = window.innerHeight + 'px';
        video.style.height = window.innerWidth + 'px';
      } else {
        // 0 أو 180 درجة
        video.style.objectFit = 'contain';
        video.style.width = window.innerWidth + 'px';
        video.style.height = window.innerHeight + 'px';
      }
    }

    function rotateVideo() {
      angle = (angle + 90) % 360;
      video.style.transform = `translate(-50%, -50%) rotate(${angle}deg)`;
      updateVideoStyle();
    }

    window.addEventListener('resize', updateVideoStyle);
    window.addEventListener('load', updateVideoStyle);
    document.addEventListener('fullscreenchange', updateVideoStyle);
  </script>
</body>
</html>
