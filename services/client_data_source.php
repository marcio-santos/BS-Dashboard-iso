<?php
    include('../../../exec_in_joomla.inc');

    $eventoid = isset($_POST['eventoid'])? $_POST['eventoid']: 18850;
    $db = &JFactory::getDBO();
    $query = "SELECT userid,(SELECT fone_cel FROM wow_users_details WHERE wow_users_details.userid = boletos_bs.userid) AS fone,nome_evo,user_email,idcliente,IF(compensado = 1, 'PAGO', 'PENDENTE') as status,transacaoid,'BB' as origem FROM boletos_bs WHERE cnab LIKE '%".$eventoid."%' ORDER BY TRIM(nome_evo) ASC;" ;

    $db->setQuery($query);
    $result_bb = $db->loadObjectList();


    $query = "SELECT userid,CliTelefone AS fone,(SELECT name FROM wow_users WHERE id = userid)AS nome_evo,CliEmail as user_email,IdCliente AS idcliente,IF(StatusTransacao IN ('Aprovado','Completo'), 'PAGO', 'PENDENTE') as status,TransacaoID as transacaoid,'PS' as origem FROM PagSeguroTransacoes WHERE ProdID LIKE '%".$eventoid."%' AND StatusTransacao <> 'Cancelado' ORDER BY TRIM(nome_evo) ASC;";
    $db->setQuery($query);
    $result_ps  = $db->loadObjectList();

    $query = "SELECT userid,pagador_fone AS fone,(SELECT name FROM wow_users WHERE id = userid)AS nome_evo, (SELECT email FROM wow_users WHERE id = userid) as user_email, evoid AS idcliente, (CASE  WHEN status LIKE 'approved' THEN 'PAGO' WHEN status LIKE 'pending' THEN 'PENDENTE' WHEN status LIKE 'cancelled' THEN 'CANCELADO' WHEN status LIKE 'rejected' THEN 'REJEITADO' ELSE 'PENDENTE' END) AS status,transacaoid as transacaoid,'MP' as origem FROM MercadoPagoTransacoes WHERE cnab LIKE '%".$eventoid."%' ORDER BY TRIM(nome_evo) ASC;";
    $db->setQuery($query);
    $result_mp  = $db->loadObjectList();


    $query = "SELECT beneficiario,(SELECT fone_cel FROM wow_users_details WHERE wow_users_details.userid = cupons.beneficiario) AS fone,(SELECT name FROM wow_users WHERE wow_users.id = cupons.beneficiario) AS nome_evo,(SELECT name FROM wow_users WHERE wow_users.id = cupons.beneficiario) AS user_email,'11' AS idcliente,IF(utilizado = 1, 'CUPOM', 'CUPOM') as status,transacaoid,'VO' as origem FROM cupons WHERE destino_id LIKE '%".$eventoid."%' ORDER BY TRIM(nome_evo) ASC;";
    $db->setQuery($query);
    $result_vo  = $db->loadObjectList();


    $result = array_merge($result_bb,$result_ps,$result_mp,$result_vo) ;

    //file_put_contents("merge_$eventoid.log",print_r($result,true));
    $line = 'nome;fone;email;pagamento;transacao;forma de pagamento;id site;id evo'."\n" ;
    foreach($result as $obj) {

        $line .= utf8_decode($obj->nome_evo).';'.$obj->fone.';'.$obj->user_email.';'.$obj->status.';'.$obj->transacaoid.';'.$obj->origem.';'.$obj->userid.';'.$obj->idcliente."\n" ;
    }

    file_put_contents('lista_professores.csv',$line);

    $ret = json_encode($result);

    echo $ret;


?>