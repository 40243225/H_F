<?
require_once '../../Package/fbsdk/src/Facebook/autoload.php';
$id=$_GET['id'];
$get_data=new Get_FB_Data($id);
$get_data->checkAcessToken($id);
class Get_FB_Data
{
  protected $fb;
  protected $access_token;
  protected $bot_id;
   function __construct($var1) {
       $this->fb= new \Facebook\Facebook([
        'app_id' => '134521967167442',
        'app_secret' => '900f15662dd99bc1e3e5ee61036a1963',
        'default_graph_version' => 'v2.11',
        //'default_access_token' => 'EAAB6WNYRR9IBAM7MIUgDy3RkuUMlCPWmTY8QhxZBjIpMhHiXMmAxDW2qSo5B8Lq47R7eXN4hnlhMQxkTH6fhgMxCP7D0DFIlhHvRmhjoxxxicAHMyLKg3hDzlwgIzxpZBXYxqtG8uXZCBsRXdVvDkaZAoXXSMfiM8Yy3QOXP7MzoMSJ1ZANQF1e6qJ68ipTOQOk2Bv6VYvwZDZD', // optional
      ]);
       //$helper = $this->fb->getRedirectLoginHelper();
       //$permissions = ['email,public_profile,email,user_likes,user_posts']; // Optional permissions
       //$loginUrl = $helper->getLoginUrl('http://cloudsoftwarelab404.info/h_f/action/FB_action/fb-callback.php?id='.$id, $permissions);
       //echo $loginUrl;
       //header('Location: '.$loginUrl); 
	   $this->bot_id=$var1;
   }
   function tryAccessToken()
   {
      try {
        $request = $this->fb->get('/me');
      } catch(Facebook\Exceptions\FacebookResponseException $e) {
        if ($e->getCode() == 190) {
          return false;
        }
      } catch(Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        return false;
      }
      return true;
   }
   function checkAcessToken($id)
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
      $sql  = "SELECT `AccessToken` FROM `member` where `BOT_ID` ='$id' ";
      $result=$mysqli->query($sql);
      $row = $result->fetch_array(MYSQLI_NUM);
      $this->access_token=$row[0];
      $this->fb->setDefaultAccessToken((string)$this->access_token);
      $temp=$this->tryAccessToken();
      if($temp==true)
      {     
          $this->Start();
      }
      else
      {
         $handle = fopen("http://140.130.35.73/bot2.0/test.php?id=".$this->bot_id."&text=1", "r");
         fclose($handle);
      }
   }
   function Start()
   {
      $this->MyID();
      $handle = fopen("http://140.130.35.73/bot2.0/test.php?id=".$this->bot_id."&text=2", "r");
      fclose($handle);
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
      //echo 'Logged in as ' . $me->getId()."<br>";
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
      $this->FanPagePost($id,$data['id'],$data['name']);
    }
	
    //$this->FanPagePost("159143227613420","123");
  }
  function FanPagePost($id,$UserLikes,$UserLikesName)
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
    $i=1;
    foreach ($posts as $data) {
      $time=$data['created_time'];
      //print_r($time);
      $date=new DateTime();
      $date=date_format($time,'Y-m-d H:i:s');
      //echo "時間:".$date."<br>";
      //echo "內容:".$data['message']."<br>";
      //這是OBJECT    
      $myfile = fopen("C:/AppServ/www/H_F/Data/User/".$id."/FanPageLatestPost/".$UserLikesName.$i.".txt", "w");
      $txt = $date.",".$data['message'];
      fwrite($myfile,$txt);
      fclose($myfile);
      //echo "時間:".$time->DateTime->date."<br>";
      $i++;
    }
    echo"<br>";
    
  }
}

?>