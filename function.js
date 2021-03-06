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
        $("#downloadModal").modal('show');
        $(".progress").css('display','block');
        $('.myprogress').css('width', '0');
        $("#success").css("display","none");
        $("#download").css("display","none");
        $.ajax({
            url: "zip.php",
            method: "POST",
            data: { albumData : albumData },
            dataType: "json",
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / 100;
                        percentComplete = parseInt(percentComplete * 100);
                        $('.myprogress').text(percentComplete + '%');
                        $('.myprogress').css('width', percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            success:function( msg ) {
                $(".progress").css('display','none');
                $("#error").css("display","none");
                $("#success").css("display","block");
                $("#successText").html("Zip Created Successfuly.Download it");
                $("#download").css("display","block");
                var tmpFile = document.getElementById('tmpFile');
                tmpFile.setAttribute("href","https://www.staging.nystrading.com/photo/"+msg.tmpFile);
            },
    });
    }
    function zipFileSelectedAll(flag) {
        $("#downloadModal").modal('show');
        $(".progress").css('display','block');
        $('.myprogress').css('width', '0');
        $("#success").css("display","none");
        $("#download").css("display","none");
        var albumData = [];
        if (flag===0) {
            $('.custom-chk').each(function(){
                this.checked = true;
            });
            $("input[name='checked']:checked").each(function(){
                albumData.push(this.value);
            });
            $('.custom-chk').each(function(){
                this.checked = false;
            });
        }else if (flag === 1){
            $("input[name='checked']:checked").each(function(){
                albumData.push(this.value);
            });
        }else{
            albumData="";
        }
        if (albumData!="" ) {            
            $.ajax({
                url: "zip.php",
                method: "POST",
                data: { albumData : albumData },
                dataType: "json",
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / 100;
                            percentComplete = parseInt(percentComplete * 100);
                            $('.myprogress').text(percentComplete + '%');
                            $('.myprogress').css('width', percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success:function( msg ) {
                    $(".progress").css('display','none');
                    $("#error").css("display","none");
                    $("#success").css("display","block");
                    $("#successText").html("Zip Created Successfuly.Download it");
                    var tmpFile = document.getElementById('tmpFile');
                    tmpFile.setAttribute("href","https://www.staging.nystrading.com/photo/"+msg.tmpFile);
                    $("#download").css("display","block");
                    $('.custom-chk').each(function(){
                        this.checked = false;
                    });
                }
            });
        }else{
            $(".progress").css('display','none');
            $("#error").css("display","block");
            $("#success").css("display","none");
            $("#errorText").html("Select an album to download.");  
        }
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
        $('.custom-chk').each(function(){
            this.checked = true;
        });
        var albumData = [];
        $("input[name='checked']:checked").each(function(){
            albumData.push(this.value);
        });
        $('.custom-chk').each(function(){
            this.checked = false;
        });
        var checkJson = encodeURIComponent(JSON.stringify(albumData));
        window.open("googleDrive/index.php?albumData="+ checkJson);
    }
    function downloadZip() {
        tmpFile.click();
        $("#downloadModal").modal('hide');
        $("#success").css("display","none");
        $("#download").css("display","none");
    }