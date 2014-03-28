<?php
    include('../../../exec_in_joomla.inc');

    function getPrograma($cnab) {
        $xnab = explode('|',$cnab);
        $prog = $xnab[2];
        /*
        switch($prog) {
        Case 'BA': $programa = 'BODYATTACK&trade;';break;
        Case 'BB': $programa = 'BODYBALANCE&trade;';break;
        Case 'BC': $programa = 'BODYCOMBAT&trade;';break;
        Case 'BS': $programa = 'BODYSTEP&trade;';break;
        Case 'BV': $programa = 'BODYVIVE&trade;';break;
        Case 'BP': $programa = 'BODYPUMP&trade;';break;
        Case 'BJ': $programa = 'BODYJAM&trade;';break;
        Case 'RPM': $programa = 'RPM&trade;';break;
        Case 'SB': $programa = 'SHBAM&trade;';break;
        Case 'CX': $programa = 'CXWORX&trade;';break;
        Case 'PJ': $programa = 'POWERJUMP';break;
        Default: $programa = $prog;
        }
        return $programa;
        */
        return $prog;
    }

    function getRodada() {
        $rodada = '';
        return $rodada;
    }

    function setEnvio($transacaoid,$origem,$lote) {
        $db = &JFactory::getDBO();
        switch($origem) {
            Case 'BB':
                $query = "UPDATE boletos_bs SET enviado =".$db->Quote($lote)." WHERE transacaoid LIKE ".$db->Quote($transacaoid);
                break;
            Case 'PS':
                $query = "UPDATE PagSeguroTransacoes SET Anotacao =".$db->Quote($lote)." WHERE transacaoid LIKE ".$db->Quote($transacaoid);
                break;
            Case 'MP':
            $query = "UPDATE MercadoPagoTransacoes SET status_evo =".$db->Quote($lote)." WHERE TransacaoID LIKE ".$db->Quote($transacaoid);
            break;
        }

        try {
            $db->setQuery($query);
            $res = $db->Query();
            if($res) {
                $affectedRows = $db->getAffectedRows($res);
                if($affectedRows == 0) {
                    $ret = 1 ;
                } else {
                    $ret = 0 ;
                }

            } else {
                $ret = 2;
            }

        } catch(Exception $e) {
            $ret = 3;
        }

    }


    $db = &JFactory::getDBO();

    $prelote = file_get_contents('pre-lote.log');
    $lote = json_decode($prelote);

    foreach($lote as $obj) {
        $cnab = $obj->cnab;
        $dta_liberado = date('Y-m-d H:i:s');
        $dta_enviado = $dta_liberado;
        $trackcode = '';
        $programas = getPrograma($cnab) ;
        $rodada = getRodada($programas);
        $tipo_despacho = 'SD';
        $lote = date('Y-m-d | H:i');


        $query  = "INSERT INTO despacho
        (userid,evoid,eventoid,origem,transacaoid,nome_evo,dta_liberado,dta_enviado,trackcode,envio_cancelado,
        motivo_cancelado,rodada,programas,comentario,observacao,tipo_despacho,lote)
        VALUES ('$obj->userid','$obj->idcliente','$obj->eventoid','$obj->origem','$obj->transacaoid','$obj->nome_evo',
        '$dta_liberado','$dta_enviado','$trackcode',0,'','$rodada','$programas','','','$tipo_despacho','$lote')
        ON DUPLICATE KEY UPDATE eventoid = '$obj->eventoid';" ;


        $db->setQuery($query);
        $db->Query();


        if($db->getErrorNum()>0) {
            $msg .= "PROBLEMAS COM O LOTE: ".$db->getErrorMsg()."<br/>";
            $msg_log = "[".Date('Y-m-d H:i:s')."] | TransacaoID:".$obj->transacaoid." | origem:".$obj->origem." | UserID:".$obj->userid." | lote:".$lote." | ERRO:".$db->getErrorMsg();
            file_put_contents('erros_envio.log',$msg_log."\n",FILE_APPEND);
        } else {
            $counter += $db->getAffectedRows();
            $ret = setEnvio($obj->transacaoid,$obj->origem,$lote);
            if($ret > 0) {
                 $msg_log = "[".Date('Y-m-d H:i:s')."] | TransacaoID:".$obj->transacaoid." | origem:".$obj->origem." | UserID:".$obj->userid." | lote:".$lote." | ERRO:$ret - ARQUIVO NÃO ATUALIZADO NA ORIGEM";
                 file_put_contents('erros_envio.log',$msg_log."\n",FILE_APPEND);
                  $msg .= "PROBLEMAS COM O LOTE: ".$msg_log."<br/>";
            }
        }

    }

    if(strlen($msg)==0) {
        $novo_lote = str_replace(':','',str_replace('-','',str_replace(' | ','',$lote))).'.jsn';
        $msg .= "LOTE CRIADO COM SUCESSO!\nInseridos $counter registros" ;
        rename('pre-lote.log',$novo_lote);
    }
    echo $msg;

?>
