<?php

    include('../../../exec_in_joomla.inc');
    
    //--------------------------------------------------------------------------------
    function template_eval(&$template, &$vars) { return strtr($template, $vars); }
    //================================================================================

    function get_nsa($ref) {
        switch($ref) {
            Case 1: $sigla = 'ARA';break;
            Case 2: $sigla = 'BAL';break;
            Case 3: $sigla = 'BAU';break;
            Case 4: $sigla = 'BEL';break;
            Case 5: $sigla = 'BLM';break;
            Case 6: $sigla = 'BRA';break;
            Case 7: $sigla = 'CAM';break;
            Case 8: $sigla = 'CAS';break;
            Case 9: $sigla = 'CGR';break;
            Case 10: $sigla = 'CHA';break;
            Case 11: $sigla = 'CUI';break;
            Case 12: $sigla = 'CUR';break;
            Case 13: $sigla = 'FOR';break;
            Case 14: $sigla = 'GOI';break;
            Case 15: $sigla = 'JPS';break;
            Case 16: $sigla = 'LON';break;
            Case 17: $sigla = 'MAC';break;
            Case 18: $sigla = 'MAN';break;
            Case 19: $sigla = 'NAT';break;
            Case 20: $sigla = 'NIT';break;
            Case 21: $sigla = 'POA';break;
            Case 22: $sigla = 'PVE';break;
            Case 23: $sigla = 'REC';break;
            Case 24: $sigla = 'RIB';break;
            Case 25: $sigla = 'RIO';break;
            Case 26: $sigla = 'SAL';break;
            Case 27: $sigla = 'SAN';break;
            Case 28: $sigla = 'SAO';break;
            Case 29: $sigla = 'SJC';break;
            Case 30: $sigla = 'SJP';break;
            Case 31: $sigla = 'SLM';break;
            Case 32: $sigla = 'SOR';break;
            Case 33: $sigla = 'TER';break;
            Case 34: $sigla = 'UBE';break;
            Case 35: $sigla = 'VIT';break;
            default: $sigla = $ref;
        }
        return $sigla;

    }
    
    function getCorum($id) {
         $db = &JFactory::getDBO();
         $query = "SELECT count(id) FROM boletos_bs WHERE compensado=1 AND SUBSTR(cnab,2,5) LIKE ".$id ;
         $db->setQuery($query);
         $resultBB = $db->loadResult();
         
         $query = "SELECT count(id) FROM PagSeguroTransacoes WHERE StatusTransacao IN ('Aprovado','Completo') AND SUBSTR(ProdID,2,5) LIKE ".$id;
         $db->setQuery($query);
         $resultPS = $db->loadResult();
         
         $query = "SELECT count(id) FROM MercadoPagoTransacoes WHERE status LIKE 'approved' AND SUBSTR(cnab,2,5) LIKE ".$id ;
         $db->setQuery($query);
         $resultMP = $db->loadResult();
         
          $total = $resultBB + $resultPS + $resultMP ;
       
        
        return $total;
    }
    
    $container = file_get_contents('../pages/template_main2.html');

    $element_blue = <<<EOT
  <div class="element other nonmetal isotope-item" data-symbol="{NSA}" data-category="other" data-nsa="{NSA}" data-color="orange" data-evento="{DATA}">
      <p class="number" id="numero">{NUMBER}</p>
      <h3 class="symbol"><span class="fb" id="nsa" data-cidade="{CIDADE}">{NSA}</span></h3>
      <h2 class="name" id="cidade">{CIDADE}</h2>
      <p class="corum" id="corum">{CORUM}</p>
      <p class="weight" id="programa">{PROGRAMA}</p>
      <p style="display:none;" name="endereco" >{ENDERECO}</p>
      <p style="display:none;" name="local">{LOCAL}</p>
      <p style="display:none;" name="nome_evento">{NOME_EVENTO}</p>
  </div>
EOT;

    $element_red = <<<EOT
<div id="{ID}" class="element alkali metal isotope-item" data-symbol="{NSA}" data-category="alkali" data-nsa="{NSA}" data-color="red" data-evento="{DATA}">
     <p class="number" id="numero">{NUMBER}</p>
      <h3 class="symbol"><span class="fb" id="nsa" data-cidade="{CIDADE}">{NSA}</span></h3>
      <h2 class="name" id="cidade">{CIDADE}</h2>
      <p class="corum" id="corum">{CORUM}</p>
      <p class="weight" id="programa">{PROGRAMA}</p>
      <p style="display:none;" name="endereco" >{ENDERECO}</p>
      <p style="display:none;" name="local">{LOCAL}</p>
      <p style="display:none;" name="nome_evento">{NOME_EVENTO}</p>
    </div>
