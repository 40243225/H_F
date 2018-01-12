<?php 
    //$id="1504269209657368"; 
    $id=$_POST['id'];
    if(!file_exists("./Data/User/".$id))
      mkdir("./Data/User/".$id, 0700);
    if(!file_exists("./Data/User/".$id."/FanPageLatestPost"))
      mkdir("./Data/User/".$id."/FanPageLatestPost", 0700);
    echo "
    
    ";
 ?>