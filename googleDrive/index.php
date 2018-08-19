<?php

require_once 'google-api-php-client/vendor/autoload.php';
session_start();

$fbaccess_token = $_SESSION['fbAccessToken'];
$albumData = isset($_REQUEST['albumData']) ? $_REQUEST['albumData'] : '';
$albumData = json_decode($albumData);
if ($albumData != '') {
    $_SESSION['albumData'] = $albumData;
}

if ($albumData != '') {
    if (is_array($albumData)) {
        foreach ($albumData as $a) {
            $sp[] = explode('_', $a);
        }
    } else {
        $sp[] = explode('_', $albumData);
    }
} else {
    $albumData = $_SESSION['albumData'];
    if (is_array($albumData)) {
        foreach ($albumData as $a) {
            $sp[] = explode('_', $a);
        }
    } else {
        $sp[] = explode('_', $albumData);
    }
}

for ($i = 0; $i < count($sp); $i++) {
    $albumReData[$sp[$i][0]] = $sp[$i][1];
}

if (!file_exists('client_id.json')) {
    exit('Client secret file not found');
}
$client = new Google_Client();
$client->setAuthConfig('client_id.json');
$client->addScope(Google_Service_Drive::DRIVE);
$client->setAccessType('offline');
$client->setApprovalPrompt('force');
if ($_SESSION['driveAccessToken'] != '') {
    $access_token = $_SESSION['driveAccessToken'];
    $client->setAccessToken($access_token);
    $token = $client->getRefreshToken();
    if ($client->isAccessTokenExpired()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        $_SESSION['driveAccessToken'] = json_encode($client->getAccessToken());
    }

    $profileLink = "https://graph.facebook.com/v3.1/me?fields=id,name&access_token={$fbaccess_token}";
    $profileJson = file_get_contents($profileLink);
    $profileData = json_decode($profileJson, true, 512, JSON_BIGINT_AS_STRING);
    $userName = $profileData['name'];
    $drive_service = new Google_Service_Drive($client);
    $fileMetadata = new Google_Service_Drive_DriveFile(
            [
                'name'     => $userName,
                'mimeType' => 'application/vnd.google-apps.folder',
            ]
        );
    $file = $drive_service->files->create($fileMetadata, [
            'fields' => 'id', ]);
    $mainFolderId = $file->id;

    foreach ($albumReData as $name => $id) {
        $fileMetadata1 = new Google_Service_Drive_DriveFile(
            [
                'name'     => $name,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents'  => [$mainFolderId],
            ]
        );
        $file1 = $drive_service->files->create($fileMetadata1, [
            'fields' => 'id', ]
        );
        $folderId = $file1->id;

        $json_link = "https://graph.facebook.com/v3.1/{$id}/photos?fields=source,images,name&access_token={$fbaccess_token}";
        $json = file_get_contents($json_link);
        $obj = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);
        $photo_count = count($obj['data']);

        $y = 1; //added for adding image name

        for ($x = 0; $x < $photo_count; $x++) {
            $source = isset($obj['data'][$x]['source']) ? $obj['data'][$x]['source'] : '';
            $content = file_get_contents($source);

            $fileMetadata2 = new Google_Service_Drive_DriveFile([
               'name'    => 'photo'.$y.'.jpg',
               'parents' => [$folderId],
            ]);
            $file2 = $drive_service->files->create($fileMetadata2, [
                'data'       => $content,
                'mimeType'   => 'image/jpeg',
                'uploadType' => 'multipart',
                'fields'     => 'id', ]);
            // $fileId=$file2->id;
            $y++;
        }
    }
    header('Location:https://drive.google.com');
} else {
    error_reporting(E_ALL);
    $redirect_uri = 'https://'.$_SERVER['HTTP_HOST'].'/photo/googleDrive/oauth2callback.php';
    header('Location: '.filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
