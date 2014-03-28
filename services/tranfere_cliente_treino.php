<?php
    /**
     * Created by mxaxs.
     * User: mac
     * Date: 09/01/14
     * Time: 00:04
     */
//try {

    include('../../exec_in_joomla.inc');


    $dados_de = $_POST['dados_de'] ;
    $dados_para = $_POST['dados_para'];

    $de = explode(';', $dados_de);
    $para = explode(';',$dados_para);

    file_put_contents('para_de.log',$de);

    $ret = json_encode(array(0,$dados_de));


    echo $ret ;


    /*

    $userid      = $_POST['userid'];
    $evoid       = $_POST['evoid'];
    $limbo       = $_POST['limbo'];
    $de          = $_POST['de'];
    $para_desc   = $_POST['para_desc'];
    $para        = $_POST['para'];
    $responsavel = $_POST['responsavel'];
    $tbl_pagto   = $_POST['tbl_pagto'];
    $transacaoid = $_POST['transacaoid'];





    $dta = Date('Y-m-d H:i:s');

    switch ($tb_pagto) {
        Case 'BB':
            $fld_cnab        = 'cnab';
            $transacaoid_fld = 'transacaoid';
            break;
        Case 'PS':
            $fld_cnab        = 'ProdID';
            $transacaoid_fld = 'TransacaoID';
            break;
        Case 'MP':
            $fld_cnab        = 'cnab';
            $transacaoid_fld = 'transacaoid';
            break;
        Case 'CC':
            $fld_cnab        = 'cnab';
            $transacaoid_fld = 'transacaoid';
            break;
    }

    $db = & JFactory::getDBO();

    if ($limbo == 'in') {
        //INSERE NO LIMBO O COITADO
        $transferido = 0;
        $query       = "INSERT INTO limbo (id,userid,evoid,transacaoid,de,para,dta_entrada,dta_saida,responsavel_entrada,responsavel_saida,transferido)
VALUES (null,'$userid','$evoid','$transacaoid','$de',NULL,'$dta',NULL,'$responsavel',NULL,'$transferido')";
        $db->setQuery($query);
        $db->Query();

        //ANOTA A TRANSFERENCIA NA TRANSACAO
        $query = "UPDATE $tbl_pagto SET $cnab_fld = REPLACE( $cnab_fld,SUBSTR($cnab_fld,2,5),'00000') WHERE $transacaoid_fld = $transacaoid";
        $db->setQuery($query);
        $db->Query();


    } else if ($limbo == 'out') {
        //REMOVE DO LIMBO O COITADO
        $transferido = 1;
        $query       = "UPDATE limbo SET para = '$para',dta_saida = '$dta_saida',responsavel_saida = $$responsavel,transferido='$transferido' WHERE $transacaoid_fld = $transacaoid";

        $db->setQuery($query);
        $db->Query();

        //ANOTA A TRANSFERENCIA NA TRANSACAO
        $query = "UPDATE $tbl_pagto SET $cnab_fld = REPLACE( $cnab_fld,SUBSTR($cnab_fld,2,5),'$para') WHERE $transacaoid_fld = $transacaoid";
        $db->setQuery($query);
        $db->Query();


    } else {
        //TRANSFERE IMEDIATAMENTE PARA OUTRO EVENTO
        $query = "UPDATE $tbl_pagto SET $$fld_cnab = $para,$fld_descricao = $$para_desc WHERE SUBSTR($$fld_cnab,2,5) LIKE $$de AND $transacaoid_fld = '$transacaoid'";
        $db->setQuery($query);
        $db->Query();
    }

    $ret = 'SUCCESS';

} catch (Exception $e) {
    $ret = $e->getErrorMsg();
}

    echo $ret;
*/

?>