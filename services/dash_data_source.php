<?php
    //RECUPERA O EXTRATO DO MOVIMENTO DO EVENTO

    include('../../../exec_in_joomla.inc');
    $eventoid = $_REQUEST['eventoid'] ;

    file_put_contents('evento.log',$eventoid);

    $db =& JFactory::getDBO();

    //BOLETOS
    $query = "SELECT DATE_FORMAT(data_compensado,'%d-%m')AS Dia, SUM(valor_cobrado) as Total FROM boletos_bs WHERE cnab LIKE '%$eventoid%' AND compensado = 1 GROUP BY Dia;";
    $db->setQuery($query);
    $result = $db->loadObjectList();
    if(count($result)>0) {
        foreach($result as $row){
            $total = $row->Total;
            $dia = $row->Dia;
            $data_bb[] = $dia ;
            $total_bb[] = floatval($total);
        }
        $boleto = array($data_bb,$total_bb);
    }

    //PAGSEGURO
    $query = "SELECT DATE_FORMAT(Data,'%d-%m')AS Dia, SUM(ProdValor) as Total FROM PagSeguroTransacoes WHERE ProdID LIKE '%$eventoid%' AND StatusTransacao IN ('Aprovado','Completo') GROUP BY Dia;";
    $db->setQuery($query);
    $result = $db->loadObjectList();
    if(count($result)>0) {
        foreach($result as $row){
            $total = $row->Total;
            $dia = $row->Dia;
            $data_ps[] = $dia ;
            $total_ps[] =floatval($total) ;

        }
        $pagseguro = array($data_ps,$total_ps);
    }


    //MERCADO PAGO
    $query = "SELECT DATE_FORMAT(data_aprovado,'%d-%m') AS Dia, SUM(valor_transacao) as Total FROM MercadoPagoTransacoes WHERE cnab  LIKE '%eventoid%' AND `status` LIKE ('approved') GROUP BY Dia";
    $db->setQuery($query);
    $result = $db->loadObjectList();
    if(count($result)>0) {
        foreach($result as $row){
            $total = $row->Total;
            $dia = $row->Dia;
            $data_mp[] = $dia ;
            $total_mp[] = floatval($total) ;
        }
        $mercadopago = array($data_mp,$total_mp);
    }


    //$sum = array_sum($boleto[1]) + array_sum($pagseguro[1]) + array_sum($mercadopago[1]) ;

    $sum += is_array($boleto[1])? array_sum($boleto[1]): 0 ;
    $sum += is_array($pagseguro[1])? array_sum($pagseguro[1]): 0 ;
    $sum += is_array($mercadopago[1])? array_sum($mercadopago[1]): 0 ;

    //$sum = number_format($sum,2,',','.');
    $array = array($boleto,$pagseguro,$mercadopago,'Meses de 2013','Valores x1000',$sum);











    header("content-type: application/json");
    //$valores = array(7,4,2,8,4,1,9,3,2,16,7,12);
    //$meses = array('janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro');
    //$array = array($valores,$meses,'Meses de 2013','Valores x1000');
    echo $_GET['callback']. '('. json_encode($array) . ')';

?>