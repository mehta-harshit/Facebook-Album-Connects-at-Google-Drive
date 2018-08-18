<?php

session_start();
$albumData = isset($_REQUEST['albumData']) ? $_REQUEST['albumData'] : die('Album ID not specified.');
$access_token = $_SESSION['fbAccessToken'];

$jsonLink = "https://graph.facebook.com/v3.1/{$albumData}/photos?fields=source,images,name&access_token={$access_token}";
$json = file_get_contents($jsonLink);

$jsonData = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);

$photo_count = count($jsonData['data']);
$data = '';
$column = '';
$y = 1;
for ($x = 0; $x < $photo_count; $x++) {
    $source = isset($jsonData['data'][$x]['source']) ? $jsonData['data'][$x]['source'] : '';
    $name = isset($jsonData['data'][$x]['name']) ? $jsonData['data'][$x]['name'] : '';

    $data .= '<div class="mySlides">';
    $data .= '<img src="'.$source.'" style="width:100%">';
    $data .= '</div>';
    $column .= '<div class="column">';
    $column .= '<img class="demo cursor" src="'.$source.'" style="width:150px" onclick="currentSlide('.$y.')">';
    $column .= '</div>';
    $y++;
}
die(json_encode(['success'=>'True', 'data'=>$data, 'column'=>$column]));
