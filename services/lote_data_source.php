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

    $query="SELECT des1.nsa, bol.transacaoid, bol.cnab AS cnab, bol.idcliente, bol.userid, bol.nome_evo, bol.user_email, bol.endereco_remessa, bol.valor_frete, des1.dta_liberacao, des1.eventoid,'BB' as origem
    FROM boletos_bs AS bol INNER JOIN despacho_controle AS des1 ON des1.eventoid = SUBSTR(bol.cnab,2,5) WHERE des1.ativo = 1 AND bol.compensado = 1 AND ISNULL(bol.enviado)
    UNION ALL SELECT des2.nsa, PagS.TransacaoID,PagS.ProdID AS cnab,PagS.IdCliente,PagS.userid,(SELECT name FROM wow_users WHERE userid = wow_users.id)as nome_evo,
    (SELECT email FROM wow_users WHERE PagS.userid = wow_users.id)as user_email,
    CONCAT(PagS.CliEndereco,',',PagS.CliNumero,',',PagS.CliComplemento,' - ',PagS.CliBairro,' - ',PagS.CliCidade,'/',PagS.CliEstado,' CEP:',PagS.CliCEP) AS endereco_remessa,
    PagS.ValorFrete,des2.dta_liberacao,des2.eventoid,'PS' as origem FROM PagSeguroTransacoes AS PagS
    INNER JOIN despacho_controle AS des2 ON des2.eventoid = SUBSTR(PagS.ProdID,2,5) WHERE des2.ativo = 1 AND PagS.StatusTransacao IN ('Aprovado','Completo') AND LENGTH(PagS.Anotacao)= 0
		UNION ALL SELECT des3.nsa,mp.transacaoid,mp.cnab AS cnab,mp.evoid,mp.userid, (SELECT name FROM wow_users WHERE mp.userid = wow_users.id)as nome_evo,
    (SELECT email FROM wow_users WHERE mp.userid = wow_users.id)as user_email,
    CONCAT( mp.remessa_logradouro ,',',mp.remessa_numero,',',mp.remessa_complemento,' - ',mp.remessa_bairro,' - ',mp.remessa_cidade,'/',mp.remessa_uf,' CEP:',mp.remessa_cep) AS endereco_remessa,
    mp.valor_frete,des3.dta_liberacao,des3.eventoid,'MP' as origem FROM MercadoPagoTransacoes AS mp
    INNER JOIN despacho_controle AS des3 ON des3.eventoid = SUBSTR(mp.cnab,2,5) WHERE des3.ativo = 1 AND mp.status LIKE 'approved' AND ISNULL(mp.status_evo);" ;

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
