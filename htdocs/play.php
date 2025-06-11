<?php
session_start();

if (!isset($_GET['id']) || !isset($_SESSION['access_token'])) {
    die("Missing video id or not authenticated.");
}

$videoId = htmlspecialchars($_GET['id']);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title>تشغيل الفيديو</title>
  <!-- تحميل CSS الخاص بمشغل Plyr -->
  <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
  <style>
    body {
      background: #000;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .plyr__video-embed {
      width: 80vw;
      max-width: 900px;
    }
  </style>
</head>
<body>

  <video
    id="player"
    controls
    crossorigin
    playsinline
  >
    <source src="proxyv.php?id=<?= urlencode($videoId) ?>" type="video/mp4" />
    متصفحك لا يدعم تشغيل الفيديو.
  </video>

  <!-- تحميل سكريبت Plyr -->
  <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
  <script>
    const player = new Plyr('#player', {
      controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
      autoplay: true,
    });
  </script>

</body>
</html>
