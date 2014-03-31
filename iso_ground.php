<?php

include('../../exec_in_joomla.inc');

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

function cmp($a, $b)  {
    return strcmp($a->INICIO, $b->INICIO);
}


function diffDate($d1, $d2, $type='', $sep='-')
{
    $d1 = explode($sep, $d1);
    $d2 = explode($sep, $d2);
    switch ($type)
    {
        case 'A':
            $X = 31536000;
            break;
        case 'M':
            $X = 2592000;
            break;
        case 'D':
            $X = 86400;
            break;
        case 'H':
            $X = 3600;
            break;
        case 'MI':
            $X = 60;
            break;
        default:
            $X = 1;
    }

    $vD1 = mktime(0, 0, 0, $d2[1], $d2[2], $d2[0]) ;
    $vD2 = mktime(0, 0, 0, $d1[1], $d1[2], $d1[0] ) ;

    $interval = floor(( $vD1 - $vD2 )/$X ) ;

    return $interval ;

}

    function getRodadaWS(){
        $db = &JFActory::getDBO();
        $query = "SELECT * FROM rodadas ORDER BY id DESC LIMIT 1" ;
        $db->setQuery($query);
        $result = $db->loadRow();
        $ret = array($result[2],$result[3],$result[5]);
        return $ret;

    }


    //DEFINE O TIPO DE TREINAMENTO
    $tipo_treino = (isset($_GET['t']))? $_GET['t']:1;
    //$tipo_treino = 1;

    $container = file_get_contents('pages/template_main.html');

$element_orange = <<<EOT
  <div class="element alkaline-earth isotope-item" data-symbol="{NSA}" data-category="other" data-nsa="{NSA}" data-color="orange" data-sigla="{SIGLA}" data-evento="{DATA}">
      <p class="number" id="numero">{NUMBER}</p>
      <h3 class="symbol"><span class="fb" id="nsa" data-cidade="{CIDADE}">{NSA}</span></h3>
      <h2 class="name" id="cidade">{CIDADE}</h2>
      <p class="corum" id="corum">{CORUM}</p>
      <p class="weight" name="nome_evento">{NOME_EVENTO}</p>
      <p class="weight" style="display:none;" id="programa">{PROGRAMA}</p>
      <p style="display:none;" name="endereco" >{ENDERECO}</p>
      <p style="display:none;" name="local">{LOCAL}</p>
      <p class="programa {PROG}"></p>
  </div>
EOT;

$element_red = <<<EOT
<div id="{ID}" class="element alkali isotope-item" data-symbol="{NSA}" data-category="alkali" data-nsa="{NSA}" data-color="red" data-sigla="{SIGLA}" data-evento="{DATA}">
     <p class="number" id="numero">{NUMBER}</p>
      <h3 class="symbol"><span class="fb" id="nsa" data-cidade="{CIDADE}">{NSA}</span></h3>
      <h2 class="name" id="cidade">{CIDADE}</h2>
      <p class="corum" id="corum">{CORUM}</p>
      <p class="weight" name="nome_evento">{NOME_EVENTO}</p>
      <p class="weight" style="display:none;" id="programa">{PROGRAMA}</p>
      <p style="display:none;" name="endereco" >{ENDERECO}</p>
      <p style="display:none;" name="local">{LOCAL}</p>
      <p class="programa {PROG}"></p>
    </div>
EOT;

$element_green = <<<EOT
<div id="{ID}" class="element feature actinoid width2 height2 isotope-item" data-symbol="{NSA}" data-category="alkali" data-nsa="{NSA}" data-color="green" data-sigla="{SIGLA}" data-evento="{DATA}">
      <p class="number" id="numero">{NUMBER}</p>
      <h3 class="symbol"><span class="fb" id="nsa" data-cidade="{CIDADE}">{NSA}</span></h3>
      <h2 class="name" id="cidade">{CIDADE}</h2>
      <p class="corum" id="corum">{CORUM}</p>
      <p class="weight" name="nome_evento">{NOME_EVENTO}</p>
      <p class="weight" style="display:none;" id="programa">{PROGRAMA}</p>
      <p style="display:none;" name="endereco" >{ENDERECO}</p>
      <p style="display:none;" name="local">{LOCAL}</p>
      <p class="programa {PROG}"></p>
      <img src="img/wait_dispatch.gif" class="wait_dispatch" />
    </div>
EOT;

$element_blue = <<<EOT
<div id="{ID}" class="element metalloid isotope-item" data-symbol="{NSA}" data-category="metalloid" data-nsa="{NSA}" data-color="blue" data-sigla="{SIGLA}" data-evento="{DATA}">
     <p class="number" id="numero">{NUMBER}</p>
      <h3 class="symbol"><span class="fb" id="nsa" data-cidade="{CIDADE}">{NSA}</span></h3>
      <h2 class="name" id="cidade">{CIDADE}</h2>
      <p class="corum" id="corum">{CORUM}</p>
      <p class="weight" name="nome_evento">{NOME_EVENTO}</p>
      <p class="weight" style="display:none;" id="programa">{PROGRAMA}</p>
      <p style="display:none;" name="endereco" >{ENDERECO}</p>
      <p style="display:none;" name="local">{LOCAL}</p>
      <p class="programa {PROG}"></p>
    </div>
EOT;

