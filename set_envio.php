<?php
include('../../../exec_in_joomla.inc');
$db = & JFactory::getDBO();
$lote = $_POST['lote'];
$enviado = $_POST['enviado'];
$ret = "";


$query = "UPDATE boletos_bs SET enviado =" . $db->Quote($enviado) . " WHERE enviado LIKE " . $db->Quote($lote);


$db->setQuery($query);
$ret = $db->Query();
if ($db->getErrorNum() > 0) {
    file_put_contents('set_envio.log', $db->getErrorMsg() . "\n", FILE_APPEND);
    $ret .= "X";
} else {
    file_put_contents('set_envio.log',$query."\n". $db->getAffectedRows() . "\n\n==================================\n", FILE_APPEND);
}


$query = "UPDATE PagSeguroTransacoes SET Anotacao =" . $db->Quote($enviado) . " WHERE Anotacao LIKE " . $db->Quote($lote);


$db->setQuery($query);
$ret = $db->Query();

if ($db->getErrorNum() > 0) {
    file_put_contents('set_envio.log', $db->getErrorMsg() . "\n", FILE_APPEND);
    $ret .= "X";
} else {
    file_put_contents('set_envio.log',$query."\n". $db->getAffectedRows() . "\n\n==================================\n", FILE_APPEND);
}


$query = "UPDATE MercadoPagoTransacoes SET status_evo =" . $db->Quote($enviado) . " WHERE status_evo LIKE " . $db->Quote($lote);


$db->setQuery($query);
$ret = $db->Query();
if ($db->getErrorNum() > 0) {
    file_put_contents('set_envio.log', $db->getErrorMsg() . "\n", FILE_APPEND);
    $ret .= "X";
} else {
    file_put_contents('set_envio.log',$query."\n". $db->getAffectedRows() . "\n\n==================================\n", FILE_APPEND);
}


$query = "UPDATE despacho SET dta_enviado =" . $db->Quote($enviado) . " WHERE lote LIKE " . $db->Quote($lote);
file_put_contents('query_despacho.log', $query . "\n", FILE_APPEND);
$db->setQuery($query);
$ret = $db->Query();

if ($db->getErrorNum() > 0) {
    file_put_contents('set_envio.log', $db->getErrorMsg() . "\n", FILE_APPEND);
    $ret .= "X";
} else {
    file_put_contents('set_envio.log',$query."\n". $db->getAffectedRows() . "\n\n==================================\n", FILE_APPEND);
}


$result = ($ret == "") ? 'SUCCESS' : 'FAIL';
echo $result;


?>


