<?php
// عرض الأخطاء لتسهيل تتبع المشاكل أثناء التطوير
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// تحميل مكتبة Google API من المجلد الأعلى (vendor)
require __DIR__ . '/vendor/autoload.php';


session_start();

// إعداد Google Client
$client = new Google_Client();
$client->setClientId('733626945827-b6ae5591pdi6itku1u0mhm6a926hsni0.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://my-h3a2.onrender.com/oauth2callback.php');
$client->addScope(Google_Service_Drive::DRIVE_READONLY);

// التعامل مع رمز التفويض بعد رجوع المستخدم من Google
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    // التحقق من وجود خطأ في التوكن
    if (isset($token['error'])) {
        echo "خطأ في تسجيل الدخول باستخدام Google: " . htmlspecialchars($token['error_description']);
        exit;
    }

    $_SESSION['access_token'] = $token;

    // إعادة التوجيه إلى صفحة المعرض بعد تسجيل الدخول
    header('Location: dashboard.php');
    exit;
}
