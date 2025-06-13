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
  <meta charset="UTF-8">
  <title>📽️ مشغل الفيديو</title>
  <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css">
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
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: black;
      overflow: hidden;
      max-width: 100vw;
      max-height: 100vh;
    }

    .video-wrapper video {
      width: 100vw;
      height: 100vh;
      object-fit: contain;
      transform: rotate(0deg);
      transition: transform 0.4s ease;
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

    h1 {
      position: absolute;
      top: 10px;
      right: 10px;
      z-index: 10;
      background: rgba(0,0,0,0.5);
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 18px;
    }

    .back-btn {
      position: fixed;
      top: 20px;
      left: 20px;
      bottom: 10px;
      right: 10px;
      z-index: 1000;
      background: rgba(0,0,0,0.5);
      color: white;
      text-decoration: none;
      padding: 8px 16px;
      border-radius: 8px;
      font-weight: bold;
    }

    .back-btn:hover {
      background: rgba(255, 0, 0, 0.6);
    }
  </style>
</head>
<body>

  <div class="video-container">
    <div class="video-wrapper" id="wrapper">
      <video id="player" controls playsinline preload="metadata" loop>
        <source src="proxyv.php?id=<?= urlencode($videoId) ?>" type="video/mp4">
        متصفحك لا يدعم تشغيل الفيديو.
      </video>
    </div>

    <button class="rotate-btn" onclick="rotateVideo()">↻ تدوير</button>
    <h1>🎬 مشغل الفيديو</h1>
  </div>

    <button onclick="window.history.back()" class="back-btn">←العودة</a>

  <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
  <script>
    const player = new Plyr('#player');
    const video = document.getElementById('player');
    const wrapper = document.getElementById('wrapper');
    let angle = 0;

    function rotateVideo() {
      angle = (angle + 90) % 360;
      video.style.transform = `rotate(${angle}deg)`;

      if (angle % 180 === 90) {
        // لما يصير الفيديو بوضع أفقي (عرضي)
        video.style.width = window.innerHeight + 'px';
        video.style.height = window.innerWidth + 'px';
      } else {
        // يرجع للوضع الطبيعي
        video.style.width = '100vw';
        video.style.height = '100vh';
      }
    }

    function resetSize() {
      if (angle % 180 === 90) {
        video.style.width = window.innerHeight + 'px';
        video.style.height = window.innerWidth + 'px';
      } else {
        video.style.width = '100vw';
        video.style.height = '100vh';
      }
    }

    window.addEventListener('resize', resetSize);
    window.addEventListener('load', resetSize);
  </script>

</body>
</html>
