<?php
require_once("../db_info.php");
$name =$_POST['name'];
$id =$_POST['id'];
$email =$_POST['email'];
$botid=$_POST['botid'];
$sql  = "INSERT INTO `member` (`FB_ID`, `BOT_ID`, `Name`, `Email`) VALUES ('$id', '$botid', '$name', '$email')";
$mysqli->query($sql);
if(!file_exists("../../Data/User/".$id))
  mkdir("../../Data/User/".$id, 0700);
if(!file_exists("../../Data/User/".$id."/FanPageLatestPost"))
  mkdir("../../Data/User/".$id."/FanPageLatestPost", 0700);
echo $id;
?> 