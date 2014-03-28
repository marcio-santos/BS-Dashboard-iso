<?php
    //--------------------------------------------------------------------------------
    function template_eval(&$template, &$vars) { return strtr($template, $vars); }
    //================================================================================

    function getPrograma($cnab) {
        $xnab = explode('|',$cnab);
        $prog = $xnab[2];

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
        return $programa;
    }

    $template_folha = file_get_contents('../pages/template_etiqueta.html');

    $file = isset($_REQUEST['f'])? $_REQUEST['f'] : 'pre-lote.log' ;
    $lote = file_get_contents($file);
    $log = (array)json_decode($lote,true);
    
    $template_etiqueta = <<<EOT
        <div class="etiqueta">
            <strong>{PROGRAMA}</strong><br/>
            {NOME_EVO}<br/>
            {ENDERECO}
        </div>
EOT;
    foreach($log as $ret) {
        $count++ ;
        $params = array(
            '{NOME_EVO}' => $ret['nome_evo'],
            '{PROGRAMA}' => getPrograma($ret['cnab']),
            '{ENDERECO}' => $ret['endereco_remessa']
        );
        if($count<=10) {
            $coluna1 .=  template_eval($template_etiqueta,$params);
        } else if ($count >10 && $count<=20) {
            $coluna2 .= template_eval($template_etiqueta,$params);
        } else {
            $count = 1;
            $coluna1 .="<div class='breakhere'></div>";
            $coluna2 .="<div class='breakhere'></div>";
            $coluna1 .=  template_eval($template_etiqueta,$params);

        }

    }

    $vars = array(
        '{COLUNA1}' => $coluna1,
        '{COLUNA2}' => $coluna2
    );
    echo template_eval($template_folha,$vars);

?>
