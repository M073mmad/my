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
    $file = $service->files->get($fileId, ['fields' => 'mimeType, name']);
    $mimeType = $file->getMimeType();
    
    if (strpos($mimeType, 'image/') !== 0) {
        http_response_code(415);
        echo "Unsupported media type";
        exit;
    }

    $response = $service->files->get($fileId, ['alt' => 'media']);
    header("Content-Type: $mimeType");
    header("Cache-Control: public, max-age=86400");

    echo $response->getBody()->getContents();

} catch (Exception $e) {
    http_response_code(500);
    echo "Error fetching file: " . $e->getMessage();
    exit;
}
