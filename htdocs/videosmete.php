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
$folderId = "1KjivG_LTOldlo6Gp4yFrc-gJX31EaSls";

$results = $service->files->listFiles([
    'q' => "'$folderId' in parents and trashed = false",
    'fields' => 'files(id,name,mimeType,thumbnailLink)'
]);

$videos = [];
$allowedExtensions = ['mp4', 'webm', 'ogg'];
foreach ($results->getFiles() as $file) {
    if (strpos($file->getMimeType(), 'video/') === 0) {
        $ext = strtolower(pathinfo($file->getName(), PATHINFO_EXTENSION));
        if (in_array($ext, $allowedExtensions)) {
            $videos[] = [
                'id' => $file->getId(),
                'name' => $file->getName(),
                'thumb' => $file->getThumbnailLink()
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8" />
  <title>Mete videos</title>
  <style>
    body {
      background: black;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 20px;
      color: white;
    }
    .gallery {
      display: grid;
      grid-template-columns: repeat(4, 1fr); /* 4 أعمدة بنفس الحجم */
      gap: 20px;
      justify-content: center; /* للحفاظ على تمركز الشبكة لو فيها مساحة إضافية */
      max-width: 1280px; /* عرض أقصى تقريبي: 4×(300+20) */
      margin: 0 auto; /* تمركز المعرض في الصفحة */
    }
    .video-box {
      border: 1px solid #ccc;
      background: #000;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.5);
      cursor: pointer;
      transition: transform 0.2s;
      height: 400px;
      overflow: hidden;
      position: relative;
      aspect-ratio: 3 / 4;
      max-width: 300px;
      width: 100%;
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

  <h2>Mete videos</h2>

  <a href="2mete.html" class="back-btn">←العودة</a>

  <div class="gallery">
    <?php foreach ($videos as $video): ?>
      <div class="video-box" 
     onclick="window.location.href='play.php?id=<?= urlencode($video['id']) ?>'"
     title="<?= htmlspecialchars($video['name']) ?>">
  <video preload="none" muted playsinline
         loading="lazy"
         data-src="download.php?id=<?= urlencode($video['id']) ?>"
         poster="<?= htmlspecialchars($video['thumb']) ?>">
    متصفحك لا يدعم الفيديو.
  </video>
</div>
    <?php endforeach; ?>
  </div>

  <script>
  const hoverTimers = new WeakMap();
  const playTimers = new WeakMap();

  document.querySelectorAll('.video-box').forEach(box => {
    const video = box.querySelector('video');

    box.addEventListener('mouseenter', () => {
      const loadTimer = setTimeout(() => {
        // تحميل الفيديو بعد 2 ثانية
        if (!video.querySelector('source')) {
          const source = document.createElement('source');
          source.src = video.dataset.src;
          source.type = "video/mp4";
          video.appendChild(source);
          video.load();
        }

        // تشغيل الفيديو بعد 1 ثانية إضافية (مجموع 3 ثواني)
        const playTimer = setTimeout(() => {
          video.play().catch(err => console.log("Can't autoplay:", err));
        }, 1000); // 3 - 2 = 1 ثانية بعد التحميل

        playTimers.set(box, playTimer);
      }, 2000); // تحميل بعد 2 ثانية

      hoverTimers.set(box, loadTimer);
    });

    box.addEventListener('mouseleave', () => {
      const loadTimer = hoverTimers.get(box);
      if (loadTimer) clearTimeout(loadTimer);
      hoverTimers.delete(box);

      const playTimer = playTimers.get(box);
      if (playTimer) clearTimeout(playTimer);
      playTimers.delete(box);

      video.pause();
      video.currentTime = 0;
    });

    // الضغط على الفيديو ينقلك لصفحة التشغيل الكامل
    box.addEventListener('click', () => {
      const videoId = video.dataset.src.split('=')[1];
      window.location.href = 'play.php?id=' + encodeURIComponent(videoId);
    });
  });
</script>
</body>
</html>
