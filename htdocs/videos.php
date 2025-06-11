<?php
// إعدادات API
$apiKey = "AIzaSyAYm_eWzEQvCjbgDJ0N4uslSC9zhzvq9DA"; // استبدل بمفتاح API الخاص بك
$folderId = "13qsLYeofdP2xGrKxFKMOHdpJdtNfd7Ba"; // استبدل بمعرف مجلد الفيديوهات

// جلب الفيديوهات من Google Drive
$url = "https://www.googleapis.com/drive/v3/files?q='" . $folderId . "'+in+parents+and+trashed=false&key=" . $apiKey . "&fields=files(id,name,mimeType)";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if ($response === false) {
    die('خطأ في جلب البيانات من Google Drive: ' . curl_error($ch));
}

curl_close($ch);
$data = json_decode($response, true);

// تصفية الفيديوهات فقط
$videos = [];
$allowedExtensions = ['mp4', 'webm', 'ogg'];

if (isset($data["files"])) {
    foreach ($data["files"] as $file) {
        if (strpos($file["mimeType"], "video/") === 0) {
            $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
            if (in_array($ext, $allowedExtensions)) {
                $videos[] = [
                    'id' => $file["id"],
                    'name' => $file["name"]
                ];
            }
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