if($tipo_treino == 7) {
    $datas = getRodadaWS() ; //PEGA O REGISTRO MAIS RECENTE DA TABELA (LAST INDEX)
    $inicio = date('Ymd',strtotime($datas[0]));
    $fim = date('Ymd',strtotime($datas[1]));;
    $ids_rodada = $datas[2];

} else {
    $inicio = date('Ymd',strtotime("-3 day")) ; //'20131219';
    $fim = date('Ymd',strtotime("+93 day")) ; //'20140201' ;

}

$programa = '' ;
$estado = '';

$client = new
SoapClient(
    "http://177.154.134.90:8084/WCF/_BS/wcfBS.svc?wsdl",array('cache_wsdl'=>WSDL_CACHE_NONE)
);
$params = array('IdClienteW12'=>229, 'IdTipoTreinamento'=>$tipo_treino, 'Inicio'=>$inicio, 'Fim'=>$fim, 'Estado'=>$estado, 'Programa'=>$programa);
$webService = $client->ListarTreinamentosWebsite($params);
$wsResultTR = $webService->ListarTreinamentosWebsiteResult->VOBS;

    file_put_contents('ws.log',print_r($wsResultTR,true)."\n------------\n".print_r($params,true)."\n$inicio\n$fim\n".print_r($datas,true));


if($tipo_treino != 7) { // SE NÃO FOR WORKSHOP

//MTA
$tipo_treino = 3;

$params = array('IdClienteW12'=>229, 'IdTipoTreinamento'=>$tipo_treino, 'Inicio'=>$inicio, 'Fim'=>$fim, 'Estado'=>$estado, 'Programa'=>$programa);
$webService = $client->ListarTreinamentosWebsite($params);
$wsResultMTA = $webService->ListarTreinamentosWebsiteResult->VOBS;

//MTA
$tipo_treino = 4;

$params = array('IdClienteW12'=>229, 'IdTipoTreinamento'=>$tipo_treino, 'Inicio'=>$inicio, 'Fim'=>$fim, 'Estado'=>$estado, 'Programa'=>$programa);


$webService = $client->ListarTreinamentosWebsite($params);
$wsResultMC = $webService->ListarTreinamentosWebsiteResult->VOBS;

if(!is_array($wsResultMC)){
	$wsResultMC = array ( 0 => $wsResultMC);;
	}

//print "<pre>";
//print_r($wsResultMTA);
//print_r($wsResultMC);
//print (is_array($wsResultMC));
//print_r($wsResultTR);
//print_r($params);
//print "</pre>";

    $wsResult = array_merge( $wsResultTR, $wsResultMTA,$wsResultMC );
} else {
    $wsResult = $wsResultTR ;
}


usort($wsResult,"cmp");
$data_dead = date('Y-m-d',time());



if(count($wsResult)>0) {
foreach($wsResult as $obj) {
    $i++;
    $dd = date('d/m',strtotime($obj->INICIO));
    $mes = date('m',strtotime($obj->INICIO));
    $data = date('d-m-Y',strtotime($obj->INICIO));
    $nsa = get_nsa($obj->ID_NSA);
    $id = $obj->ID_TREINAMENTO;
    $end =  $obj->ENDERECO;
    $sigla = $obj->SIGLA;

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
        $nome_evento = $obj->DS_SERVICO.' '.$obj->SIGLA;
    }

    $len = strlen($end);
    $corum = getCorum($id);
    $coruns = ($corum > 1)? $corum ." inscritos" : $corum ." inscrito" ;
    $cidade = utf8_decode($obj->CIDADE);
    $cidade = (trim($obj->CIDADE) == 'São José do Rio Preto')? 'S.J. Rio Preto':$cidade;
    $cidade = (trim($obj->CIDADE) == 'SÃO JOSÉ DOS CAMPOS')? 'S.J. dos Campos':$cidade;
    $cidade = (trim($obj->CIDADE) == 'São José dos Campos')? 'S.J. dos Campos':$cidade;
    $prog = strtolower($obj->DS_ABREVIACAO);

    $vars = array(
        '{ID}' => $i,
        '{NUMBER}' => $dd,
        '{NSA}' => $nsa,
        '{CIDADE}' => $cidade,
        '{DATA}' => $data,
        '{PROGRAMA}' => $id, //date('d-m-Y',strtotime($obj->INICIO))
        '{LOCAL}' =>  utf8_decode($local),
        '{NOME_EVENTO}' => ($tipo_treino!=7) ?  utf8_decode($nome_evento): $cidade,
        '{ENDERECO}' => utf8_decode($endereco),
        '{CORUM}' =>$coruns,
        '{PROG}' => $prog
    ) ;


//CONFIGURA CODIFICAÇÃO DE CORES
    //POR PRAZO
    $data_ref = date('Y-m-d',strtotime($obj->INICIO));
    $interv = diffDate($data_dead,$data_ref,'D','-');



    //POR CORUM
    if($corum >=4 ) {
        $tpl = template_eval($element_green,$vars);
        $color = 'green';
    } else if( $corum >2 && $corum < 4) {
        $tpl = template_eval($element_orange,$vars);
        $color = 'orange';
    }  else {

        if($interv > 45) {
            $tpl = template_eval($element_blue,$vars);
            $color = 'blue';
        } else {
            $tpl = template_eval($element_red,$vars);
            $color = 'red';
        }
    }




    $itens .= "<li data-int='$interv' data-nsa='$nsa' data-data='$dd' data-color='$color' data-sigla='$sigla' data-mes='$mes' data-programa = '$prog'>".$tpl."</li>" ;

}
}

$vars = array('{ITEMS}' => $itens) ;
echo template_eval($container,$vars) ;

