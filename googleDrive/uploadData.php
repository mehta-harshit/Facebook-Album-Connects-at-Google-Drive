<?php
require_once 'google-api-php-client/vendor/autoload.php';
session_start();
$fbaccess_token=$_SESSION['fbAccessToken'];
$mainFolderId = isset($_POST['mainFolderId']) ? $_POST['mainFolderId'] : "";
$albumData=$_SESSION['albumData'];
$albumData=json_decode($albumData);
if ($albumData!="") {
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

$client = new Google_Client();
$access_token = $_SESSION['driveAccessToken'];
$client->setAccessToken($access_token);
$drive_service = new Google_Service_Drive($client);
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
?>