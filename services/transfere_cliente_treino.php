<?php
    /**
     * Created by mxaxs.
     * User: mac
     * Date: 09/01/14
     * Time: 00:04
     */
try {

    include('../../../exec_in_joomla.inc');


    $dados_de = $_POST['dados_de'] ;
    $dados_para = $_POST['dados_para'];

    $from = explode(';', $dados_de);
    $to = explode(';', $dados_para);



    $userid      = $from[3];
    $evoid       = $from[4];
    $limbo       = ($to[0]=='0'? 'in':'none');
    $de          = $from[0];
    $para_desc   = $to[1];
    $para        = $to[0];
    $responsavel = 'Estela';
    $tb_pagto   = $from[2];
    $transacaoid = $from[1];


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

    if ($limbo == 'in') {
        //INSERE NO LIMBO O COITADO
        $transferido = 0;
        $query       = "INSERT INTO limbo (userid,evoid,transacaoid,de,para,dta_entrada,dta_saida,responsavel_entrada,responsavel_saida,transferido)
VALUES ('$userid','$evoid','$transacaoid','$de',NULL,'$dta',NULL,'$responsavel',NULL,'$transferido')";

        file_put_contents('query.log',$query);

        $db->setQuery($query);
        $db->Query();

        //ANOTA A TRANSFERENCIA NA TRANSACAO
        $query = "UPDATE $tbl_pagto SET $fld_cnab = REPLACE( $fld_cnab,SUBSTR($fld_cnab,2,5),'00000') WHERE $transacaoid_fld LIKE '$transacaoid'";
        $db->setQuery($query);
        $db->Query();


    } else if ($limbo == 'out') {
        //REMOVE DO LIMBO O COITADO
        $transferido = 1;
        $query       = "UPDATE limbo SET para = '$para',dta_saida = '$dta_saida',responsavel_saida = $$responsavel,transferido='$transferido' WHERE $transacaoid_fld LIKE '$transacaoid'";

        $db->setQuery($query);
        $db->Query();

        //ANOTA A TRANSFERENCIA NA TRANSACAO
        $query = "UPDATE $tbl_pagto SET $fld_cnab = REPLACE( $fld_cnab,SUBSTR($fld_cnab,2,5),'$para') WHERE $transacaoid_fld LIKE '$transacaoid' ";
        $db->setQuery($query);
        $db->Query();


    } else {
        //TRANSFERE IMEDIATAMENTE PARA OUTRO EVENTO
        $query = "UPDATE $tbl_pagto SET $fld_cnab = REPLACE( $fld_cnab,SUBSTR($fld_cnab,2,5),'$para'),$fld_descricao = '$para_desc' WHERE SUBSTR($fld_cnab,2,5) LIKE '$de' AND $transacaoid_fld LIKE '$transacaoid'";
        $db->setQuery($query);
        $db->Query();
    }


    if($db->getAffectedRows()>0) {
        $ret = '<p class="response">SUCESSO! Transferência efetuada.</p>';
    } else {
        $ret = 'OOPS!<br/>'.$msg.'<br/>Nenhum registro afetado: '.$db->getAffectedRows().'<br/><br/><hr/>'.$db->getErrorMsg();
    }

    echo $ret ;

} catch (Exception $e) {
    $ret = $e->getErrorMsg();
    echo $ret ;
}



?>