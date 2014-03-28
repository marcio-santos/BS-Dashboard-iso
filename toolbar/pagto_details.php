<?php
  include('../../../exec_in_joomla.inc');
  try{
  $transacaoid = $_POST['transacaoid'];
  $origem = $_POST['origem'];

  $db = &JFactory::getDBO();
  switch($origem) {
      Case 'BB':
            $ds_origem = 'BOLETO BANCÁRIO';
            $query = "SELECT * FROM boletos_bs WHERE transacaoid LIKE ".$db->Quote($transacaoid);
            break;
      Case 'PS':
            $ds_origem = 'PAGSEGURO';
            $query = "SELECT * FROM PagSeguroTransacoes WHERE TransacaoID LIKE ".$db->Quote($transacaoid);
            break;
      Case 'MP':
            $ds_origem = 'MERCADO PAGO';
            $query = "SELECT * FROM MercadoPagoTransacoes WHERE transacaoid LIKE ".$db->Quote($transacaoid);
            break;
  }

  $db->setQuery($query);
  $result = $db->loadObjectList();
    switch($origem) {
        Case 'BB':
            $enviado = $result[0]->enviado;
            break;

        Case 'PS':
            $enviado = $result[0]->Anotacao;
            break;

        Case 'MP':
            $enviado = $result[0]->status_evo;
            break;
    }
     $enviado = (strlen($enviado)==0)? 'NÃO ENVIADO' : $enviado;
  $ret = "<div style='font-size:18px;'>$ds_origem&nbsp;&nbsp;- Enviado:$enviado</div><hr/><table>" ;
  foreach($result[0] as $key=>$value){

      $ret .= "<strong>$key: </strong>$value<br/>";
  }
  $ret .= "</table>" ;
  echo $ret;
   } catch (Exception $e) {
      echo $e->getMessage();
  }
?>
