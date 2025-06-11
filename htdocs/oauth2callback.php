require 'vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setClientId('632728965271-reiv5re5tbuu4k3l9npt33n9ih4hbtho.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://fro5.ct.ws/oauth2callback.php');
$client->addScope(Google_Service_Drive::DRIVE_READONLY);

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $token;
    header('Location: gallery.php'); // عد إلى صفحتك
    exit;
}
