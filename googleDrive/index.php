<?php
require_once 'google-api-php-client/vendor/autoload.php';
session_start();

$fbaccess_token=$_SESSION['fbAccessToken'];
$albumData = isset($_REQUEST['albumData']) ? $_REQUEST['albumData'] : "";
$albumData=json_decode($albumData);
if ($albumData!="") {
    $_SESSION['albumData']=$albumData;
}

if ($albumData!="") {
    if (is_array($albumData)) {
        foreach ($albumData as $a) {
            $sp[]=explode("_",$a);
        }
    }else{
        $sp[]=explode("_",$albumData);
    }
}else{
    $albumData=$_SESSION['albumData'];
    if (is_array($albumData)) {
        foreach ($albumData as $a) {
            $sp[]=explode("_",$a);
        }
    }else{
        $sp[]=explode("_",$albumData);
    }
}

for ($i=0; $i < count($sp); $i++) { 
    $albumReData[$sp[$i][0]]=$sp[$i][1];
}

function getData($id,$name,$folderId,$after = NULL,$postfix=NULL)
{
    global $fbaccess_token,$drive_service;
        if($after=="")
    {
        $jsonLink = "https://graph.facebook.com/v3.1/{$id}/photos?fields=source,images,name&access_token={$fbaccess_token}&limit=100";
        $postfix=1; #postfix for image name
    }else{
        $jsonLink = "https://graph.facebook.com/v3.1/{$id}/photos?fields=source,images,name&access_token={$fbaccess_token}&limit=100&after=$after";
    }    
    $json = file_get_contents($jsonLink);
    $obj = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);
    $photoCount = count($obj['data']);
    if(!empty($obj['data']))
    {
        $after=$obj['paging']['cursors']['after'];
        for($x=0; $x<$photoCount; $x++){
            $source = isset($obj['data'][$x]['source']) ? $obj['data'][$x]['source'] : "";
            $content = file_get_contents($source);

            $fileMetadata2 = new Google_Service_Drive_DriveFile(array(
               'name' => 'photo'.$postfix.'.jpg',
               'parents' => array($folderId)
            ));
            $file2 = $drive_service->files->create($fileMetadata2, array(
                'data' => $content,
                'mimeType' => 'image/jpeg',
                'uploadType' => 'multipart',
                'fields' => 'id'
            ));
            $postfix++;
        }
        getData($id,$name,$folderId,$after,$postfix);
    }
    return 0;
}
if (!file_exists("client_id.json")) exit("Client secret file not found");
$client = new Google_Client();
$client->setAuthConfig('client_id.json');
$client->addScope(Google_Service_Drive::DRIVE);
$client->setAccessType('offline');
$client->setApprovalPrompt('force');
if ($_SESSION['driveAccessToken']!="") {
	$access_token = $_SESSION['driveAccessToken'];
	$client->setAccessToken($access_token);
    $token=$client->getRefreshToken();
    if ($client->isAccessTokenExpired()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        $_SESSION['driveAccessToken']=json_encode($client->getAccessToken());
    }

        $profileLink="https://graph.facebook.com/v3.1/me?fields=id,name&access_token={$fbaccess_token}";
        $profileJson = file_get_contents($profileLink);
        $profileData = json_decode($profileJson, true, 512, JSON_BIGINT_AS_STRING);
        $userName="Facebook_Albums[".$profileData['name']."]";
        $drive_service = new Google_Service_Drive($client);
        $fileMetadata = new Google_Service_Drive_DriveFile(
            array(
                'name' => $userName,
                'mimeType' => 'application/vnd.google-apps.folder',
            )
        );
        $file = $drive_service->files->create($fileMetadata, array(
            'fields' => 'id'));
        $mainFolderId=$file->id;

    foreach ($albumReData as $name => $id) {
        $fileMetadata1 = new Google_Service_Drive_DriveFile(
            array(
                'name' => $name,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents' => array($mainFolderId)
            )
        );
        $file1 = $drive_service->files->create($fileMetadata1, array(
            'fields' => 'id')
        );
        $folderId=$file1->id;
        getData($id,$name,$folderId);
        
    }
    header('Location:https://drive.google.com');
} else {
    error_reporting(E_ALL);
  $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/photo/googleDrive/oauth2callback.php';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}