<?php
/**
 * Created by PhpStorm.
 * User: MárcioAlex
 * Date: 15/04/14
 * Time: 15:05
 */

include('../../../exec_in_joomla.inc');
$db = &JFactory::getDBO();


$files = glob('jsn_output/*.jsn');

echo "<h1>".count($files)."  arquivos</h1>";

/*
echo '<pre>';
print_r($files);
echo '</pre>';
die();
*/

foreach($files as $name) {
    $ano = substr($name,11,4);
    $mes = substr($name,15,2);
    $dia = substr($name,17,2);
    $hora  = substr($name,19,2);
    $min  = substr($name,21,2);

    $lote = $ano.'-'.$mes.'-'.$dia.' | '.$hora.':'.$min ;

    $query = "SELECT des1.nsa, bol.transacaoid, bol.cnab AS cnab, bol.idcliente, bol.userid, bol.nome_evo, bol.user_email, bol.endereco_remessa, bol.valor_frete, des1.dta_liberacao, des1.eventoid,'BB' as origem
                    FROM boletos_bs AS bol INNER JOIN despacho_controle AS des1 ON des1.eventoid = SUBSTR(bol.cnab,2,5) WHERE bol.compensado = 1 AND bol.transacaoid IN (SELECT despacho.transacaoid FROM despacho WHERE lote LIKE '$lote')
                    UNION ALL SELECT des2.nsa, PagS.TransacaoID,PagS.ProdID AS cnab,PagS.IdCliente,PagS.userid,(SELECT name FROM wow_users WHERE userid = wow_users.id)as nome_evo,
                    (SELECT email FROM wow_users WHERE PagS.userid = wow_users.id)as user_email,
                    CONCAT(PagS.CliEndereco,',',PagS.CliNumero,',',PagS.CliComplemento,' - ',PagS.CliBairro,' - ',PagS.CliCidade,'/',PagS.CliEstado,' CEP:',PagS.CliCEP) AS endereco_remessa,
                    PagS.ValorFrete,des2.dta_liberacao,des2.eventoid,'PS' as origem FROM PagSeguroTransacoes AS PagS
                    INNER JOIN despacho_controle AS des2 ON des2.eventoid = SUBSTR(PagS.ProdID,2,5) WHERE PagS.StatusTransacao IN ('Aprovado','Completo')  PagS.TransacaoID IN (SELECT despacho.transacaoid FROM despacho WHERE lote LIKE '$lote')
                    UNION ALL SELECT des3.nsa,mp.transacaoid,mp.cnab AS cnab,mp.evoid,mp.userid, (SELECT name FROM wow_users WHERE mp.userid = wow_users.id)as nome_evo,
                    (SELECT email FROM wow_users WHERE mp.userid = wow_users.id)as user_email,
                    CONCAT( mp.remessa_logradouro ,',',mp.remessa_numero,',',mp.remessa_complemento,' - ',mp.remessa_bairro,' - ',mp.remessa_cidade,'/',mp.remessa_uf,' CEP:',mp.remessa_cep) AS endereco_remessa,
                    mp.valor_frete,des3.dta_liberacao,des3.eventoid,'MP' as origem FROM MercadoPagoTransacoes AS mp
                    INNER JOIN despacho_controle AS des3 ON des3.eventoid = SUBSTR(mp.cnab,2,5) WHERE mp.status LIKE 'approved' AND  mp.transacaoid IN (SELECT despacho.transacaoid FROM despacho WHERE lote LIKE '$lote')";

    $db->setQuery($query);
    $result = $db->loadObjectLIst();
    $dump = json_encode((array)$result);
    $ret = file_put_contents('tmp/'.$name,$dump);

    if($ret===false) { die('erro');}


    echo "<h1>$lote</h1>";
    echo $query;

    $i++;

}
echo "<h1>$i</h1>";