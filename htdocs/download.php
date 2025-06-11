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

$fileId = $_GET['id'];
$service = new Google_Service_Drive($client);

try {
    // الحصول على بيانات الملف (الحجم ونوع MIME)
    $file = $service->files->get($fileId, ['fields' => 'size,mimeType,name']);
    $fileSize = (int)$file->getSize();
    $mimeType = $file->getMimeType();

    // التعامل مع رأس Range من المتصفح
    $range = null;
    if (isset($_SERVER['HTTP_RANGE'])) {
        $range = $_SERVER['HTTP_RANGE'];
    }

    $http = $client->authorize();

    if ($range) {
        // استخراج حدود الطلب من رأس Range
        list(, $range) = explode('=', $range, 2);
        $range = explode('-', $range);
        $start = intval($range[0]);
        $end = isset($range[1]) && is_numeric($range[1]) ? intval($range[1]) : $fileSize - 1;

        if ($end >= $fileSize) {
            $end = $fileSize - 1;
        }

        $length = $end - $start + 1;

        // جلب البيانات من Google Drive باستخدام range
        $headers = [
            'Range' => "bytes=$start-$end"
        ];
        $request = $http->request('GET', "https://www.googleapis.com/drive/v3/files/$fileId?alt=media", ['headers' => $headers]);

        header("HTTP/1.1 206 Partial Content");
        header("Content-Type: $mimeType");
        header("Accept-Ranges: bytes");
        header("Content-Length: $length");
        header("Content-Range: bytes $start-$end/$fileSize");
        header("Content-Disposition: inline; filename=\"".$file->getName()."\"");

        echo $request->getBody();
    } else {
        // جلب الملف كاملًا بدون Range
        $request = $http->request('GET', "https://www.googleapis.com/drive/v3/files/$fileId?alt=media");

        header("Content-Type: $mimeType");
        header("Content-Length: $fileSize");
        header("Accept-Ranges: bytes");
        header("Content-Disposition: inline; filename=\"".$file->getName()."\"");

        echo $request->getBody();
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "خطأ أثناء تحميل الفيديو: " . $e->getMessage();
}
