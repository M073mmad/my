<?php
require_once __DIR__ . '/vendor/autoload.php';
session_start();

if (!isset($_GET['id']) || !isset($_SESSION['access_token'])) {
    http_response_code(403);
    exit("Unauthorized");
}

$client = new Google_Client();
$client->setAccessToken($_SESSION['access_token']);

$service = new Google_Service_Drive($client);

$fileId = $_GET['id'];
$file = $service->files->get($fileId, ['fields' => 'thumbnailLink']);

if ($file && $file->getThumbnailLink()) {
    header('Location: ' . $file->getThumbnailLink());
    exit;
} else {
    http_response_code(404);
    echo "Thumbnail not found.";
}
?>
