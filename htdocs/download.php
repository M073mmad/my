<?php
require_once(__DIR__ . '/vendor/autoload.php');
session_start();

if (!isset($_SESSION['access_token'])) {
    http_response_code(401);
    die('Unauthorized');
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    die('Missing file ID');
}

$client = new Google_Client();
$client->setAccessToken($_SESSION['access_token']);

if ($client->isAccessTokenExpired()) {
    unset($_SESSION['access_token']);
    header('Location: auth.php');
    exit;
}

$service = new Google_Service_Drive($client);
$fileId = $_GET['id'];

try {
    $response = $service->files->get($fileId, ['alt' => 'media']);
    $http = $client->authorize();
    $stream = $http->request('GET', "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media");

    header("Content-Type: video/mp4");
    header("Content-Disposition: inline");
    echo $stream->getBody();
} catch (Exception $e) {
    http_response_code(500);
    echo "خطأ أثناء تحميل الفيديو: " . $e->getMessage();
}
