<?php
require_once("../db_info.php");
$id =$_POST['id'];
$bot_id =$_POST['bot_id'];
if(isset($id) &&isset($bot_id))
{
	$sql  = "UPDATE `member` SET `BOT_ID` = '$bot_id' WHERE `member`.`FB_ID` = $id";
	$mysqli->query($sql);
}

?> 