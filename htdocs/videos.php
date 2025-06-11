<?php
require_once(__DIR__ . '/vendor/autoload.php');
session_start();

// التأكد من وجود التوكن
if (!isset($_SESSION['access_token'])) {
    header('Location: auth.php');
    exit;
}

$client = new Google_Client();
$client->setAccessToken($_SESSION['access_token']);

if ($client->isAccessTokenExpired()) {
    unset($_SESSION['access_token']);
    header('Location: auth.php');
    exit;
}

$service = new Google_Service_Drive($client);
$folderId = "13qsLYeofdP2xGrKxFKMOHdpJdtNfd7Ba";

$results = $service->files->listFiles([
    'q' => "'$folderId' in parents and trashed = false",
    'fields' => 'files(id,name,mimeType)'
]);

$videos = [];
$allowedExtensions = ['mp4', 'webm', 'ogg'];
foreach ($results->getFiles() as $file) {
    if (strpos($file->getMimeType(), 'video/') === 0) {
        $ext = strtolower(pathinfo($file->getName(), PATHINFO_EXTENSION));
        if (in_array($ext, $allowedExtensions)) {
            $videos[] = [
                'id' => $file->getId(),
                'name' => $file->getName()
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <title>📹معرض الفيديوهات</title>
  <style>
    body {
      background: black;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 20px;
      color: white;
    }

    .gallery {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }

    .video-box {
      border: 1px solid #ccc;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      cursor: pointer;
      transition: transform 0.2s;
      width: 300px;
      height: 400px;
      overflow: hidden;
    }

    .video-box:hover {
      transform: scale(1.02);
    }

    video {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    h2 {
        color: white;
        text-align: center;
    }

    .top-center-container {
      width: 100%;
      display: flex;
      justify-content: center;
      margin-top: 30px;
      margin-bottom: 20px;
    }

    .btn {
      padding: 10px 20px;
      background: #28a745;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
      margin-bottom: 20px;
      text-decoration: none;
      display: inline-block;
    }

    .close-btn {
      position: absolute;
      top: 20px;
      right: 30px;
      font-size: 30px;
      color: white;
      background: none;
      border: none;
      cursor: pointer;
      z-index: 10001;
      user-select: none;
    }
  </style>
</head>
<body>

  <h2>📹 معرض الفيديوهات </h2>
  <div class="top-center-container">
    <a href="gallery.php" class="btn">العودة للمعرض</a>
  </div>

  <div class="gallery">
    <?php foreach ($videos as $video): ?>
      <div class="video-box" onclick="window.location.href='play.php?id=<?= urlencode($video['id']) ?>'" title="<?= htmlspecialchars($video['name']) ?>">
        <video preload="metadata" muted loop>
          <source src="proxyv.php?id=<?= urlencode($video['id']) ?>" type="video/mp4">
          متصفحك لا يدعم الفيديو
        </video>
      </div>
    <?php endforeach; ?>
  </div>

  <script>
    const boxes = document.querySelectorAll('.video-box');

    boxes.forEach(box => {
      const video = box.querySelector('video');
      let timer;

      box.addEventListener('mouseenter', () => {
        timer = setTimeout(() => {
          video.play();
        }, 2000);
      });

      box.addEventListener('mouseleave', () => {
        clearTimeout(timer);
        video.pause();
        video.currentTime = 0;
      });
    });

    function showVideo(src) {
      const overlay = document.getElementById('videoOverlay');
      const video = document.getElementById('overlayVideo');
      video.src = src;
      video.play();
      overlay.style.display = 'flex';
    }

    function closeVideo() {
      const overlay = document.getElementById('videoOverlay');
      const video = document.getElementById('overlayVideo');
      video.pause();
      video.src = '';
      overlay.style.display = 'none';
    }

    document.getElementById('videoOverlay').addEventListener('click', function(e) {
      if (e.target === this) closeVideo();
    });

    document.addEventListener('keydown', function(e) {
      if (e.key === "Escape") closeVideo();
    });
  </script>

</body>
</html>
