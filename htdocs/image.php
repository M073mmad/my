<?php
session_start();

// تحميل مكتبة Google API
require_once __DIR__ . '/vendor/autoload.php';

if (!isset($_SESSION['access_token'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$client = new Google_Client();
$client->setAccessToken($_SESSION['access_token']);

$drive = new Google_Service_Drive($client);

// التحقق من صلاحية التوكن
if ($client->isAccessTokenExpired()) {
    unset($_SESSION['access_token']);
    http_response_code(403);
    exit('Expired Token');
}

// خذ الـ file ID من الرابط
$fileId = $_GET['id'] ?? null;

if (!$fileId) {
    http_response_code(400);
    exit('Missing file ID');
}

try {
    // تحميل محتوى الصورة مباشرة
    $response = $drive->files->get($fileId, ['alt' => 'media']);
    
    // إعدادات الهيدر - غيّر النوع حسب نوع الصورة إذا لزم
    header('Content-Type: image/jpeg');
    echo $response->getBody();
} catch (Exception $e) {
    http_response_code(404);
    echo 'الصورة غير موجودة أو لا يمكن الوصول إليها';
}
