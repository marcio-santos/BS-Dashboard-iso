<?php
include('../../exec_in_joomla.inc') ;

echo 'Executando...';

try{
    $db = &JFactory::getDBO();
    $query = "UPDATE wow_users_details SET fone_cel = (SELECT CliTelefone FROM PagSeguroTransacoes WHERE PagSeguroTransacoes.userid = wow_users_details.userid ORDER BY PagSeguroTransacoes.id DESC LIMIT 1)";
    $db->setQuery($query);

    if($db->getErrorNum()== 0) {
        echo "<br/>Arquivos atualizados: ".$db->getAffectedRows();
        echo "<br/>Operação finalizada.";
    } else {
        echo $db->getErrorMsg();
    }



} catch(Exception $e){
    echo $e->getMessage();
}
