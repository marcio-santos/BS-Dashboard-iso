<?php
  include('../../../exec_in_joomla.inc');
  
  $eventoid = $_REQUEST['eventoid'];
  $db = &JFactory::getDBO();
  $query = "SELECT (SELECT name FROM wow_users WHERE agenda_groundworks.userid = id) AS nome, aula,email,`data` FROM `agenda_groundworks` WHERE eventoid LIKE '$eventoid' ORDER BY aula,TRIM(nome) ASC;" ;
  
  $db->setQuery($query);
  $result = $db->loadObjectList();
  $ret = json_encode($result);
  file_put_contents('groundw.log',$eventoid."\n".$ret);
  echo $ret;
  
  
?>
