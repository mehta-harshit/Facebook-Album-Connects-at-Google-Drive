<?php
session_start();
require_once 'google-api-php-client/vendor/autoload.php';

$fbaccess_token=$_SESSION['fbAccessToken'];
$albumData = isset($_REQUEST['albumData']) ? $_REQUEST['albumData'] : "";
if ($albumData!="") {
    $_SESSION['albumData']=$albumData;
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
} else {
  $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/photo/googleDrive/oauth2callback.php';
  echo "<script>window.location.href='$redirect_uri'</script>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Google Drive</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <div class="row text-center">
        <h1>Hello <?php echo $profileData['name']; ?>, your data is uploading...</h1>
        <h3>Check out on <a href='https://drive.google.com/drive/my-drive'><button onclick="uploadData()" class="btn btn-success btn-lg">Google Drive</button></a></h3>
    </div>
</div>
</body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript">
    var mainFolderId =<?php echo '"'.$mainFolderId.'"'; ?>;
    function uploadData() {
        $.ajax({
            url: "uploadData.php",
            method: "POST",
            data: { mainFolderId : mainFolderId },
            dataType: "json",
    });
    }
</script>