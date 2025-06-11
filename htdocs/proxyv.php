<?php
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo "Missing file id";
    exit;
}

$fileId = $_GET['id'];
$downloadUrl = "https://drive.google.com/uc?export=download&id=" . urlencode($fileId);

// الخطوة 1: جلب الرابط الحقيقي بعد التوجيه
$ch = curl_init($downloadUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // لا نتبع التوجيه
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
$response = curl_exec($ch);

preg_match('/Location:\s*(.*)/i', $response, $matches);
curl_close($ch);

if (!isset($matches[1])) {
    http_response_code(500);
    echo "Failed to resolve direct download URL.";
    exit;
}

$directLink = trim($matches[1]);

// الخطوة 2: جلب الفيديو وإزالة Content-Disposition
$ch = curl_init($directLink);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");

// تمرير طلب Range إذا موجود (للسحب Seek)
if (isset($_SERVER['HTTP_RANGE'])) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Range: ' . $_SERVER['HTTP_RANGE']
    ]);
}

// تمرير محتوى الفيديو مباشرة
header("Access-Control-Allow-Origin: *"); // مهم للفيديو
header("Content-Type: video/mp4");
header("Accept-Ranges: bytes");

// توجيه المخرجات مباشرة للمتصفح
$fp = fopen('php://output', 'wb');
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_exec($ch);
curl_close($ch);
fclose($fp);
