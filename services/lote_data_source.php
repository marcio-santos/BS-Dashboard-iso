<?php

    //--------------------------------------------------------------------------------
    function template_eval(&$template, &$vars) { return strtr($template, $vars); }
    //================================================================================

    function getPrograma($cnab) {
        $xnab = explode('|',$cnab);
        $prog = $xnab[2];

        if (strpos($prog, ';') !== false) {
            $programa = $prog;
        } else {
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
        }

        return $programa;
    }

    function hasWS($eventoid) {
        $db = &JFactory::getDBO();
        $query = "SELECT * FROM rodadas ORDER BY id DESC LIMIT 1";
        $db->setQuery($query);
        $return = $db->loadRow();
        $eventos = explode(',',$return[5]);
        $ret = (in_array($eventoid, $eventos));
        return $ret;
    }

    $template_element = <<<EOT
<div id="{ID}" class="ex_element {COLOR}">
    <div class="info">
        <div class="evento">{WS}{EVENTO} - {NSA}</div>
        <div class="nome_evo">{NOME_EVO}</div>
        <div class="endereco">{ENDERECO}</div>
    </div>
    <div class="valor_frete">&#36;{VALOR_FRETE}</div>
    <div class="dump" style="display:none">{DUMP}</div>
</div>
EOT;

    include ('../../../exec_in_joomla.inc');
    $db = &JFactory::getDBO();

    $query="SELECT despacho_controle.nsa,transacaoid,cnab AS cnab,idcliente,userid,nome_evo,user_email,endereco_remessa,valor_frete,dta_liberacao,eventoid,'BB' as origem
    FROM boletos_bs INNER JOIN despacho_controle ON eventoid = SUBSTR(cnab,2,5) WHERE ativo = 1 AND compensado = 1 AND ISNULL(enviado)
    UNION ALL SELECT despacho_controle.nsa,TransacaoID,ProdID AS cnab,IdCliente,userid,(SELECT name FROM wow_users WHERE userid = wow_users.id)as nome_evo,
    (SELECT email FROM wow_users WHERE userid = wow_users.id)as user_email,
    CONCAT(CliEndereco,',',CliNumero,',',CliComplemento,' - ',CliBairro,' - ',CliCidade,'/',CliEstado,' CEP:',CliCEP) AS endereco_remessa,
    ValorFrete,dta_liberacao,eventoid,'PS' as origem FROM PagSeguroTransacoes
    INNER JOIN despacho_controle ON eventoid = SUBSTR(ProdID,2,5) WHERE ativo = 1 AND StatusTransacao IN ('Aprovado','Completo') AND LENGTH(Anotacao)= 0
    UNION ALL SELECT despacho_controle.nsa,transacaoid,cnab AS cnab,evoid,userid, (SELECT name FROM wow_users WHERE userid = wow_users.id)as nome_evo,
    (SELECT email FROM wow_users WHERE userid = wow_users.id)as user_email,
    CONCAT( remessa_logradouro ,',',remessa_numero,',',remessa_complemento,' - ',remessa_bairro,' - ',remessa_cidade,'/',remessa_uf,' CEP:',remessa_cep) AS endereco_remessa,
    valor_frete,dta_liberacao,eventoid,'MP' as origem FROM MercadoPagoTransacoes
    INNER JOIN despacho_controle ON eventoid = SUBSTR(cnab,2,5) WHERE ativo = 1 AND status LIKE 'approved' AND ISNULL(status_evo);" ;

    try {
        $db->setQuery($query);
        $result = $db->loadObjectList();
        if(count($result)>0) {
            foreach($result as $obj) {
                $tr = getPrograma($obj->cnab);
                $ws = (hasWS($objt->eventoid))? 'WORKSHOP ':'';
                $element_color = (hasWS($objt->eventoid))? 'orange_element':'green_element';
                $params = array (
                    '{ID}' => $obj->transacaoid,
                    '{NSA}' => $obj->nsa,
                    '{EVENTO}' => $tr,
                    '{NOME_EVO}' => trim($obj->nome_evo),
                    '{ENDERECO}' => $obj->endereco_remessa,
                    '{VALOR_FRETE}' => $obj->valor_frete,
                    '{WS}' => $ws,
                    '{COLOR}' => $element_color,
                    //'{DUMP}' => json_encode(print_r($obj,true))
                    '{DUMP}' =>json_encode((array)$obj)
                );
                $ret .= template_eval($template_element,$params) ;
                //$csv .= $obj->nsa.";".str_replace('&trade;','',$tr).";". utf8_decode($obj->nome_evo).";". utf8_decode($obj->endereco_remessa).";".$obj->valor_frete."\n";
            }


            $dump = json_encode((array)$result);
            file_put_contents('pre-lote.log',$dump);
            $texto = (count($result)==1)? " CLIENTE AGUARDANDO NOVO LOTE" : " CLIENTES AGUARDANDO NOVO LOTE";
            $total = count($result).$texto ;
            $head = "<div id='head' class='head'>$total</div>" ;
            $ret = $head.$ret;
        } else {
            $ret = '<div id="sem_envio" class="ex_element">NENHUM ENVIO NECESSÁRIO</div>';
        }
        //file_put_contents('lista_logistica.csv',$csv);

        echo $ret;

    } catch (Exception $e) {
        $ret = "'<div id='erro' class='ex_element'>$e->getMessage()</div>";
        echo $ret;
    }

?>
