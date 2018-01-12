<?php
$visits = $HTTP_COOKIE_VARS['visits'];
if (!isset($visits)) $visits = 1;
setcookie("visits", $visits+1);
?>
<p>歡迎您第 <?=$visits?> 次光臨!</p>