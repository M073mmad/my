<?php
session_start();

$page = $_GET['page'] ?? 'davi';

if ($page === 'davi') {
    if (!isset($_SESSION['access_token'])) {
        header('Location: auth.php');
        exit;
    }

    // هنا تكتب محتوى صفحة arb بالـHTML
    ?>
<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <title>Davi Bertrand</title>
  <style>
    body {
      background: black;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 20px;
      color: white;
    }

    h1 {
      text-align: center;
      margin-bottom: 30px;
      color: white;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 25%;
    }

    .button-tile {
      position: relative;
      border-radius: 12px;
      overflow: hidden;
      text-align: center;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
      transition: transform 0.2s, box-shadow 0.2s;
      cursor: pointer;
      text-decoration: none;
      color: white;
      height: 400px;
      background: white;
    }

    .button-tile:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.5);
    }

    .button-tile img {
       position: relative;
       width: 80%; /* ← نسبة العرض داخل الزر */
       height: auto;
       margin-top: 20px;
       z-index: 1;
       object-fit: contain;
     }

    .button-tile::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 70%; /* ← حسب مدى امتداد التلاشي */
  background: linear-gradient(to top, rgba(0,0,0,0.95), rgba(0,0,0,0.5), transparent);
  z-index: 2;
}


    .button-tile p {
      position: absolute;
      bottom: 10px;
      left: 0;
      width: 100%;
      margin: 0;
      padding: 0 10px;
      font-weight: bold;
      z-index: 3;
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

<h1>Davi Bertrand</h1>

  <a href="agn.php?page=pagesagn" class="back-btn">←العودة</a>

<div class="grid">
  <!-- زر 1 -->
  <a href="?page=videosdavi" class="button-tile">
    <img loading="lazy" src="proxyic.php?id=1RVLAWz-jjde0xD6sSw4mmfVRoGlhWjGg" alt="شعار 1">
    <p>Davi videos</p>
  </a>

  <!-- زر 2 -->
  <a href="?page=imgdavi" class="button-tile">
    <img loading="lazy" src="proxyic.php?id=1u40pQkIJL6f4GHqA4Hcwg7-eYjjxTKl7" alt="شعار 2">
    <p>Davi gallery</p>
  </a>

  <!-- أضف المزيد -->
</div>

</body>
</html>

<?php
} elseif ($page === 'videosdavi') {

<?php
require_once(__DIR__ . '/vendor/autoload.php');

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
$folderId = "1sSxBjm56oQnRXQbzh3k-mQxBQq32IyNv";

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
  <title>Davi videos</title>
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

  <h2>Davi videos</h2>

  <a href="agn.php?page=pagesagn" class="back-btn">←العودة</a>

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

<?php
} elseif ($page === 'imgdavi') {
?>

<?php
require_once(__DIR__ . '/vendor/autoload.php');

$client = new Google_Client();
$client->setClientId('733626945827-b6ae5591pdi6itku1u0mhm6a926hsni0.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://distant-marin-m073mmad-7f9e1fee.koyeb.app/oauth2callback.php');
$client->addScope(Google_Service_Drive::DRIVE_READONLY);

if (!isset($_SESSION['access_token'])) {
    header('Location: auth.php');
    exit;
}

$client->setAccessToken($_SESSION['access_token']);

if ($client->isAccessTokenExpired()) {
    unset($_SESSION['access_token']);
    header('Location: auth.php');
    exit;
}

$service = new Google_Service_Drive($client);

$folderId = "1Vk7l7C5YHw9qFrQwCV4-O4e8jIBvGgoa";
$results = $service->files->listFiles([
    'q' => "'$folderId' in parents and trashed = false",
    'fields' => 'files(id,name,mimeType,thumbnailLink)'
]);

$images = [];
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
foreach ($results->getFiles() as $file) {
    if (strpos($file->getMimeType(), 'image/') === 0) {
        $ext = strtolower(pathinfo($file->getName(), PATHINFO_EXTENSION));
        if (in_array($ext, $allowedExtensions)) {
            $images[] = [
                'thumb' => $file->getThumbnailLink(),
                'id' => $file->getId(),
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8" />
  <title>Davi images</title>
  <style>
    body {
      background: black;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 20px;
      color: white;
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
    .gallery {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      justify-content: center;
    }
    .gallery img {
      width: 300px;           /* عرض موحد */
      height: auto;           /* ارتفاع يتناسب تلقائياً */
      object-fit: cover;      /* يملأ الإطار مع قص زائد */
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
      transition: transform 0.3s;
      cursor: pointer;
      background: #000;
    }

    .gallery img:hover {
      transform: scale(1.05);
    }
    .overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.8);
      backdrop-filter: blur(5px);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }
    .overlay img {
      height: 100vh;             /* تغطي الارتفاع بالكامل */
      width: auto;               /* العرض تلقائي لتناسب الصورة */
      max-width: 100vw;
      object-fit: contain;       /* لا تقص أو تمدد الصورة */
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
     }

     @media (max-width: 768px) {
  .overlay img {
    width: 100vw;
    height: auto;
    max-height: 90vh;
  }
}

      
    .close-btn {
      position: absolute;
      top: 20px; right: 30px;
      font-size: 30px;
      color: white;
      background: none;
      border: none;
      cursor: pointer;
      z-index: 1001;
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

  <h2>Davi images</h2>

  <div class="top-center-container">
    
  </div>

    <a href="agn.php?page=pagesagn" class="back-btn">←العودة</a>

  <div class="gallery">
    <?php
    if (!empty($images)) {
      foreach ($images as $img) {
        // هنا نستخدم الصورة المصغرة في src و الصورة الكاملة عند الضغط (proxy.php)
        echo '<img src="' . htmlspecialchars($img['thumb']) . '" alt="صورة" loading="lazy" onclick="showImage(\'proxy.php?id=' . htmlspecialchars($img['id']) . '\')">';
      }
    } else {
      echo "<p style='color:white'>لا توجد صور متاحة أو أن الصور غير عامة.</p>";
    }
    ?>
  </div>

  <div class="overlay" id="imageOverlay">
    <button class="close-btn" onclick="closeImage()">✖</button>
    <img id="overlayImage" src="" alt="صورة مكبرة" />
  </div>

  <script>
    function showImage(src) {
      document.getElementById('overlayImage').src = src;
      document.getElementById('imageOverlay').style.display = 'flex';
    }
    function closeImage() {
      document.getElementById('imageOverlay').style.display = 'none';
      document.getElementById('overlayImage').src = '';
    }
    document.getElementById('imageOverlay').addEventListener('click', function(e) {
      if (e.target === this) {
        closeImage();
      }
    });
    document.addEventListener('keydown', function(e) {
      if (e.key === "Escape") {
        closeImage();
      }
    });
  </script>

</body>
</html>
<?php } ?>
