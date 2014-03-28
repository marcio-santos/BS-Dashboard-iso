<?php
$id = $_GET['id'];
include('../../exec_in_joomla.inc');
try{
    
         $db = &JFactory::getDBO();
         $query = "SELECT count(id) FROM boletos_bs WHERE compensado=1 AND SUBSTR(cnab,2,5) LIKE ".$id ;
         $db->setQuery($query);
         $resultBB = $db->loadResult();
         
         $query = "SELECT count(id) FROM PagSeguroTransacoes WHERE StatusTransacao IN ('Aprovado','Completo') AND SUBSTR(ProdID,2,5) LIKE ".$id;
         $db->setQuery($query);
         $resultPS = $db->loadResult();
         
         $query = "SELECT count(id) FROM MercadoPagoTransacoes WHERE status LIKE 'approved' AND SUBSTR(cnab,2,5) LIKE ".$id ;
         $db->setQuery($query);
         $resultMP = $db->loadResult();
         
          $total = $resultBB + $resultPS + $resultMP ;
       
         if($db->ErrorNum()>0) {
             echo $db->ErrorMsg();
         }  else {
             echo "<h1>$id</h1>";
              echo "ESTE é o resultado: ". $total;
         }
        
} catch(Exception $e) {
    echo $e->getMessage();
}  
    
?>
