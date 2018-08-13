<?php
session_start();

$response = isset($_POST ['response']) ? $_POST ['response'] : "";

$_SESSION['fbid']=$response['authResponse']['userID'];
$_SESSION['fbAccessToken']=$response['authResponse']['accessToken'];
$_SESSION['status']=$response['status'];
?>