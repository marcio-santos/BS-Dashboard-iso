<?php

    include ('../../../exec_in_joomla.inc');
    $db = &JFactory::getDBO();

    $query="SELECT transacaoid,idcliente,userid,nome_evo,user_email,endereco_remessa,valor_frete,dta_liberacao,eventoid,'BB' as origem 
    FROM boletos_bs INNER JOIN despacho_controle ON eventoid = SUBSTR(cnab,2,5) WHERE ativo = 1 
    UNION ALL SELECT TransacaoID,IdCliente,userid,(SELECT name FROM wow_users WHERE userid = wow_users.id)as nome_evo, 
    (SELECT email FROM wow_users WHERE userid = wow_users.id)as user_email,
    CONCAT(CliEndereco,',',CliNumero,',',CliComplemento,' - ',CliBairro,' - ',CliCidade,'/',CliEstado,' CEP:',CliCEP) AS endereco_remessa,
    ValorFrete,dta_liberacao,eventoid,'PS' as origem FROM PagSeguroTransacoes 
    INNER JOIN despacho_controle ON eventoid = SUBSTR(ProdID,2,5) WHERE ativo = 1 
    UNION ALL SELECT transacaoid,evoid,userid, (SELECT name FROM wow_users WHERE userid = wow_users.id)as nome_evo, 
    (SELECT email FROM wow_users WHERE userid = wow_users.id)as user_email,
    CONCAT( remessa_logradouro ,',',remessa_numero,',',remessa_complemento,' - ',remessa_bairro,' - ',remessa_cidade,'/',remessa_uf,' CEP:',remessa_cep) AS endereco_remessa,
    valor_frete,dta_liberacao,eventoid,'MP' as origem FROM MercadoPagoTransacoes 
    INNER JOIN despacho_controle ON eventoid = SUBSTR(cnab,2,5) WHERE ativo = 1 ;" ;

    try {
        $db->setQuery($query);
        $result = $db->loadObjectList();
        if(count($result)>0) {
                $ret = $result;
        } else {
            $ret = array(0,'NENHUM ENVIO NECESSÁRIO');
        }
        echo '<pre>';
        echo json_encode($ret);
        //print_r($result);
        echo '</pre>';
        
    } catch (Exception $e) {
        $ret = array(0,$e->getMessage());
        echo json_encode($ret);
    }

?>
