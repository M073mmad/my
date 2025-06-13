<?php
require_once(__DIR__ . '/vendor/autoload.php');
session_start();

$client = new Google_Client();
$client->setClientId('733626945827-b6ae5591pdi6itku1u0mhm6a926hsni0.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://fro5.onrender.com/oauth2callback.php');
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

$folderId = "1-0fHnE5RvIb3QgRphne0yYuWHMmTAqgy";
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
  <title>📷 معرض الصور</title>
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

  <h2>📷 معرض الصور</h2>

  <div class="top-center-container">
    <a href="videos.php" class="btn">عرض الفيديوهات</a>
  </div>

    <a href="dashboard.php" class="back-btn">←العودة</a>

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
