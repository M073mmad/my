<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

// التحقق من التوكن
if (!isset($_SESSION['access_token'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$client = new Google_Client();
$client->setAccessToken($_SESSION['access_token']);

if ($client->isAccessTokenExpired()) {
    unset($_SESSION['access_token']);
    http_response_code(403);
    exit('Expired Token');
}

$fileId = $_GET['id'] ?? null;
if (!$fileId) {
    http_response_code(400);
    exit('Missing file ID');
}

$drive = new Google_Service_Drive($client);

try {
    // جلب نوع الملف لتحديد الهيدر الصحيح
    $meta = $drive->files->get($fileId, ['fields' => 'mimeType']);
    $mime = $meta->getMimeType();

    // تحميل الصورة كستريم مباشر
    $response = $drive->files->get($fileId, ['alt' => 'media'], ['stream' => true]);

    // إعداد الهيدر المناسب
    header('Content-Type: ' . $mime);
    header('Cache-Control: public, max-age=86400'); // تخزين مؤقت يوم واحد

    // طباعة محتوى الصورة مباشرة
    fpassthru($response->getBody()->detach());

} catch (Exception $e) {
    http_response_code(404);
    echo '⚠️ الصورة غير موجودة أو لا يمكن الوصول إليها';
}
