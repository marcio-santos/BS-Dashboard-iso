<?php

    include('../../../exec_in_joomla.inc');

try {


    $eventoid = isset($_POST['eventoid'])? $_POST['eventoid']: 0;
    $db = &JFactory::getDBO();
    $query = "SELECT userid,(SELECT fone_cel FROM wow_users_details WHERE wow_users_details.userid = boletos_bs.userid LIMIT 1) AS fone,nome_evo,user_email,idcliente,IF(boletos_bs.compensado = 1, 'PAGO', 'PENDENTE') as status,transacaoid,'BB' as origem FROM boletos_bs WHERE userid > 0 AND cnab LIKE 'E".$eventoid."%' ORDER BY TRIM(nome_evo) ASC;" ;

    $db->setQuery($query);
    $result_bb = $db->loadObjectList();

    if(!is_array($result_bb)) { $result_bb = array();}

    $query = "SELECT userid,CliTelefone AS fone,(SELECT name FROM wow_users WHERE id = userid)AS nome_evo,CliEmail as user_email,IdCliente AS idcliente,IF(StatusTransacao IN ('Aprovado','Completo'), 'PAGO', 'PENDENTE') as status,TransacaoID as transacaoid,'PS' as origem FROM PagSeguroTransacoes WHERE ProdID LIKE 'E".$eventoid."%' AND StatusTransacao <> 'Cancelado' ORDER BY TRIM(nome_evo) ASC;";
    $db->setQuery($query);
    $result_ps  = $db->loadObjectList();

    if(!is_array($result_ps)) { $result_ps = array();}

    $query = "SELECT userid,pagador_fone AS fone,(SELECT name FROM wow_users WHERE id = userid)AS nome_evo, (SELECT email FROM wow_users WHERE id = userid) as user_email, evoid AS idcliente, (CASE  WHEN status LIKE 'approved' THEN 'PAGO' WHEN status LIKE 'pending' THEN 'PENDENTE' WHEN status LIKE 'cancelled' THEN 'CANCELADO' WHEN status LIKE 'rejected' THEN 'REJEITADO' ELSE 'PENDENTE' END) AS status,transacaoid as transacaoid,'MP' as origem FROM MercadoPagoTransacoes WHERE cnab LIKE 'E".$eventoid."%' ORDER BY TRIM(nome_evo) ASC;";
    $db->setQuery($query);
    $result_mp  = $db->loadObjectList();

    if(!is_array($result_mp)) { $result_mp = array();}


    $query = "SELECT beneficiario,(SELECT fone_cel FROM wow_users_details WHERE wow_users_details.userid = cupons.beneficiario) AS fone,(SELECT name FROM wow_users WHERE wow_users.id = cupons.beneficiario) AS nome_evo,(SELECT name FROM wow_users WHERE wow_users.id = cupons.beneficiario) AS user_email,'11' AS idcliente,IF(utilizado = 1, 'CUPOM', 'CUPOM') as status,transacaoid,'VO' as origem FROM cupons WHERE destino_id LIKE 'E".$eventoid."%' ORDER BY TRIM(nome_evo) ASC;";
    $db->setQuery($query);
    $result_vo  = $db->loadObjectList();

    if(!is_array($result_vo)) { $result_vo = array();}


    $result = array_merge($result_bb,$result_ps,$result_mp,$result_vo) ;




   file_put_contents("merge_$eventoid.log","boleto\n".print_r($result_bb,true)."\nPagseguro\n".print_r($result_ps,true)."\nMercadoP\n".print_r($result_mp,true)."\nVO\n".print_r($result_vo,true)."\n");
    file_put_contents("merge_$eventoid.log","------------------\n".print_r($result,true),FILE_APPEND);

    $line = 'nome;fone;email;pagamento;transacao;forma de pagamento;id site;id evo'."\n" ;



    foreach($result as $obj) {

        $line .= utf8_decode($obj->nome_evo).';'.$obj->fone.';'.$obj->user_email.';'.$obj->status.';'.$obj->transacaoid.';'.$obj->origem.';'.$obj->userid.';'.$obj->idcliente."\n" ;
    }

    file_put_contents('lista_professores.csv',$line);

    $ret = json_encode($result);

    echo $ret;

} catch (Exception $e) {
    echo $e->getMessage();
}

