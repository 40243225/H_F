<html>
<head>
<title>Facebook Login JavaScript Example</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<script type="text/javascript">
  <?php
    $id=$_GET['id'];
    $name=$_GET['name']
  ?>// This is called with the results from from FB.getLoginStatus().
function fb_login()
{ // FB 第三方登入，要求公開資料與email
    FB.login(function(response)
    {
        statusChangeCallback(response);
        console.log(response);
    }, {scope: 'public_profile,email,user_likes,user_posts'});
}  
  function statusChangeCallback(response) {
    if (response.status === 'connected') {
      // Logged into your app and Facebook.
      FB.api('/me', function(response) { //目前只能25筆  .limit(100)還要再試試
        if (response && !response.error) {
        var UserID = response.id;
        var UserName=response.name;
        var name ="<?php echo $name;?>";
        var botid="<?php echo $id;?>";
        //alert(response.id+response.name+response.email)
        document.getElementById("fbbtn").style.visibility="hidden"; 
        if(UserName.match(name)!=null)
        {
          if(confirm("是否要連結聊天機器人?"))
          {
            alert("userid:"+UserID+"id="+botid)
             ToMySQL_FBRegister(UserID,botid)      
          }
        }
        else
          alert("Facebook登入的帳號與Menssager的不同，請重新確認"); 
        }
      });
      //myID();
    } else {
      // The person is not logged into your app or we are unable to tell.
      document.getElementById("fbbtn").style.visibility="visible"; 
      document.getElementById('status').innerHTML = '請先登入fb';

    }
  }
  function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }
  window.fbAsyncInit = function() {
  FB.init({
    appId      : '134521967167442',
    cookie     : true,  // enable cookies to allow the server to access 
                        // the session
    xfbml      : true,  // parse social plugins on this page
    version    : 'v2.11' // use graph api version 2.8
  })
  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });

  };
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/zh_TW/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));

function ToMySQL_FBRegister(id,bot_id)
{ //找出資料庫中最近點讚之貼文
  if (window.XMLHttpRequest) {
  // code for IE7+, Firefox, Chrome, Opera, Safari
     xmlhttp = new XMLHttpRequest();
  } else {
      // code for IE6, IE5
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  }

  xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
          //alert(this.responseText.trim());
          UserID=this.responseText.trim();
          //alert(UserID);
          ULikes(UserID);
      }
  };

  var payload = {id: id, bot_id:bot_id};
  xmlhttp.open("POST","http://140.130.35.73/h_f/action/FB_action/FB_Connect2.php",true);
  xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=UTF-8');
  var encodedData = encodeFormData(payload);
  xmlhttp.send(encodedData);
  //ODToMySQL(PostsID);
}
function encodeFormData(data) {
    if (!data) return "";    // Always return a string
    var pairs = [];          // To hold name=value pairs
    for (var name in data) {                                  // For each name
        if (!data.hasOwnProperty(name)) continue;            // Skip inherited
        if (typeof data[name] === "function") continue;      // Skip methods
        var value = data[name].toString();                   // Value as string
        name = encodeURIComponent(name.replace(" ", "+"));   // Encode name
        value = encodeURIComponent(value.replace(" ", "+")); // Encode value
        pairs.push(name + "=" + value);   // Remember name=value pair
    }
    return pairs.join('&'); // Return joined pairs separated with &
}
</script>
<input id="fbbtn" type="image" onClick="fb_login()" src="./image/fblogin.png" style="visibility:hidden;"/>
</div>
<div id="textDiv"></div>  



</body>
</html>