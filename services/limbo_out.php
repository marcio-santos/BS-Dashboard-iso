<?php
    /**
     * Created by mxaxs.
     * User: mac
     * Date: 09/01/14
     * Time: 00:04
     */
try {

    include('../../../exec_in_joomla.inc');


    $dados_from = $_POST['dados_who'] ;
    $dados_to = $_POST['dados_to'];

    $from = explode(';', $dados_from);
    $to = explode(';', $dados_to);



    $userid      = $from[2];
    $evoid       = $from[3];
    $para_desc   = $to[1];
    $para        = $to[0];
    $responsavel = 'Estela';
    $tb_pagto   = $from[1];
    $transacaoid = $from[0];


    $dta = Date('Y-m-d H:i:s');

    switch ($tb_pagto) {
        Case 'BB':
            $fld_cnab        = 'cnab';
            $transacaoid_fld = 'transacaoid';
            $tbl_pagto       = 'boletos_bs';
            $fld_descricao   = 'evento';
            break;

        Case 'PS':
            $fld_cnab        = 'ProdID';
            $transacaoid_fld = 'TransacaoID';
            $tbl_pagto       = 'PagSeguroTransacoes';
            $fld_descricao   = 'ProdDescricao';
            break;

        Case 'MP':
            $fld_cnab        = 'cnab';
            $transacaoid_fld = 'transacaoid';
            $tbl_pagto       = 'MercadoPagoTransacoes';
            $fld_descricao   = 'produto_descricao';
            break;

        Case 'CC':
            $fld_cnab        = 'cnab';
            $transacaoid_fld = 'transacaoid';
            $tbl_pagto       = 'ConveniosCortesias';
            $fld_descricao   = 'evento';
            break;

    }

    $db = & JFactory::getDBO();


        //REMOVE DO LIMBO O COITADO
        $transferido = 1;
        $query       = "UPDATE limbo SET para = '$para',dta_saida = '$dta_saida',responsavel_saida = '$responsavel,transferido='$transferido' WHERE $transacaoid_fld LIKE '$transacaoid'";

        $db->setQuery($query);
        $db->Query();

        //ANOTA A TRANSFERENCIA NA TRANSACAO
        $query = "UPDATE $tbl_pagto SET $fld_cnab = REPLACE( $fld_cnab,SUBSTR($fld_cnab,2,5),'$para') WHERE $transacaoid_fld LIKE '$transacaoid' ";


        $db->setQuery($query);
        $db->Query();



    if($db->getAffectedRows()>0) {
        $ret = '<p class="response">SUCESSO! Transferência efetuada.</p><br/><img src="img/check_sucess.png" style="display: block;margin-left:auto;margin-right:auto;" />';
    } else {
        $ret = 'OOPS!<br/>'.$msg.'<br/>Nenhum registro afetado: '.$db->getAffectedRows().'<br/><br/><hr/>'.$db->getErrorMsg();
    }

    echo $ret ;

} catch (Exception $e) {
    $ret = $e->getErrorMsg();
    echo $ret ;
}



?>