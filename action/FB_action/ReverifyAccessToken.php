﻿<?
require_once '../../Package/fbsdk/src/Facebook/autoload.php';
session_start();
$id=$_GET['id'];
$get_data=new Get_FB_Data($id);
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
       $loginUrl = $helper->getLoginUrl('http://cloudsoftwarelab404.info/h_f/action/FB_action/fb-callback.php?id='.$id, $permissions);
       //echo $loginUrl;
       header('Location: '.$loginUrl); 
   }
   
}

?>