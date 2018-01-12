<?
require_once '../../Package/fbsdk/src/Facebook/autoload.php';
session_start();
$info=$_GET['id'];
$info=explode("_",$info);
$bot_id= $info[0];
$mode=$info[1];
$name =$info[2];
$up=new Update_AcessToken($bot_id);
if($mode==60132)
{
  $up->getAcessToken();
  $id=$up->getUserID();
  $up->SetTOMySQL($id);
}
else if($mode==63152)
{
  $up->getAcessToken();
  $user=$up->getUserProfile();
  $checkname =$user['first_name'].$user['last_name'];
  if(!strcmp($name,$checkname))
  {
    $up->register($user['id'],$user['name'],$user['email']);
  }
  else
  {
    echo "登入的FaceBook帳號與Messanger不同!,請重新登陸您的Facebook帳號";
  }
}
else
{
  echo "error_mode";
}
class Update_AcessToken
{
  protected $fb;
  protected $access_token;
  protected $mysqli;
  protected $bot_id;
  protected $user_id;
  function __construct($var1) {
     $this->fb = new Facebook\Facebook([
      'app_id' => '134521967167442', // Replace {app-id} with your app id
      'app_secret' => '900f15662dd99bc1e3e5ee61036a1963',
      'default_graph_version' => 'v2.11',
      ]);
     $DBNAME = "h_f";
     $DBUSER = "root";
     $DBPASSWD = "00000000";
     $DBHOST = "localhost";
     $this->mysqli = new Mysqli($DBHOST, $DBUSER, $DBPASSWD, $DBNAME);
     if ($this->mysqli->connect_errno) {
      printf("Connect failed: %s\n", $mysqli->connect_error);
      exit();
    }
    $this->bot_id=$var1;
  }
  function getAcessToken()
  {
    $helper = $this->fb->getRedirectLoginHelper();
    try {
      $accessToken = $helper->getAccessToken();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
      // When Graph returns an error
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
      // When validation fails or other local issues
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }

    if (! isset($accessToken)) 
    {
      if ($helper->getError()) {
        header('HTTP/1.0 401 Unauthorized');
        echo "Error: " . $helper->getError() . "\n";
        echo "Error Code: " . $helper->getErrorCode() . "\n";
        echo "Error Reason: " . $helper->getErrorReason() . "\n";
        echo "Error Description: " . $helper->getErrorDescription() . "\n";
      } else {
        header('HTTP/1.0 400 Bad Request');
        echo 'Bad request';
      }
      exit;
    }
    $oAuth2Client = $this->fb->getOAuth2Client();
    $tokenMetadata = $oAuth2Client->debugToken($accessToken);
    $tokenMetadata->validateAppId('134521967167442');
    $tokenMetadata->validateExpiration();
    if (! $accessToken->isLongLived()) {
      // Exchanges a short-lived access token for a long-lived one
      try {
        $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
      } catch (Facebook\Exceptions\FacebookSDKException $e) {
        echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
        exit;
      }
      //var_dump($accessToken->getValue());
    }
    $_SESSION['fb_access_token'] = (string) $accessToken;
    $this->access_token=(string) $accessToken;
    return $this->access_token;
  }
  function getUserID()
  {
    try 
    {
      $response = $this->fb->get('/me',$this->access_token);
    } 
    catch(\Facebook\Exceptions\FacebookResponseException $e) 
    {
      // When Graph returns an error
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } 
    catch(\Facebook\Exceptions\FacebookSDKException $e) 
    {
      // When validation fails or other local issues
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }
    $me = $response->getGraphUser();
    $this->user_id = $me->getId();
    return $this->user_id;
  }
  function getUserProfile()
  {
    try 
    {
      $response = $this->fb->get('/me/?fields=first_name,last_name,name,email',$this->access_token);
    } 
    catch(\Facebook\Exceptions\FacebookResponseException $e) 
    {
      // When Graph returns an error
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } 
    catch(\Facebook\Exceptions\FacebookSDKException $e) 
    {
      // When validation fails or other local issues
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }
    $me = $response->getGraphUser();
    $name = $me->getName();
    $first_name = $me->getFirstName();
    $last_name = $me->getLastName();
    $email= $me->getEmail();
    $id=$me->getId();
    return array("id" => $id,"name" => $name,"first_name"=>$first_name,"last_name"=>$last_name,"email" => $email);

  }
  function register($id,$name,$email)
  {
    $date=date("Y-m-d" , mktime(0,0,0,date("m"),date("d")+11,date("Y")));
    $sql  = "INSERT INTO `member` (`FB_ID`, `BOT_ID`, `Name`, `Email`, `AccessToken`, `expiration`) VALUES ('$id','$this->bot_id','$name','$email','$this->access_token','$date')";
    if($this->mysqli->query($sql))
        {
          if(!file_exists("../../Data/User/".$id))
            mkdir("../../Data/User/".$id, 0700);
          if(!file_exists("../../Data/User/".$id."/FanPageLatestPost"))
            mkdir("../../Data/User/".$id."/FanPageLatestPost", 0700);
          echo"<h1>註冊成功!!</h1>";
        }
      else
        echo "你已經註冊過了";
  }
  function SetTOMySQL($id)
  {
    $sql  = "SELECT `FB_ID` FROM `member` WHERE `BOT_ID` LIKE '$this->bot_id'";
    $result=$this->mysqli->query($sql);
    $row = $result->fetch_array(MYSQLI_NUM);
    $userid=$row[0]; 
    if(!strcmp($id,$userid))
    {
      $date=date("Y-m-d" , mktime(0,0,0,date("m"),date("d")+11,date("Y")));
      $sql  = "UPDATE `member` SET `AccessToken` = '$this->access_token', `expiration` = '$date' WHERE `member`.`FB_ID` = '$id'";
      if($this->mysqli->query($sql))
        echo"更新權杖成功!!";
      else
        echo "failed";
    }
    else if($userid==null)
    {
      echo "尚未啟動服務，請先啟動服務";
    }
    else
    {
      echo "登入的FaceBook帳號與Messanger不同!,請重新確認";
    }
  }
}
?>