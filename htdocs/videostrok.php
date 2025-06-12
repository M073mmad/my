<?php
require_once(__DIR__ . '/vendor/autoload.php');
session_start();

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
$folderId = "13vdj_UXiatR2voOXTojHlsZapEvwSL-I";

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
  <meta charset="UTF-8" />
  <title>معرض تروك</title>
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
      background: #000;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.5);
      cursor: pointer;
      transition: transform 0.2s;
      width: 300px;
      height: 400px;
      overflow: hidden;
      position: relative;
    }
    .video-box:hover {
      transform: scale(1.02);
    }
    video {
      width: 100%;
      height: 100%;
      object-fit: cover;
      background: black;
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
  </style>
</head>
<body>

  <h2>معرض تروك </h2>
  <div class="top-center-container">
    <a href="videos.php" class="btn">العودة</a>
  </div>

  <div class="gallery">
    <?php foreach ($videos as $video): ?>
      <div class="video-box" 
           onclick="window.location.href='play.php?id=<?= urlencode($video['id']) ?>'"
           title="<?= htmlspecialchars($video['name']) ?>">
        <video preload="none" muted playsinline loading="lazy" data-src="download.php?id=<?= urlencode($video['id']) ?>">
  متصفحك لا يدعم الفيديو.
</video>

      </div>
    <?php endforeach; ?>
  </div>

  <script>
    const hoverTimers = new WeakMap();

    // إيقاف كل الفيديوهات في البداية (ساكنة)
    document.querySelectorAll('.video-box video').forEach(video => {
      video.pause();
      video.currentTime = 0;
    });

    document.querySelectorAll('.video-box').forEach(box => {
      box.addEventListener('mouseenter', () => {
        const video = box.querySelector('video');
        // تأكد الفيديو توقف وحضر الـ currentTime
        video.pause();
        video.currentTime = 0;

        // بعد 3 ثواني شغل الفيديو
        const timer = setTimeout(() => {
          video.play().catch(e => {
            // إذا حصل خطأ (مثل منع التشغيل التلقائي)
            console.log("Playback prevented:", e);
          });
        }, 3000);

        hoverTimers.set(box, timer);
      });

      box.addEventListener('mouseleave', () => {
        const timer = hoverTimers.get(box);
        if (timer) {
          clearTimeout(timer);
          hoverTimers.delete(box);
        }
        const video = box.querySelector('video');
        video.pause();
        video.currentTime = 0;
      });
    });
  </script>
<script>
  // مراقبة ظهور الفيديوهات لتحميل المصدر فقط عند الحاجة
  const lazyVideos = document.querySelectorAll("video[data-src]");
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const video = entry.target;
        if (!video.querySelector("source")) {
          const source = document.createElement("source");
          source.src = video.dataset.src;
          source.type = "video/mp4";
          video.appendChild(source);
          video.load(); // حمّل الفيديو بعد إضافة المصدر
        }
      }
    });
  }, { threshold: 0.25 }); // يبدأ التحميل عندما يظهر 25% من الفيديو في الشاشة

  lazyVideos.forEach(video => observer.observe(video));
</script>

</body>
</html>
