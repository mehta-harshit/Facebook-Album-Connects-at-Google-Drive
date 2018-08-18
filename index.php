<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>FB Photo Album</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> 
    <link rel="stylesheet" href="style.css">
<script src="fbSync.js"></script> 
</head>
<body>
<div class="text-center container" id="login">
	<h2 class="text-center">FB Photo Album</h2>       
	<div class="form-group col-md-12">
		<div class="fb-login-button btn btn-primary " data-max-rows="1" data-size="large" data-button-type="continue_with" data-show-faces="false" data-auto-logout-link="false" data-use-continue-as="false" onlogin="checkLoginState();" ></div>
	</div>
</div>
<div id="album" style="display: none;">
    <?php
    $fbid = $_SESSION['fbid'];
    $access_token = $_SESSION['fbAccessToken'];
    ?>
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
        <a class="navbar-brand" href="#">FB Photo Album</a>
    </div>
    <ul class="nav navbar-nav">
      <li class="active"><a href="#">All Albums</a></li>
  </ul>
  <ul class="nav navbar-nav navbar-right">
      <li><a href="" onclick="logout();"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
  </ul>
</div>
</nav>
<div class="container">
	<div class="row">
		<div class="col-md-10">
    <div class="alert alert-danger alert-dismissible"  id="error" style="display:none">
        <h4 id="errorText"></h4>
    </div>
    <div class="alert alert-success alert-dismissible" id="success" style="display:none">
        <h4 id="successText"></h4>
    </div>
    <?php
    $fields = 'id,name,description,link,cover_photo,count';
    $jsonLink = "https://graph.facebook.com/v3.1/{$fbid}/albums?fields={$fields}&access_token={$access_token}";
    $json = file_get_contents($jsonLink);

    $data = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);
    $albumCount = count($data['data']);
    if ($albumCount > 0) {
        for ($x = 0; $x < $albumCount; $x++) {
            $id = isset($data['data'][$x]['id']) ? $data['data'][$x]['id'] : '';
            $name = isset($data['data'][$x]['name']) ? $data['data'][$x]['name'] : '';
            $coverPhoto = isset($data['data'][$x]['cover_photo']['id']) ? $data['data'][$x]['cover_photo']['id'] : '';
            $count = isset($data['data'][$x]['count']) ? $data['data'][$x]['count'] : '';
            $show_pictures_link = "photos.php?album_id={$id}&album_name={$name}";
            $albumData = "'".$name.'_'.$id."'";
            if ($name != '') {
                ?>
                    <div class='col-md-6'>
                    	<div class="card">
                    		<a href='#' onclick="slideshow('<?php echo $id; ?>');openModal();">
                    			<img class='card-img-top img-responsive' src='https://graph.facebook.com/v3.1/<?php echo $coverPhoto; ?>/picture?access_token=<?php echo $access_token; ?>' alt='facebook album images' style='max-height: 230px;margin: 0 auto;'>
                    		</a>
                    		<div class='card-body'>
                    			<h3 class='card-text'>
                    				<a href='#' onclick="slideshow('<?php echo $id; ?>');openModal();"><?php echo $name; ?></a>
                    				<input type="checkbox" class="custom-chk" name="checked" id="checked" value="<?php echo $name.'_'.$id; ?>">
                    			</h3>
                    		</div>
                    		<p>
                    			<div style='color:#888;'><h4><?php echo $count; ?> Photos</h4></div>
                    			<button class="btn btn-primary" onclick="zipFile(<?php echo $albumData; ?>)">Download This Album</button>
                    			<button class="btn btn-warning" onclick="move(<?php echo $albumData; ?>)">Move</button>
                    			<a href="googleDrive/index.php?albumData=<?php echo json_encode([$name.'_'.$id]); ?>" target="_blank"></a>
                    		</p>
                        </div>
                    </div>
                    <?php
            }
        } ?>
            <div id="myModal" class="modalGallery">
                <span class="close cursor" onclick="closeModal()">&times;</span>
                <div class="modalGallery-content">
                    <div id="slides">
                    </div>
                    <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                    <a class="next" onclick="plusSlides(1)">&#10095;</a>
                    <div class="caption-container">
                      <p id="caption"></p>
                    </div>
                    <div id="columnGets">
                    </div>
                </div>
            </div>
        </div>
  <div class="col-md-2">
      <div class="col-md-12">
        <button class="btn btn-success btnMargin" onclick="zipFileAll()">Download All</button>
        <button class="btn btn-warning btnMargin" onclick="moveAll()">Move All</button>
        <button class="btn btn-success btnMargin" onclick="zipFileSelected()">Download Selected</button>
        <button class="btn btn-warning btnMargin" onclick="moveSelected()">Move Selected</button>
    </div>
</div>
    <?php
    } else {
        ?>
    <div class="col-md-12"><h1>No Any Album Found, Create some album on facebook.</h1></div>
    <?php
    }
?>
<div class="col-md-12">
	<div class="progress">
		<div class="progress-bar progress-bar-success myprogress" role="progressbar" style="width:0%">0%</div>
	</div>
</div>
</div>
</div>
<div class="modal fade" id="downloadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Download Zip File</h4>
            </div>
            <div class="modal-body text-center">
                <button type="button" id="download" class="btn btn-primary btn-lg" onclick="downloadZip()" style="margin: 0 auto;"><a href="" id="tmpFile" download style="color: white;text-decoration: none;">Download</a></button>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="function.js"></script>
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
</div>
</body>
</html>                                		                            