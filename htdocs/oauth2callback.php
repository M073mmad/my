require 'vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setClientId('733626945827-b6ae5591pdi6itku1u0mhm6a926hsni0.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://fro5.onrender.com/oauth2callback.php');
$client->addScope(Google_Service_Drive::DRIVE_READONLY);

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $token;
    header('Location: gallery.php'); // عد إلى صفحتك
    exit;
}
