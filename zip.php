<?php
session_start();
$albumData = isset($_POST['albumData']) ? $_POST['albumData'] : die('Album ID not specified.');
if (is_array($albumData)) {
	foreach ($albumData as $a) {
		$sp[]=explode("_",$a);
	}
}else{
	$sp[]=explode("_",$albumData);
}

for ($i=0; $i < count($sp); $i++) { 
	$albumReData[$sp[$i][0]]=$sp[$i][1];
}
$access_token=$_SESSION['fbAccessToken'];

$zip = new ZipArchive();

$tmpFile = tempnam('.','');
$zip->open($tmpFile, ZipArchive::CREATE); 
function getData($id,$name,$after = NULL,$postfix=NULL)
{
	global $access_token,$zip;
	if($after=="")
	{
		$jsonLink = "https://graph.facebook.com/v3.1/{$id}/photos?fields=source,images,name&access_token={$access_token}&limit=100";
		$json = file_get_contents($jsonLink);
		$obj = json_decode($json,true);
		$photo_count = count($obj['data']);
		$after=$obj['paging']['cursors']['after'];
	    $postfix=1; #postfix for image name
	    for($x=0; $x<$photo_count; $x++){
	    	$source = isset($obj['data'][$x]['source']) ? $obj['data'][$x]['source'] : "";
	    	$downloadFile = file_get_contents($source);
	    	$zip->addFromString($name.'/'."img".$postfix.".jpg",$downloadFile);
	    	$postfix++;
	    }
	    getData($id,$name,$after,$postfix);
	}else{
		$jsonLink = "https://graph.facebook.com/v3.1/{$id}/photos?fields=source,images,name&access_token={$access_token}&limit=100&after=$after";
		$json = file_get_contents($jsonLink);
		$obj = json_decode($json,true);
		$photo_count = count($obj['data']);
		if(!empty($obj['data']))
		{
			$after=$obj['paging']['cursors']['after'];
			for($x=0; $x<$photo_count; $x++){
				$source = isset($obj['data'][$x]['source']) ? $obj['data'][$x]['source'] : "";
				$downloadFile = file_get_contents($source);
				$zip->addFromString($name.'/'."img".$postfix.".jpg",$downloadFile);
				$postfix++;
			}
			getData($id,$name,$after,$postfix);
		}
	}
	return 0;
}

foreach ($albumReData as $name => $id) {
	$zip->addEmptyDir($name);
	getData($id,$name);
}
# close zip
$zip->close();
$rand=rand(1,1000);
$fileName="FbAlbumPhotos".$rand.".zip";
header('Content-Type: application/octet-stream');
header('Pragma: private');
file_put_contents($fileName,fopen($tmpFile, 'r'));
unlink($tmpFile);

die(json_encode(array("success"=>"true","tmpFile"=>$fileName)));