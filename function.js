function slideshow(id) 
{
    var albumData = id
    $.ajax({
        url: "photo.php",
        method: "POST",
        data: { albumData : albumData },
        dataType: "json",
        success:function( msg ) {
            $("#slides").html(msg.data);
            $("#columnGets").html(msg.column);
            showSlides(1);
        },
        error:function( jqXHR, textStatus ) {
            console.log( "Request failed: " + textStatus );
        }
    });
}  
function openModal() {
  document.getElementById('myModal').style.display = "block";
}

function closeModal() {
  document.getElementById('myModal').style.display = "none";
}

var slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
    var i;
    var slides = document.getElementsByClassName("mySlides");
    var dots = document.getElementsByClassName("demo");
    if (n > slides.length) {slideIndex = 1}
        if (n < 1) {slideIndex = slides.length}
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            slides[slideIndex-1].style.display = "block";
            dots[slideIndex-1].className += " active";
}
    function zipFile(albumData) {
        $('.myprogress').css('width', '0');
        $.ajax({
            url: "zip.php",
            method: "POST",
            data: { albumData : albumData },
            dataType: "json",
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                xhr.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        console.log(evt.loaded);
                        console.log(evt.total);
                        percentComplete = parseInt(percentComplete * 100);
                        console.log(percentComplete);
                        $('.myprogress').text(percentComplete + '%');
                        $('.myprogress').css('width', percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            success:function( msg ) {
                $("#prog").css('display','none');
                $("#error").css("display","none");
                $("#success").css("display","block");
                $("#successText").html("Zip Created Successfuly.Download it");
                $("#prog").css('display','none');
                $("#tmpFile").attr("href","https://www.staging.nystrading.com/photo/"+msg.tmpFile);
                $("#downloadModal").modal('show');
            },
            error:function( jqXHR, textStatus ) {
              console.log( "Request failed: " + textStatus );
          }
    });
    }
    function zipFileSelected() {
        var albumData = [];
        $("input[name='checked']:checked").each(function(){
            albumData.push(this.value);
        });
        if (albumData!="") {            
            $.ajax({
                url: "zip.php",
                method: "POST",
                data: { albumData : albumData },
                dataType: "json",
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            percentComplete = parseInt(percentComplete * 100);
                            $('.myprogress').text(percentComplete + '%');
                            $('.myprogress').css('width', percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success:function( msg ) {
                    $("#error").css("display","none");
                    $("#success").css("display","block");
                    $("#successText").html("Zip Created Successfuly.Download it");
                    $("#prog").css('display','none');
                    $("#tmpFile").val(msg.tmpFile);
                    $("#downloadModal").modal('show');
                },
                error:function( jqXHR, textStatus ) {
                    console.log( "Request failed: " + textStatus );
                }
            });
        }else{
            $("#error").css("display","block");
            $("#success").css("display","none");
            $("#errorText").html("Select an album to download.");  
        }
    }
	function zipFileAll() {
		$('.checkbox').each(function(){
			this.checked = true;
		});
		var albumData = [];
		$("input[name='checked']:checked").each(function(){
            albumData.push(this.value);
        });
		$('.checkbox').each(function(){
			this.checked = false;
		});
		$.ajax({
			url: "zip.php",
			method: "POST",
			data: { albumData : albumData },
			dataType: "json",
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        percentComplete = parseInt(percentComplete * 100);
                        $('.myprogress').text(percentComplete + '%');
                        $('.myprogress').css('width', percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
			success:function() {
                $("#error").css("display","none");
                $("#success").css("display","block");
                $("#successText").html("Zip Created Successfuly.Download it");
				$(".myprogress").css('display','none');
			},
			error:function( jqXHR, textStatus ) {
				console.log( "Request failed: " + textStatus );
			}
		});
	}
    function move(albumData) {
        var checkJson = encodeURIComponent(JSON.stringify(albumData));
        window.open("googleDrive/index.php?albumData="+ checkJson);
    }
    function moveSelected() {
        var albumData = [];
        $("input[name='checked']:checked").each(function(){
            albumData.push(this.value);
        });
        if (albumData!="") {
            var checkJson = encodeURIComponent(JSON.stringify(albumData));
            window.open("googleDrive/index.php?albumData="+ checkJson);
        }else{
            $("#error").css("display","block");
            $("#success").css("display","none");
            $("#errorText").html("Select any album to move.");
        }
    }
    function moveAll() {
        $('.checkbox').each(function(){
            this.checked = true;
        });
        var albumData = [];
        $("input[name='checked']:checked").each(function(){
            albumData.push(this.value);
        });
        $('.checkbox').each(function(){
            this.checked = false;
        });
        var checkJson = encodeURIComponent(JSON.stringify(albumData));
        window.open("googleDrive/index.php?albumData="+ checkJson);
    }
    function downloadZip() {
        $("#downloadModal").modal('hide');
        $("#success").css("display","none");
    }