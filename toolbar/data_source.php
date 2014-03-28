<?php
  include('../../../exec_in_joomla.inc');
  
  $eventoid = isset($_POST['eventoid'])? $_POST['eventoid']: 18850;
  $db = &JFactory::getDBO();
  $query = "SELECT nome_evo,user_email,idcliente,IF(compensado = 1, 'PAGO', 'PENDENTE') as status,transacaoid FROM boletos_bs WHERE cnab LIKE '%".$eventoid."%' ORDER BY TRIM(nome_evo) ASC;" ;
  
  $db->setQuery($query);
  $result = $db->loadObjectList();
  $ret = json_encode($result);
 
  echo $ret;
  
  
?>
