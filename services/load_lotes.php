<?php
    include('../../../exec_in_joomla.inc');
    $db = &JFactory::getDBO();
    $query = "SELECT lote,IF(dta_enviado = '0000-00-00%',dta_liberado,dta_enviado) AS data_envio,count(lote) AS total FROM despacho GROUP BY lote ORDER BY lote DESC;"   ;
    $db->setQuery($query);
    $result = $db->loadObjectList();
    foreach($result as $obj) {
        $total = str_pad($obj->total,3,'0',STR_PAD_LEFT);
        $file = str_replace(':','',str_replace('-','',str_replace(' | ','',$obj->lote)));
        $xls  = "http://bodysystems.net/_ferramentas/dashboard-iso/services/lote_xls.php?f=".$file.'.jsn';
        $xfile = "http://bodysystems.net/_ferramentas/dashboard-iso/services/etiquetas_data_source.php?f=".$file.'.jsn';
        /* //HIGHSLIDE
        $html .= "<li class='list'><a class='list' href='#frmEnvio' onclick='return hs.htmlExpand(this, { contentId: &apos;frmEnvio&apos;,captionText:&apos;$obj->lote&apos; } )'><img style='float:right;padding:5px;' src='img/calendar3.png' /></a><a class='list' href='$xls' alt='Planilha de Controle' title='Planilha de Controle'><img src='img/page_excel.png' style='float:right;padding:5px;cursor:pointer;'  /></a><span class='list' alt='Total de Etiquetas no lote' title='Total de Etiquetas no lote'>$total</span><a alt='Abrir lote' title='Abrir lote' href='$xfile' class='lote-fechado' target='_blank' data-lote='$xfile' data-envios='$obj->total'>$obj->lote</a></li>"  ;
        */
        $id = str_replace(' | ','',str_replace(':','',str_replace('-','',$obj->lote)));   
        if($obj->data_envio=='0') {
            $dta_env = $obj->data_envio;
        }   else {
            $dta_env = Date('d-m-Y',strtotime($obj->data_envio)); 
        }            

        $html .= "<li class='list'><a class='fb' href='#frmEnvio' title='$obj->lote' id='$id' data-envio='$dta_env'><img style='float:right;padding:5px;' src='img/calendar3.png' /></a><a class='list' href='$xls' alt='Planilha de Controle' title='Planilha de Controle'><img src='img/page_excel.png' style='float:right;padding:5px;cursor:pointer;'  /></a><span class='list' alt='Total de Etiquetas no lote' title='Total de Etiquetas no lote'>$total</span><a alt='Abrir lote' title='Abrir lote' href='$xfile' class='lote-fechado' target='_blank' data-lote='$xfile' data-envios='$obj->total'>$obj->lote</a></li>"  ;
    }

    echo $html;



?>
