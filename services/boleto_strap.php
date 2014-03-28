<?php

    include('../../../exec_in_joomla.inc');

    //------------------------------
    function template_eval(&$template, &$vars) { return strtr($template, $vars); }
    //------------------------------

    // TRANSACAOID
    function uuid(){
        // version 4 UUID
        $get =  sprintf(
            '%08x-%04x-%04x-%02x%02x-%012x',
            mt_rand(),
            mt_rand(0, 65535),
            bindec(substr_replace(
                    sprintf('%016b', mt_rand(0, 65535)), '0100', 11, 4)
            ),
            bindec(substr_replace(sprintf('%08b', mt_rand(0, 255)), '01', 5, 2)),
            mt_rand(0, 255),
            mt_rand()
        );
        return strtoupper($get) ;
    }

    //CRIA O NOSSO NÚMERO PARA BOLETO
    function nosso_numero() {
        do {
            $ultimo_id = file_get_contents('../../calendario/services/nosso_numero.count');
        } while ($ultimo_id === false);
        $ultimo_id = $ultimo_id +1 ;
        file_put_contents('../../calendario/services/nosso_numero.count',$ultimo_id,LOCK_EX) ;

        return $ultimo_id ;
    }
   //-------------------------------------

    //INSERE O BOLETO NA TABELA BOLETOS_BS
    function inserirBoletoBs($evento,$tipo_evento,$cnab,$evoid,$siteid,$nome,$email,$endereco,$promocode,$data_documento,$data_vencimento,$nnum,$docnum,$linha,$gvalor,$gfrete,$nivel_bonus,$transacaoid) {

        //REGISTRA O IP DO USUÁRIO

        if ( isset($_SERVER["REMOTE_ADDR"]) )    {

            $ip=$_SERVER["REMOTE_ADDR"] . ' ';

        } else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) )    {

            $ip=$_SERVER["HTTP_X_FORWARDED_FOR"] . ' ';

        } else if ( isset($_SERVER["HTTP_CLIENT_IP"]) )    {

            $ip=$_SERVER["HTTP_CLIENT_IP"] . ' ';

        }


        try {

            $db = &JFactory::getDBO() ;

            if($gvalor!=0 && $evoid!=0 && $cnab!='') {

                $query = "INSERT INTO boletos_bs (id,evento,tipo_evento,cnab,idcliente,userid,nome_evo,user_email,endereco_remessa,promo_code,data_geracao,data_vencimento,nosso_numero,doc_numero,linha_digitavel,valor_cobrado,valor_frete,nivel_bonus,ip,compensado,data_compensado,transacaoid) VALUES ('null', '$evento','$tipo_evento', '$cnab','$evoid','$siteid','$nome','$email','$endereco','$promocode','$data_documento','$data_vencimento','$nnum','$docnum','$linha','$gvalor','$gfrete','$nivel_bonus','$ip','false','0000-00-00','$transacaoid')" ;
                $db->setQuery($query) ;
                $db->Query();

                file_put_contents('atrack.log',$query,FILE_APPEND);

                if ($db->getErrorNum()) {
                    $ret = array(false,$db->getErrorMsg());
                    $thefile =  'erros_boletos_site.log' ;
                    $msg = "[".date("Y-m-d | H:i:s", time()) .'] - '.$db->getErrorMsg()."\n";
                    file_put_contents ($thefile, $msg, FILE_APPEND | LOCK_EX);
                }  else {
                    $ret = array(true,'Boleto inserido na base com sucesso.') ;
                }

            } else {
                $ret = array(false,'Existe algum problema com o valor do boleto.');
            }

            return $ret ;

        } catch (Exception $e) {
            $msg = "[".date("Y-m-d | H:i:s", time()) .'] - '.$e->getMessage()." - Origem -> IdCliente:".$evoid." - SiteID:".$siteid." - NNum:".$nnum." - CNAB:".$cnab."\r\n";
            $thefile =  'erros_boletos_site.log' ;
            file_put_contents ($thefile, $msg, FILE_APPEND | LOCK_EX);
            $ret = array(false,$e->getMessage()) ;
            return $ret ;

        }

    }

    //INSERE O BOLETO NA TABELA DO EVO
    function inserirBoletosEvo($evento,$cnab,$evoid,$promocode,$data_documento,$data_vencimento,$nnum,$docnum,$gvalor,$gfrete,$endereco){


        $gfrete = 0;
        $promocode = '-';
        $client = new SoapClient(
            "http://177.154.134.90:8084/WCF/_BS/wcfBS.svc?wsdl" , array('cache_wsdl' => 0)
        );

        $params = array(

            'Evento'=>$evento,
            'CNAB'=>$cnab,
            'IdCliente'=>$evoid,
            'CodigoPromotor'=>$promocode,
            'DataGeracao'=> date('Ymd His', strtotime($data_documento)),
            'DataVencimento' => date('Ymd His', strtotime($data_vencimento)),
            'NossoNumero' =>$nnum,
            'DocNumero' =>$docnum,
            'ValorCobrado' =>$gvalor,
            'ValorFrete' =>$gfrete,
            'Endereco' =>$endereco

        );

        $parametros = print_r($params,true) ;
        $thefile =  'parametros.log' ;
        file_put_contents ($thefile, "[".date("Y-m-d | H:i:s", time()) .'] - '."\n".$parametros."\n", FILE_APPEND | LOCK_EX);

        try {
            $webService = $client->InserirBoleto($params);
            $result = $webService->InserirBoletoResult ;

            if($result==0){
                $ret = array(true,'Boleto Inserido com Sucesso');
            } else {
                $ret = array(false,$result) ;
                $thefile =  'erros_boletos_evo.log' ;
                file_put_contents ($thefile, "[".date("Y-m-d | H:i:s", time()) .'] - '.$ret[1]."\n", FILE_APPEND | LOCK_EX);
            }

            return $ret ;

        } catch (Exception $e) {
            $msg = "[".date("Y-m-d | H:i:s", time()) .'] - '.$e->getMessage()." - Origem -> IdCliente:".$evo_id." - SiteID:".$siteid." - NNum:".$nnum." - CNAB:".$cnab."\r\n";
            $thefile =  'erros_boletos_evo.log' ;
            file_put_contents ($thefile, $msg, FILE_APPEND | LOCK_EX);
            $ret = array(false,$e->getMessage()) ;
            return $ret ;

        }

    }

    //CRIA O BOLETO PARA PAGAMENTO
    function ProcBoleto($nnum,$data_documento,$data_vencimento,$valor_cobrado,$nome,$endereco,$cpf,$i1,$i2,$i3,$i4,$i5) {

        $valor_cobrado = str_replace(',','.', $valor_cobrado) ;

        $codigobanco = '341'; // O Itau sempre será este número
        $agencia = '0350'; // 4 posições
        $conta = '37578';  // 5 posições sem dígito
        $carteira = '175'; // A sem registro é 175 para o Itaú
        $moeda = '9'; // Sempre será 9 pois deve ser em Real
        $nossonumero = $nnum; // Número de controle do Emissor (pode usar qq número de até 8 digitos);
        $data = $data_documento;  //'05/03/2005'; // Data de emissão do boleto
        $vencimento = $data_vencimento;   //'05/03/2006'; // Data no formato dd/mm/yyyy
        $valor = $valor_cobrado; // Colocar PONTO no formato REAIS.CENTAVOS (ex: 666.01)

        // NOS CAMPOS ABAIXO, PREENCHER EM MAIÚSCULAS E DESPREZAR ACENTUAÇÃO, CEDILHAS E
        // CARACTERES ESPECIAIS (REGRAS DO BANCO)

        $cedente = 'BODY SYSTEMS LTDA.';

        $sacado = $nome;
        $endereco_sacado = $endereco;
        //$cidade = 'UBERLANDIA';
        //$estado = 'MG';
        //$cep = '38400-000';
        $cpf_cnpj = $cpf;
        $instrucoes1 = $i1;
        $instrucoes2 = $i2;
        $instrucoes3 = $i3;
        $instrucoes4 = $i4;
        $instrucoes5 = $i5;

        // FIM DA ÁREA DE CONFIGURAÇÃO

        function Modulo11($valor) {
            $multiplicador = '4329876543298765432987654329876543298765432';
            for ($i = 0; $i<=42; $i++ ) {
                $parcial = $valor[$i] * $multiplicador[$i];
                $total += $parcial;
            }
            $resultado = 11-($total%11);
            if (($resultado >= 10)||($resultado == 0)) {
                $resultado = 1;
            }

            return $resultado;
        }


        function calculaDAC ($CalculaDAC) {
            $tamanho = strlen($CalculaDAC);
            for ($i = $tamanho-1; $i>=0; $i--) {
                if ($multiplicador !== 2) {
                    $multiplicador = 2;
                }
                else {
                    $multiplicador = 1;
                }
                $parcial = strval($CalculaDAC[$i] * $multiplicador);

                if ($parcial >= 10) {
                    $parcial = $parcial[0] + $parcial[1];
                }
                $total += $parcial;
            }
            $total = 10-($total%10);
            if ($total >= 10) {
                $total = 0;
            }
            return $total;
        }

        function calculaValor ($valor) {
            $valor = str_replace('.','',$valor);
            return str_repeat('0',(10-strlen($valor))).$valor;
        }

        function calculaNossoNumero ($valor) {
            return str_repeat('0',(8-strlen($valor))).$valor;
        }

        function calculaFatorVencimento ($dia,$mes,$ano) {
            $vencimento = mktime(0,0,0,$mes,$dia,$ano)-mktime(0,0,0,7,3,2000);
            return ceil(($vencimento/86400)+1000);
        }

        // CALCULO DO CODIGO DE BARRAS (SEM O DAC VERIFICADOR)
        $codigo_barras = $codigobanco.$moeda.calculaFatorVencimento(substr($vencimento,0,2),substr($vencimento,3,2),substr($vencimento,6,4));
        $codigo_barras .= calculaValor($valor).$carteira.calculaNossoNumero($nossonumero).calculaDAC($agencia.$conta.$carteira.calculaNossoNumero($nossonumero)).$agencia.$conta.calculaDAC($agencia.$conta).'000';



        // CALCULO DA LINHA DIGITÁVEL
        $parte1 = $codigobanco.$moeda.substr($carteira,0,1).substr($carteira,1,2).substr(calculaNossoNumero($nossonumero),0,2);
        $parte1 = substr($parte1,0,5).'.'.substr($parte1,5,4).calculaDAC($parte1);

        $parte2 = substr(calculaNossoNumero($nossonumero),2,5).substr(calculaNossoNumero($nossonumero),7,1).calculaDAC($agencia.$conta.$carteira.calculaNossoNumero($nossonumero)).substr($agencia,0,3);
        $parte2 = substr($parte2,0,5).'.'.substr($parte2,5,5).calculaDAC($parte2);

        $parte3 = substr($agencia,3,1).$conta.calculaDAC($agencia.$conta).'000';
        $parte3 = substr($parte3,0,5).'.'.substr($parte3,5,8).calculaDAC($parte3);

        $parte5 = calculaFatorVencimento(substr($vencimento,0,2),substr($vencimento,3,2),substr($vencimento,6,4)).calculaValor($valor);

        $numero_boleto = $parte1.' '.$parte2.' '.$parte3.' '.Modulo11($codigo_barras).' '.$parte5;

        // INSERÇÃO DO DAC NO CODIGO DE BARRAS

        $codigo_barras = substr($codigo_barras,0,4).Modulo11($codigo_barras).substr($codigo_barras,4,43);
        $m_codigo_barras = Modulo11($codigo_barras);
        //   print Modulo11($codigo_barras);
        //   exit;

        $ret = array($numero_boleto,$m_codigo_barras) ;
        return $ret ;

    }

    //PUBLICA O BOLETO
    function setBoleto($cnab,$nnum,$data_documento,$data_vencimento,$valor_cobrado,$sac,$endereco,$cpf,$evento,$transacaoid) {
        $template = <<<EOT
		 <form id="form_boleto" target="_blank" name="form_boleto" method="post" action="http://bodysystems.net/_ferramentas/dashboard-iso/services/boleto_manual.php">
			  <input type="image" name="submit" src="http://bodysystems.net/images/bt_boletos.png" alt="Pagar com Boleto" />
			  <input type="hidden" name="nnum" value="{NNUM}" />
			  <input type="hidden" name="data_documento" value="{DATA_DOCUMENTO}" />
			  <input type="hidden" name="data_vencimento" value="{DATA_VENCIMENTO}" />
			  <input type="hidden" name="cnab" value="Uso interno BS: {CNAB}" />
			  <input type="hidden" name="valor_cobrado" value="{VALOR_COBRADO}" />
			  <input type="hidden" name="sac" value="{SAC}" />
			  <input type="hidden" name="endereco" value="{ENDERECO}" />
			  <input type="hidden" name="cpf" value="{CPF}" />
			  <input type="hidden" name="evento" value="{EVENTO}" />
			  <input type="hidden" name="transacaoid" value="{TRANSACAOID}" />
		 </form>

EOT;
        $endereco = utf8_decode($endereco) ;
        $params = array(
            '{CNAB}' => $cnab,
            '{NNUM}' => $nnum,
            '{DATA_DOCUMENTO}' => $data_documento,
            '{DATA_VENCIMENTO}'=> $data_vencimento,
            '{EVENTO}' => $evento,
            '{ENDERECO}' => $endereco,
            '{CPF}' => $cpf,
            '{VALOR_COBRADO}'=> $valor_cobrado,
            '{SAC}'=> $sac,
            '{TRANSACAOID}' => $transacaoid,
        );

        $botao = template_eval($template,$params) ;
        return $botao;
    }

    function sendEmail($from,$to,$msg) {
        file_put_contents('x.log',$from."\n".$to."\n".$msg);
    }

    $nnum = nosso_numero() ;

    $i1 = 'NÃO RECEBER ESTE DOCUMENTO APÓS VENCIMENTO';
    $i2 = 'PAGAMENTO REFERENTE A TREINAMENTO BODY SYSTEMS' ;
    $i3 = $_POST['evento'] ;
    //$i4 = 'NÃO SE ESQUEÇA DE INSCREVER-SE NO GROUNDWORKS!';
    $i5 = 'USO INTERNO: '.$cnab ;


    try {


    //PROCESSA OS DADOS DO BOLETO
    // $boleto[0] ==> linha digitavel
    // $boleto[1] ==> codigo de barras
    $data_documento =   date('d/m/Y');
    //$data_vencimento =  date('d/m/Y',strtotime('+1 day'));
	//print 'aki:['.$_POST['data_vcto'].']';
	
	if($_POST['data_vcto'] && $_POST['data_vcto']!='__/__/____'){
		$data_vencimento =  $_POST['data_vcto'];
	}else{
		$data_vencimento =  date('d/m/Y',strtotime('+1 day'));
	}
	$dt_vcto =  explode('/',$data_vencimento);
    $evoid =           $_POST['evoid'];
    $userid =           $_POST['userid'];
    $cpf =              $_POST['cpf'];
    $nome =             $_POST['nome'];
    $endereco =         $_POST['endereco'];
    $valor_cobrado =    $_POST['valor_cobrado'] ;
    $docnum =           $evoid;
    $evento =           'BOLETO MANUAL';
    $cnab =             'C1|CA|00|00';
    $transacaoid =      uuid();
    $valor_frete =  '0';
    $promocode =    '-';


    file_put_contents('post.log',print_r($_POST,true));

    $data_doc_db = date('Y-m-d');
    //$data_venc_db = date('Y-m-d',strtotime('+1 day'));
	$data_venc_db = date('Y-m-d', strtotime($dt_vcto[2].'-'.$dt_vcto[1].'-'.$dt_vcto[0]));

    //$valor_cobrado = $valor_cobrado; //calcTotal($valor_ws,$valor_cartao,$valor_boleto,$valor_frete,$formaPagto) ;
    $valor_cobrado = sprintf("%01.2f", $valor_cobrado);

    $boleto = ProcBoleto($nnum,$data_documento,$data_vencimento,$valor_cobrado,$nome,$endereco,$cpf,$i1,$i2,$i3,$i4,$i5) ;
    $linha = $boleto[0] ;

        file_put_contents('x.log',$linha,FILE_APPEND) ;

    //envia dados do boleto para o site (tabela boleto_bs)
    $boleto_bs = inserirBoletoBs($evento,'CA',$cnab,$evoid,$siteid,$nome,$email,$endereco,$promocode,$data_doc_db,$data_venc_db,$nnum,$docnum,$linha,   $valor_cobrado,$valor_frete,'1',$transacaoid) ;

        file_put_contents('x.log',$boleto_bs,FILE_APPEND) ;

    if(!$boleto_bs[0]) {
        $to ='debug@bodysystems.net' ;
        $subject = 'Erro cadastrando boleto no Site';
        $msg = 'Consulte o arquivo erros_boletos_site.log na pasta _ferramentas/calendario para detalhes.' ;
        sendEmail($to,$subject,$msg) ;
    }

    //envia dados do boleto para o evo
    $boleto_evo = inserirBoletosEvo($evento,$cnab,$evoid,$promocode,$data_doc_db,$data_venc_db,$nnum,$docnum,$valor_cobrado,$valor_frete,$endereco) ;

    if(!$boleto_evo[0]) {
        $to ='debug@bodysystems.net' ;
        $subject = 'Erro cadastrando boleto no EVO';
        $msg = 'Consulte o arquivo erros_boletos_evo.log na pasta _ferramentas/calendario para detalhes.' ;
        sendEmail($to,$subject,$msg) ;
    }

    //gera o botào do boleto
    $botao = setBoleto($cnab,$nnum,$data_documento,$data_vencimento,$valor_cobrado,$nome,$endereco,$cpf,$evento,$transacaoid) ;
    $mArr = "BOTAO BOLETO: "."\n".$botao."\n" ;
    file_put_contents('incricao_treino.log',$mArr,FILE_APPEND) ;

    echo $botao ;

    //FIM DO BOLETO //////


    } catch (Exception $e) {
        echo $e->getMessage();
    }