EOT;

    $element_green = <<<EOT
<div id="{ID}" class="element feature actinoid width2 height2 isotope-item" data-symbol="{NSA}" data-category="alkali" data-nsa="{NSA}" data-color="green" data-evento="{DATA}">
      <p class="number" id="numero">{NUMBER}</p>
      <h3 class="symbol"><span class="fb" id="nsa" data-cidade="{CIDADE}">{NSA}</span></h3>
      <h2 class="name" id="cidade">{CIDADE}</h2>
      <p class="corum" id="corum">{CORUM}</p>
      <p class="weight" id="programa">{PROGRAMA}</p>
      <p style="display:none;" name="endereco" >{ENDERECO}</p>
      <p style="display:none;" name="local">{LOCAL}</p>
      <p style="display:none;" name="nome_evento">{NOME_EVENTO}</p>
      <img src="img/wait_dispatch.gif" class="wait_dispatch" /></p>
    </div>
EOT;

    $inicio = '20131219';
    $fim = '20140201' ;
    $programa = '' ;
    $estado = '';

    $tipo_treino = 1;

    $client = new
    SoapClient(
        "http://177.154.134.90:8084/WCF/_BS/wcfBS.svc?wsdl",array('cache_wsdl'=>WSDL_CACHE_NONE)
    );
    $params = array('IdClienteW12'=>229, 'IdTipoTreinamento'=>$tipo_treino, 'Inicio'=>$inicio, 'Fim'=>$fim, 'Estado'=>$estado, 'Programa'=>$programa);
    $webService = $client->ListarTreinamentosWebsite($params);
    $wsResult = $webService->ListarTreinamentosWebsiteResult->VOBS;


    foreach($wsResult as $obj) {
        $i++;
        $dd = date('d/m',strtotime($obj->INICIO));
        $mes = date('m',strtotime($obj->INICIO));
        $data = date('d-m-Y',strtotime($obj->INICIO));
        $nsa = get_nsa($obj->ID_NSA);
        $id = $obj->ID_TREINAMENTO;
        $end =  $obj->ENDERECO;

        if ($tipo_treino == 7) {
                $chunk1 = strpos($end,':')+1;
                $chunk2 = strpos($end,'-');
                 $local = trim(substr($end,$chunk1,$chunk2 - $chunk1));
                 $endereco = trim(substr($end,strpos($end,'-')+1,strlen($end)));
                 $nome_evento = trim(substr($end,0,$chunk1-1));
        } else {
                $chunk1 = strpos($end,'-');


                 $local = trim(substr($end,0,$chunk1));
                 $endereco = trim(substr($end,$chunk1+1,strlen($end)-$chunk1));
                 $nome_evento = $obj->DS_SERVICO;
        }

        $len = strlen($end);
         $corum = getCorum($id);
         $corum = ($corum > 1)? $corum ." inscritos" : $corum ." inscrito" ;
        $vars = array(
            '{ID}' => $i,
            '{NUMBER}' => $dd,
            '{NSA}' => $nsa,
            '{CIDADE}' => utf8_decode($obj->CIDADE),
            '{DATA}' => $data,
            '{PROGRAMA}' => $id, //date('d-m-Y',strtotime($obj->INICIO))
            '{LOCAL}' =>  utf8_decode($local),
            '{NOME_EVENTO}' =>  utf8_decode($nome_evento),
            '{ENDERECO}' => utf8_decode($endereco),
            '{CORUM}' =>$corum
        ) ;

       
        
      
        if($corum >=4 ) {
                $tpl = template_eval($element_green,$vars);
                $color = 'green';
        } else if($corum < 4 && $corum >= 2) {
                $tpl = template_eval($element_blue,$vars);
                $color = 'orange';
        }  else {
                $tpl = template_eval($element_red,$vars);
                $color = 'red';
        } 

        $itens .= "<li data-nsa='$nsa' data-data='$dd' data-color='$color' data-mes='$mes'>".$tpl."</li>" ;

    }

    $vars = array('{ITEMS}' => $itens) ;
    echo template_eval($container,$vars) ;

?>
