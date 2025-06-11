<?php
require_once __DIR__ . '/vendor/autoload.php';
session_start();

if (!isset($_GET['id']) || !isset($_SESSION['access_token'])) {
    http_response_code(400);
    exit("Missing file id or access token.");
}

$client = new Google_Client();
$client->setAccessToken($_SESSION['access_token']);

if ($client->isAccessTokenExpired()) {
    http_response_code(401);
    exit("Access token expired.");
}

$service = new Google_Service_Drive($client);
$fileId = $_GET['id'];

try {
    // احضار بيانات الملف
    $file = $service->files->get($fileId, ['fields' => 'mimeType']);
    $mimeType = $file->getMimeType();

    if (strpos($mimeType, 'video/') !== 0) {
        http_response_code(415);
        exit("Not a video.");
    }

    // إعداد رؤوس الـ Range لدعم التخزين المؤقت
    $headers = [];
    if (isset($_SERVER['HTTP_RANGE'])) {
        $headers[] = 'Range: ' . $_SERVER['HTTP_RANGE'];
    }

    $http = $client->authorize();
    $response = $http->request(
        'GET',
        "https://www.googleapis.com/drive/v3/files/$fileId?alt=media",
        ['headers' => $headers, 'stream' => true]
    );

    header("Content-Type: $mimeType");
    header("Accept-Ranges: bytes");

    // تمرير باقي الرؤوس من Google
    foreach ($response->getHeaders() as $name => $values) {
        if (in_array(strtolower($name), ['content-length', 'content-range'])) {
            header("$name: " . implode(", ", $values));
        }
    }

    while (!$response->getBody()->eof()) {
        echo $response->getBody()->read(8192);
        flush();
    }

} catch (Exception $e) {
    http_response_code(500);
    exit("Error: " . $e->getMessage());
}
