<?php

    $file = isset($_REQUEST['f'])? $_REQUEST['f'] : 'pre-lote.jsn' ;
    $lote = file_get_contents($file);
    $log = (array)json_decode($lote,true);

    $xls = 'EVO_ID;SITE_ID;CLIENTE;ENDEREÇO;FRETE'."\n" ;
    foreach($log as $ret) {
        $xls .= $ret['idcliente'].";".$ret['userid'].";".utf8_decode($ret['nome_evo']).";".utf8_decode($ret['endereco_remessa']).";".$ret['valor_frete']."\n" ;
    }
    $xls_file = str_replace('.jsn','',$file);
    file_put_contents('jsn_output/'.$xls_file.".csv",$xls) ;

    header('Location: http://bodysystems.net/_ferramentas/dashboard-iso/services/jsn_output/'.$xls_file.'.csv') ;


?>
