<?php
require_once 'vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setClientId('632728965271-reiv5re5tbuu4k3l9npt33n9ih4hbtho.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://fro5.onrender.com/oauth2callback.php');
$client->addScope(Google_Service_Drive::DRIVE_READONLY);

if (!isset($_GET['code'])) {
    // الخطوة 1: أرسل المستخدم إلى رابط المصادقة
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit;
}
