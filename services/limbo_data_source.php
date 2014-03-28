<?php
    include('../../../exec_in_joomla.inc');

    $eventoid = '00000';
    $db = &JFactory::getDBO();
    $query = "SELECT userid,nome_evo,user_email,idcliente,IF(compensado = 1, 'PAGO', 'PENDENTE') as status,transacaoid,'BB' as origem FROM boletos_bs WHERE cnab LIKE '%".$eventoid."%' ORDER BY TRIM(nome_evo) ASC;" ;

    $db->setQuery($query);
    $result_bb = $db->loadObjectList();


    $query = "SELECT userid,(SELECT name FROM wow_users WHERE id = userid)AS nome_evo,CliEmail as user_email,IdCliente AS idcliente,IF(StatusTransacao IN ('Aprovado','Completo'), 'PAGO', 'PENDENTE') as status,TransacaoID as transacaoid,'PS' as origem FROM PagSeguroTransacoes WHERE ProdID LIKE '%".$eventoid."%' AND StatusTransacao <> 'Cancelado' ORDER BY TRIM(nome_evo) ASC;";
    $db->setQuery($query);
    $result_ps  = $db->loadObjectList();

    $query = "SELECT userid,(SELECT name FROM wow_users WHERE id = userid)AS nome_evo, (SELECT email FROM wow_users WHERE id = userid) as user_email, evoid AS idcliente, (CASE  WHEN status LIKE 'approved' THEN 'PAGO' WHEN status LIKE 'pending' THEN 'PENDENTE' WHEN status LIKE 'cancelled' THEN 'CANCELADO' WHEN status LIKE 'rejected' THEN 'REJEITADO' ELSE 'PENDENTE' END) AS status,transacaoid as transacaoid,'MP' as origem FROM MercadoPagoTransacoes WHERE cnab LIKE '%".$eventoid."%' ORDER BY TRIM(nome_evo) ASC;";
    $db->setQuery($query);
    $result_mp  = $db->loadObjectList();



    $result = array_merge($result_bb,$result_ps,$result_mp) ;

    file_put_contents('merge.log',print_r($result,true));

    $ret = json_encode($result);

    echo $ret;


?>