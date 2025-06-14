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
    body {
      background: black;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 20px;
      color: white;
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
      transform-origin: center center;
      transition: transform 0.4s ease;
    }

    .rotate-btn {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1000;
      background: rgba(0,0,0,0.5);
      color: white;
      text-decoration: none;
      padding: 8px 16px;
      border-radius: 8px;
      font-weight: bold;
    }

    h1 {
      position: fixed;
      top: 10px;
      left: 50%;
      transform: translateX(-50%);
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

  <a onclick="window.history.back()" class="back-btn">← العودة</a>

  <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
  <script>
    const player = new Plyr('#player');
    const video = document.getElementById('player');
    const wrapper = document.getElementById('wrapper');
    let angle = 0;

    function updateSize() {
      if (angle % 180 === 90) {
        video.style.width = window.innerHeight + 'px';
        video.style.height = window.innerWidth + 'px';
      } else {
        video.style.width = '100vw';
        video.style.height = '100vh';
      }
    }

    function rotateVideo() {
      angle = (angle + 90) % 360;

      let transform = `rotate(${angle}deg)`;

      // إضافة تعويض لموضع الفيديو بعد التدوير
      if (angle === 90) {
        transform += ' translate(0, -100%)';
      } else if (angle === 180) {
        transform += ' translate(-100%, -100%)';
      } else if (angle === 270) {
        transform += ' translate(-100%, 0)';
      }

      video.style.transform = transform;

      updateSize();
    }

    window.addEventListener('resize', updateSize);
    window.addEventListener('load', updateSize);
    document.addEventListener('fullscreenchange', updateSize);
  </script>

</body>
</html>
