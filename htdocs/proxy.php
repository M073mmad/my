<?php
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo "Missing file id";
    exit;
}

$fileId = $_GET['id'];
$apiKey = "AIzaSyAYm_eWzEQvCjbgDJ0N4uslSC9zhzvq9DA";

$url = "https://www.googleapis.com/drive/v3/files/$fileId?alt=media&key=$apiKey";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, false);
$response = curl_exec($ch);

if ($response === false) {
    http_response_code(500);
    echo "Failed to fetch file.";
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

if ($httpCode !== 200) {
    http_response_code($httpCode);
    echo "Error fetching file.";
    exit;
}

// تأكد من نوع الفيديو
if (!str_starts_with($contentType, 'video/')) {
    http_response_code(415);
    echo "Unsupported media type";
    exit;
}

header("Content-Type: $contentType");
header("Cache-Control: public, max-age=86400");
echo $response;
