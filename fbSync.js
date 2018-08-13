(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.1&appId=280992489328105&autoLogAppEvents=1';
  fjs.parentNode.insertBefore(js, fjs);
}
(document, 'script', 'facebook-jssdk'));
function checkLoginState() {
  FB.login(function(response) {
    if (response.authResponse) {
      FB.api('/me', function(response) {
        console.log('Good to see you, ' + response.name + '.');
      });
    } else {
      console.log('User cancelled login or did not fully authorize.');
    }
  }, {scope: 'public_profile,user_photos',auth_type: 'rerequest'}  );
  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });
}
function statusChangeCallback(response) {
  if (response.status === 'connected') {
    var accessToken = response.authResponse.accessToken;
    var request = $.ajax({
      url: "session.php",
      method: "POST",
      data: { response : response },
    });
    request.done(function( msg ) {
      console.log(response.status);
      $("#login").hide();
      $("#album").load(location.href+" #album>*","");
      $("#album").css("display","block");
    });
    request.fail(function( jqXHR, textStatus ) {
      alert( "Request failed: " + textStatus );
    });
  } else {
   console.log('Please log into this app.');
 }
}
function logout() {
  FB.getLoginStatus(function(response) {
    FB.logout(function(connected) {
      console.log('logout');      
      document.location = 'logout.php';    
    });
  });
}
window.fbAsyncInit = function() {
  FB.init({
    appId      : '{280992489328105}',
    cookie     : true,  
    xfbml      : true,  
    version    : 'v3.1' 
  });
  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });
};

(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "https://connect.facebook.net/en_US/sdk.js";
  fjs.parentNode.insertBefore(js, fjs);
}
(document, 'script', 'facebook-jssdk'));