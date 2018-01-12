<?php
header('Content-type: text/html; charset=utf-8');
//include("db_info.php");
$id=$_POST['id'];
$UserLikes_name=$_POST['fpn'];
$PostsMessage=$_POST['pm'];
$i=$_POST['i'];
$myfile = fopen("../../Data/User/".$id."/FanPageLatestPost/".$UserLikes_name.$i.".txt", "w");
$txt = $PostsMessage;
fwrite($myfile,$txt);
fclose($myfile);
/*if(!strcmp($gender,'male'))
	$gender=0;
else
	$gender=1;
$sql = "INSERT INTO `Memeber_info` (`ID`, `Name`, `gender`) VALUES ('$id','$name','$gender')";
mysql_query($sql);*/
?> 