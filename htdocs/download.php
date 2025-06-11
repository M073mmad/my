<?php
require_once 'vendor/autoload.php';
require_once 'auth.php'; // ملف إعداد Google Client مع OAuth

if (!isset($_GET['id'])) {
    die('لم يتم تحديد الملف.');
}

$fileId = $_GET['id'];

$driveService = new Google_Service_Drive($client);

try {
    $response = $driveService->files->get($fileId, ['alt' => 'media']);

    header('Content-Type: video/mp4'); // غيّره حسب نوع الفيديو إذا أردت

    while (!$response->getBody()->eof()) {
        echo $response->getBody()->read(1024 * 1024);
        flush();
    }

} catch (Exception $e) {
    die("فشل في تحميل الفيديو: " . $e->getMessage());
}
