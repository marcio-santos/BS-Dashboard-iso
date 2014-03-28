<?php

    include('../../../exec_in_joomla.inc');
    $eventoid = $_REQUEST['eventoid'] ;
    $task = $_REQUEST['task'];
    $ativo = $_REQUEST['ativo'];
    $nsa = $_REQUEST['nsa'];

    try{
        $db = &JFactory::getDBO();
        $dta_alteracao = date('Y-m-d H:i:s');

        switch($task) {
            Case 'get' :
                $query = "SELECT * FROM despacho_controle WHERE eventoid LIKE ".$db->Quote($eventoid);
                break;
            Case 'set' :
                $dta_liberacao = date('Y-m-d H:i:s');
                $query = "INSERT INTO despacho_controle VALUES ('','$eventoid','$dta_liberacao','$dta_liberacao','ENVIO DO PRIMEIRO LOTE',1,'$nsa')";
                break;
            Case 'update' :
                $query = "UPDATE despacho_controle SET dta_alteracao = '$dta_alteracao',alteracao = 'ATUALIZACAO DO LOTE',ativo = $ativo WHERE eventoid LIKE ".$db->Quote($eventoid);
                break;

        }
        if ($task=='get'){
            $db->setQuery($query);
            $result = $db->loadObjectList();
            echo json_encode($result[0]);
        } else {
            $db->setQuery($query);
            $result = $db->Query();
            if($db->getErrorNum()>0) {
                $ret = array(0,$db->getErrorMsg());
            } else {
                if($db->getAffectedRows()>0) {
                    $ret = array(1,'Atualização efetuada');
                } else {
                    $ret = array(0,'Evento não encontrado');
                }
            }

            echo json_encode($ret) ;
        }

    } catch(Expection $e) {
        $ret = array(0,$e->getMessage());
        echo json_encode($ret);
    }


?>
