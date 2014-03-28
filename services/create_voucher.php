<?php
/*
 * cupon (string)
 * destino (string)
 * comprador (userid)
 * beneficiario (userid)
 * data (date)
 * utilizado (bool)
 * data_utilizado (date)
 * bloqueado (bool)
 * validade (date)
 */

include('../../../exec_in_joomla.inc');

function uuid()
{
    // version 4 UUID
    $get = sprintf(
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
    return strtoupper($get);
}

function sendMyMail($voucher)
{

    require '../../../mail/PHPMailerAutoload.php';
    $nome = utf8_decode($_POST['nome']);
    $email = $_POST['email'];


    $msg = <<<EOT
    <body style="font-family:Segoe, 'Segoe UI', 'DejaVu Sans', 'Trebuchet MS', Verdana, sans-serif;font-size:11pt;">
        <h2>$nome</h2>
        <span style="font-size:16px;font-weight:bold;">Agora voc� j� pode se increver usando seu Cupom!</span>
        <hr/>
        <h1>$voucher</h1>
        <hr/>
        <h3>Siga os passos abaixo:</h3>
        <ol>
            <li>Copie o c�digo de seu Cupom (Selecione e Ctrl+C)</li>
            <li>Acesse o Calend�rio de Treinamento <a href="http://bodysystems.net/calendarios/treino" target="_blank"> clique aqui</a></li>
            <li>Preencha os campos com todos os dados necess�rios</li>
            <li>Na etapa de pagamento, escolha a op��o 'Pagar com Cupom'</li>
            <li>Cole o seu c�digo (Ctrl+V) na respectiva caixa</li>
            <li>Siga os passos finais at� a confirma��o de sua inscri��o</li>
        </ol>
        <p>Pronto! Agora � s� ficar de olho na data correta de seu treinamento, acompanhando em nosso site</p>
        <p>Sucesso!<br/>Time Body Systems</p>
    </body>
EOT;


    $data = Date('Y-m-d H:i:s');
    $msg_dump = "Segue abaixo o seu Voucher para inscri��o\n\n$voucher";

    file_put_contents('voucher_dump.csv', $msg_dump, FILE_APPEND);


    $mail = new PHPMailer;

    $mail->isSMTP(); // Set mailer to use SMTP
    $mail->Host = 'localhost'; // Specify main and backup server
    $mail->SMTPAuth = true; // Enable SMTP authentication
    $mail->Username = 'power14'; // SMTP username
    $mail->Password = 'q!R;E]rgqog/jpFDMZ&^SL[O{>377YS.@6"rwW;'; // SMTP password
    $mail->SMTPSecure = 'tls'; // Enable encryption, 'ssl' also accepted

    $mail->From = 'noreply@powerjump.com.br';
    $mail->FromName = 'Treinamentos Body Systems';
    $mail->addAddress($email, $nome); // Add a recipient
    $mail->addAddress('estela_adriana25@hotmail.com', 'Estela Louren�o'); // Name is optional
    $mail->addReplyTo('noreplay@bodysystems.net', 'N�o responda');

    $mail->WordWrap = 50; // Set word wrap to 50 characters
    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    $mail->isHTML(true); // Set email format to HTML

    $mail->Subject = 'Cupom para Inscri��o em Treinamento Body Systems';
    $mail->Body = $msg;
    $mail->AltBody = $msg_dump;

    if (!$mail->send()) {

        $ret = $mail->ErrorInfo;


    } else {

        $ret = 'Email enviado com sucesso.';

    }

    return $ret;

}

try {
//1.CRIA VOUCHER COM ID UNICO
    $voucher = uuid();
    $comprador = $_POST['cpf'];
    $data = Date('Y-m-d H:i:s');
    $validade = $_POST['validade'];

//2. ARMAZENA VOUCHER

    $db = & JFactory::getDBO();
    $query = "INSERT INTO cupons (cupon,destino,destino_id,comprador,beneficiario,idcliente,data,utilizado,data_utilizado,bloqueado,validade,TransacaoID) VALUES ('$voucher',NULL,NULL,'$comprador',NULL,NULL,'$data',0,NULL,0,'$validade','$voucher')";
    file_put_contents('query_cupom.log', $query);
    $db->setQuery($query);
    $db->Query();
    if ($db->getErrorNum() > 0) {
        $ret = array(1, $db->getErrorMsg());
    } else {
        if ($db->getAffectedRows() > 0) {
            $ret = array(0, $voucher);
        } else {
            $ret = array(1, 'N�o foi poss�vel criar o cupom');
        }
    }


//3. CRIA EMAIL
    if ($ret[0] == 0) {
        $mmail = sendMyMail($voucher);
    } else {
        $mmail = 'email n�o enviado';
    }


//4. RETORNA PROCESSAMENTO
    $ret = array($ret, $mmail);
    echo json_encode($ret);

} catch (Exception $e) {
    $ret = array(1, $e->getMessage());
    echo json_encode($ret);
}
