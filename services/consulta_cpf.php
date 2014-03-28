<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 14/02/14
 * Time: 08:03
 */

    include ('../../../exec_in_joomla.inc');

    function validaCPF($cpf)
    {    // Verifiva se o nÃºmero digitado contÃ©m todos os digitos
        $cpf = str_pad(preg_replace('[^0-9]', '', $cpf), 11, '0', STR_PAD_LEFT);

        // Verifica se nenhuma das sequÃªncias abaixo foi digitada, caso seja, retorna falso
        if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999')
        {
            return false;
        }
        else
        {   // Calcula os nÃºmeros para verificar se o CPF Ã© verdadeiro
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }

                $d = ((10 * $d) % 11) % 10;

                if ($cpf{$c} != $d) {
                    return false;
                }
            }

            return true;
        }
    }


    $cpf = $_POST['cpf_sacado'];
    $cpf = str_replace('-','',str_replace('.','',$cpf));

if(validaCPF($cpf)){

    //==========INTERAÇÕES COM O EVO====================================//
    $client = new
    SoapClient(
        "http://177.154.134.90:8084/WCF/Clientes/wcfClientes.svc?wsdl"
    );
    $tipo = (strlen($cp)>11)? 1:2;
    $params = array('IdClienteW12'=>229, 'IdFilial'=>1, 'CpfCnpj'=>$cpf, 'TipoCliente'=>$tipo);
    $webService = $client->ListarClienteCPFCNPJ($params);
    $wsResult = $webService->ListarClienteCPFCNPJResult;


    file_put_contents('retorno.log',print_r($wsResult,true));

    // Recupera o IdCliente
    $evoid = $wsResult->ID_CLIENTE;
    $nome = mb_convert_case($wsResult->NOME, MB_CASE_TITLE, "UTF-8");
    $email = strtolower($wsResult->EMAIL);
    $cep = $wsResult->CEP;
    $numero =$wsResult->NUMERO;
    $complemento = mb_convert_case($wsResult->COMPLEMENTO, MB_CASE_TITLE, "UTF-8");
    $endereco = mb_convert_case($wsResult->ENDERECO, MB_CASE_TITLE, "UTF-8");
    $bairro = mb_convert_case($wsResult->BAIRRO, MB_CASE_TITLE, "UTF-8");
    $cidade = mb_convert_case($wsResult->CIDADE, MB_CASE_TITLE, "UTF-8");
    $estado = mb_convert_case($wsResult->ESTADO, MB_CASE_TITLE, "UTF-8");
    $ende = $endereco.','.$numero.' '.$complemento.' '.$bairro.' - '.$cidade.'/'.$estado.' - '.'CEP '.$cep ;

    $db= &JFactory::getDbo();
    $query = "SELECT id FROM wow_users WHERE cpf LIKE ".$db->Quote($cpf);
    $db->setQuery($query);
    $userid = $db->loadResult();

    //VALORES PADRÃO CASO RETORNO SEJA INCOMPATÍVEL
    $userid = is_null($userid) ? 62: $userid;
    $evoid = ($evoid==0) ? 11:$evoid;

    //CORRIGE ENDEREÇO CHARSET
    $endereco = utf8_decode($endereco);

    $ret = array($userid,$evoid,$nome,$ende);



} else {
    $ret = array(1,'CPF/CNPJ INCORRETO - '.$cpf,'');
}

    echo json_encode($ret);