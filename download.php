<?php
session_start();
$tmpFile = isset($_POST['tmpFile']) ? $_POST['tmpFile'] : "";

header('Content-Type: application/octet-stream');
header('Pragma: private');
file_put_contents("FbAlbumPhotos.zip",fopen($tmpFile, 'r'));

// zip up the contents
//chdir($path);
// var_dump(exec("zip -r {$tmpFile} ./"));


//$filename = "{$name}.zip";
// header('Content-Disposition: attachment; filename=FbAlbumPhotos.zip');
// header('Content-type: application/zip');
// header('Content-Transfer-Encoding: binary');
// readfile($tmpFile);
exit();
/*header('Content-disposition: attachment; filename=FbAlbumPhotos.zip');
readfile($tmpFile);*/

die(json_encode(array("success"=>"true")));

?>