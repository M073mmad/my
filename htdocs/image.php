<?php
session_start();

if (!isset($_SESSION['access_token'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$client = new Google_Client();
$client->setAccessToken($_SESSION['access_token']);

$drive = new Google_Service_Drive($client);

// خذ الـ file ID من الرابط
$fileId = $_GET['id'];

try {
    $response = $drive->files->get($fileId, [
        'alt' => 'media'
    ]);

    // إعدادات الهيدر
    header('Content-Type: image/jpeg'); // غيّرها حسب نوع الصورة
    echo $response->getBody();
} catch (Exception $e) {
    http_response_code(404);
    echo 'الصورة غير موجودة أو لا يمكن الوصول إليها';
}
