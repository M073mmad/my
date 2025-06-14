<?php
require_once __DIR__ . '/vendor/autoload.php';
session_start();

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo "Missing file id";
    exit;
}

if (!isset($_SESSION['access_token'])) {
    http_response_code(401);
    echo "Unauthorized, no access token";
    exit;
}

$fileId = $_GET['id'];

$client = new Google_Client();
$client->setAccessToken($_SESSION['access_token']);

if ($client->isAccessTokenExpired()) {
    http_response_code(401);
    echo "Access token expired";
    exit;
}

$service = new Google_Service_Drive($client);

try {
    // احصل على رابط thumbnail فقط
    $file = $service->files->get($fileId, ['fields' => 'thumbnailLink, mimeType']);

    if (strpos($file->getMimeType(), 'image/') !== 0) {
        http_response_code(415);
        echo "Unsupported media type";
        exit;
    }

    $thumbUrl = $file->getThumbnailLink();

    // إعادة توجيه مباشرة إلى صورة thumbnail
    header("Location: $thumbUrl");
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo "Error fetching thumbnail: " . $e->getMessage();
    exit;
}
