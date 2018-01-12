<?
require_once '../fbsdk/src/Facebook/autoload.php';
include("db_info.php"); 
session_start();
$id=$_GET['id'];
$get_data=new Get_FB_Data($id);
$get_data->checkAcessToken();
class Get_FB_Data
{
  protected $fb;
  protected $access_token;
   function __construct($id) {
       $this->fb= new \Facebook\Facebook([
        'app_id' => '134521967167442',
        'app_secret' => '900f15662dd99bc1e3e5ee61036a1963',
        'default_graph_version' => 'v2.11',
        //'default_access_token' => 'EAAB6WNYRR9IBAM7MIUgDy3RkuUMlCPWmTY8QhxZBjIpMhHiXMmAxDW2qSo5B8Lq47R7eXN4hnlhMQxkTH6fhgMxCP7D0DFIlhHvRmhjoxxxicAHMyLKg3hDzlwgIzxpZBXYxqtG8uXZCBsRXdVvDkaZAoXXSMfiM8Yy3QOXP7MzoMSJ1ZANQF1e6qJ68ipTOQOk2Bv6VYvwZDZD', // optional
      ]);
       $helper = $this->fb->getRedirectLoginHelper();
       $permissions = ['email,public_profile,email,user_likes,user_posts']; // Optional permissions
       $loginUrl = $helper->getLoginUrl('http://cloudsoftwarelab404.info/h_f/test/fb-callback.php?id='.$id, $permissions);
       //echo $loginUrl;
       header('Location: '.$loginUrl);
       
       
   }
   function checkAcessToken()
   {
      $DBNAME = "h_f";
      $DBUSER = "root";
      $DBPASSWD = "00000000";
      $DBHOST = "localhost";
      $mysqli = new Mysqli($DBHOST, $DBUSER, $DBPASSWD, $DBNAME);
        if ($mysqli->connect_errno) {
          printf("Connect failed: %s\n", $mysqli->connect_error);
         exit();
      }
      //$sql  = "UPDATE `member` SET `AccessToken` = '', `expiration` = '' WHERE `member`.`FB_ID` = \'1504269209657368\'";
      $sql  = 'SELECT `expiration`,`AccessToken` FROM `member` where `FB_ID` = 1504269209657368';
      $result=$mysqli->query($sql);
      $row = $result->fetch_array(MYSQLI_NUM);
      $expiration=$row[0];
      $now=date("Y-m-d" , mktime(0,0,0,date("m"),date("d"),date("Y")));
      if(strtotime($expiration)<strtotime($now))
      {
          $this->access_token=$row[1];
          $this->Start();
      }
      else
        echo "請重新驗證";
   }
   function Start()
   {
      $this->MyID();

   }
   function MyID()
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
      $id = $me->getId();
      echo 'Logged in as ' . $me->getId()."<br>";
      $this->UserLikes($id);
   }
   function UserLikes($id)
  {
      try{
      $response = $this->fb->get("/".$id.'/likes',$this->access_token);
    }
    catch(\Facebook\Exceptions\FacebookResponseException $e) {
      // When Graph returns an error
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } catch(\Facebook\Exceptions\FacebookSDKException $e) {
      // When validation fails or other local issues
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }
    $like = $response->getGraphEdge();
    foreach($like as $data)
    {
      $this->FanPagePost($data['id'],$data['name']);
    }
    //$this->FanPagePost("159143227613420","123");
  }
  function FanPagePost($UserLikes,$UserLikesName)
  {
    try{
      $response = $this->fb->get("/".$UserLikes.'/posts',$this->access_token);
    }
    catch(\Facebook\Exceptions\FacebookResponseException $e) {
      // When Graph returns an error
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } catch(\Facebook\Exceptions\FacebookSDKException $e) {
      // When validation fails or other local issues
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }
    $posts = $response->getGraphEdge();
    
    echo "粉專名稱:".$UserLikesName."<br>";
    foreach ($posts as $data) {

      $time=$data['created_time'];
      //print_r($time);
      $date=new DateTime();
      $date=date_format($time,'Y-m-d H:i:s');
      echo "時間:".$date."<br>";
      echo "內容:".$data['message']."<br>";
      //這是OBJECT   
      
      echo "<br>";

      //echo "時間:".$time->DateTime->date."<br>";
    }
    echo"<br>";
    
  }
}

?>