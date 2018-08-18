<?php

require_once 'google-api-php-client-2.2.2/vendor/autoload.php';
session_start();

$client = new Google_Client();
$client->setAuthConfigFile('client_id.json');
$client->setRedirectUri('https://'.$_SERVER['HTTP_HOST'].'/photo/googleDrive/oauth2callback.php');
$client->addScope(Google_Service_Drive::DRIVE);

if (!isset($_GET['code'])) {
    $auth_url = $client->createAuthUrl();
    header('Location: '.filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
    $client->authenticate($_GET['code']);
    $access_token = $client->getAccessToken();
    $_SESSION['driveAccessToken'] = json_encode($access_token);
    $redirect_uri = 'https://'.$_SERVER['HTTP_HOST'].'/photo/googleDrive/index.php';
    header('Location: '.filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
