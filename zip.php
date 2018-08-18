<?php

session_start();
$albumData = isset($_POST['albumData']) ? $_POST['albumData'] : die('Album ID not specified.');
if (is_array($albumData)) {
    foreach ($albumData as $a) {
        $sp[] = explode('_', $a);
    }
} else {
    $sp[] = explode('_', $albumData);
}

for ($i = 0; $i < count($sp); $i++) {
    $albumReData[$sp[$i][0]] = $sp[$i][1];
}
$access_token = $_SESSION['fbAccessToken'];

$zip = new ZipArchive();

$tmpFile = tempnam('.', '');
$zip->open($tmpFile, ZipArchive::CREATE);

foreach ($albumReData as $name => $id) {
    $zip->addEmptyDir($name);
    $jsonLink = "https://graph.facebook.com/v3.1/{$id}/photos?fields=source,images,name&access_token={$access_token}";
    $json = file_get_contents($jsonLink);

    $obj = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);

    $photo_count = count($obj['data']);

    $y = 1; //added for adding image name
    for ($x = 0; $x < $photo_count; $x++) {
        $source = isset($obj['data'][$x]['source']) ? $obj['data'][$x]['source'] : '';
        $downloadFile = file_get_contents($source);
        //add it to the zip
        $zip->addFromString($name.'/'.'img'.$y.'.jpg', $downloadFile);
        $y++;
    }
}
// close zip
$zip->close();
$rand = rand(1, 1000);
$fileName = 'FbAlbumPhotos'.$rand.'.zip';
header('Content-Type: application/octet-stream');
header('Pragma: private');
file_put_contents($fileName, fopen($tmpFile, 'r'));
unlink($tmpFile);

die(json_encode(['success'=>'true', 'tmpFile'=>$fileName]));
