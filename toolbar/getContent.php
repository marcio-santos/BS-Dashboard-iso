<?php
  $name = $_POST['item'] ;
  
  /*
  $li = "<ol>";
  for($i=0;$i<100;$i++) {
      $li .="<li>".$name."</li>";
  }
  $li .= "</ol>";
  echo $li;
  */
  switch($name) {
      Case 'home': $i = '<iframe style="width:100%;height:100%;" src="http://ig.com.br"></iframe>' ;break;
      Case 'client': $i = '<iframe  style="width:100%;height:100%;" src="http://www.uol.com.br"></iframe>' ;break;
      Case 'graph': $i = '<iframe  style="width:100%;height:100%;" src="http://www.terra.com.br"></iframe>' ;break;
  }
  echo $i;
?>
