<html>
<head>
<title>Facebook Login JavaScript Example</title>
<meta charset="UTF-8">
</head>
<body>
<?php
    $id=$_GET['id'];
    $name=$_GET['name']
?>
<script> // This is called with the results from from FB.getLoginStatus().
function fb_login()
{
    // FB 第三方登入，要求公開資料與email
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
        //alert(response.id+response.name+response.email)
        document.getElementById("fbbtn").style.visibility="hidden"; 
        Uemail(UserID,UserName);
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

function myID() { //首先找出使用者點讚的粉絲專頁
  FB.api('/me', function(response) { //目前只能25筆  .limit(100)還要再試試
    console.log(response);

    if (response && !response.error) {
        console.log(response.data);
    var UserID = response.id;
    var UserName=response.name;
    //alert(response.id+response.name+response.email)
    Uemail(UserID,UserName)
      
    }
  });
 }
function Uemail(UserID,UserName) { //首先找出使用者點讚的粉絲專頁
  FB.api('/'+ UserID +'/?fields=email', function(response) { //目前只能25筆  .limit(100)還要再試試
    console.log(response);
    var str=""
    if (response && !response.error) {
        var LikesData = response.data; //使用者喜歡的粉專
        console.log(response.data);
      var UserEmail=response.email;
      ToMySQL_FBRegister(UserID,UserName,UserEmail)
    }
  });
 }

function ULikes(UserID) {
 //首先找出使用者點讚的粉絲專頁
 if(confirm("是否開始載入資料?"))
{
  FB.api('/' + UserID + '/likes', function(response) { //目前只能25筆  .limit(100)還要再試試
    console.log(response);
    var str=""
    if (response && !response.error) {
        var LikesData = response.data; //使用者喜歡的粉專
        console.log(response.data);
      for(var i=0;i<25;i++){
      var UserLikes = LikesData[i].id;
      var UserLikes_name = LikesData[i].name;
      str =str+"粉絲專業名稱為:"+UserLikes_name+"<br>";
      LikesPost(UserLikes,UserLikes_name,UserID,str);
      //LikesPost(UserLikes,UserID);
      }
      
    }
  });
}
}
function LikesPost(UserLikes,UserLikes_name,UserID,str){ //再來找出每筆粉絲專頁各自的貼文
    FB.api('/'+ UserLikes +'/posts', function(response) {
    console.log(response);
    if (response && !response.error) {
        var PostsData = response.data; //粉專發佈的貼文
        console.log(response.data);
    //PostLikes = PostsData[0].likes; //對貼文點讚的人
      for(var i=0;i<25;i++){
      var PostsID = PostsData[i].id; //每篇貼文的ID
      var PostsMessage = PostsData[i].message;
      var time =PostsData[i].created_time;
      ToMySQL_FB(i,UserID,UserLikes_name,PostsMessage);
      //str=str+"貼文內容"+i+":"+PostsMessage+"時間"+time+"<BR>";
      //Posts(PostsID,UserID);
      }
       //貼文
      //alert(PostsData[0].id);
    }
    
    
    
    });
}
function ToMySQL_FB(i,id,UserLikes_name,PostsMessage)
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
        document.getElementById("txtHint").innerHTML = this.responseText;
    }
};
var payload = {id: id,fpn: UserLikes_name, pm: PostsMessage,i:i};
xmlhttp.open("POST","action/FB_action/FB_Member.php",true);
xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=UTF-8');
var encodedData = encodeFormData(payload);
xmlhttp.send(encodedData);
//ODToMySQL(PostsID);
    
}
function ToMySQL_FBRegister(id,Name,Email)
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

var payload = {id: id,name: Name,email:Email};
xmlhttp.open("POST","action/FB_action/FB_Register.php",true);
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