<?php
if (!isset($_GET['id'])) {
    die('لم يتم تحديد الفيديو.');
}

$fileId = $_GET['id'];
$videoUrl = "proxyv.php?id=" . urlencode($fileId);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8" />
<title>تشغيل الفيديو</title>
<style>
  body {
    background: black;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
  }
  video {
    max-width: 90vw;
    max-height: 90vh;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0,0,0,0.7);
  }
  .back-btn {
    position: fixed;
    top: 20px;
    left: 20px;
    background: #28a745;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    z-index: 1000;
  }
</style>
</head>
<body>

<button class="back-btn" onclick="window.history.back()">العودة للمعرض</button>

<video controls autoplay>
  <source src="<?= htmlspecialchars($videoUrl) ?>" type="video/mp4" />
  متصفحك لا يدعم تشغيل الفيديو.
</video>

</body>
</html>
