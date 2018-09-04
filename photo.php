<?php
session_start();
$albumData = isset($_REQUEST['albumData']) ? $_REQUEST['albumData'] : die('Album ID not specified.');
$access_token=$_SESSION['fbAccessToken'];

$data=getData();
function getData($after = NULL,$slideNo = NULL)
{
	global $access_token,$albumData;
	if($after=="")
	{
		$data="";        
		$column="";
		$jsonLink = "https://graph.facebook.com/v3.1/{$albumData}/photos?fields=source,images,name&access_token={$access_token}&limit=100";
		$slideNo=1;
	}else{
		$jsonLink = "https://graph.facebook.com/v3.1/{$albumData}/photos?fields=source,images,name&access_token={$access_token}&limit=100&after=$after";
	}
	$json = file_get_contents($jsonLink);
	$jsonData = json_decode($json,true);
	$photo_count = count($jsonData['data']);
	if(!empty($jsonData['data']))
	{
		$after=$jsonData['paging']['cursors']['after'];
		for($x=0; $x<$photo_count; $x++){ 
			$source = isset($jsonData['data'][$x]['source']) ? $jsonData['data'][$x]['source'] : "";
			$name = isset($jsonData['data'][$x]['name']) ? $jsonData['data'][$x]['name'] : "";

			$data.='<div class="mySlides">';
			$data.='<img src="'.$source.'" style="width:100%">';
			$data.='</div>';
			$column.='<div class="column col-md-2">';
			$column.='<img class="demo cursor" src="'.$source.'" style="width:100px" onclick="currentSlide('.$slideNo.')">';
			$column.='</div>';
			$slideNo++;
		}
		getData($after,$slideNo);
	}
	return array($data,$column);
}
die(json_encode(array("success"=>"True","data"=>$data[0],"column"=>$data[1])));