<?php
session_start();

if (!isset($_GET['id']) || !isset($_SESSION['access_token'])) {
    http_response_code(403);
    exit("Unauthorized");
}

require_once __DIR__ . '/vendor/autoload.php';

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
    $file = $service->files->get($fileId, ['fields' => 'thumbnailLink']);
    header('Location: ' . $file->getThumbnailLink());
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo 'خطأ: ' . htmlspecialchars($e->getMessage());
}
