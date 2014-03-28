<?php
include('../../../exec_in_joomla.inc');
$eventoid = $_REQUEST['eventoid'];
$professor = $_REQUEST['professor'];
$valor = $_REQUEST['valor'];
$avisado = $_REQUEST['avisado'];
$task = $_REQUEST['task'];
$hoje = date('Y-m-d');
file_put_contents('escala.log',print_r($_REQUEST,true),FILE_APPEND);
try {
    $db = & JFactory::getDBO();
    if ($task == 1) { //UPDATE
        //$query = "REPLACE INTO escala_treinamento (id,id_evento,ds_evento,treinador,valor_aula,aviso,dt_aviso) VALUES ('','$eventoid','','$professor','$valor',$avisado','$hoje')";
        $query = "INSERT INTO escala_treinamento (id,id_evento,ds_evento,treinador,valor_aula,aviso,dt_aviso) VALUES ('','$eventoid','','$professor','$valor','$avisado','$hoje')
         ON DUPLICATE KEY UPDATE treinador = '$professor',valor_aula = $valor, aviso = $avisado,dt_aviso = '$hoje'";
        $db->setQuery($query);
        $db->Query();
        if ($db->getErrorNum() > 0) {
            $ret = array(1, $db->getErrorMsg());
        } else {
            if ($db->getAffectedRows() > 0) {
                $ret = array(0, 'Sucesso!');
            } else {
                $ret = array(1, 'Não executada a tarefa!');
            }
        }
    } else {
        $query = "SELECT * FROM escala_treinamento WHERE id_evento = $eventoid";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        if ($db->getErrorNum() > 0) {
            $ret = array(1, $db->getErrorMsg());
        } else {
            if (count($result) > 0) {
                $ret = array(0, $result[0]->treinador,$result[0]->valor_aula, $result[0]->aviso);
            } else {
                $ret = array(1, '');
            }
        }
    }
    echo json_encode($ret);
} catch (Exception $e) {
    echo $e->getMessage();
}
