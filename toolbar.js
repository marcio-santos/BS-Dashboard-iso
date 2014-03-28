$(document).ready(function() {

    $('.toolbar').hover(function(){
        var $img_on = $(this).attr('on');

        $(this).attr('src',$img_on);
        }
    )
    $('.toolbar').mouseout(function(){
        var $img_off = $(this).attr('off');
        if(!$(this).hasClass('selected')) {
            $(this).attr('src',$img_off)
        }
    });

    $('.toolbar').click(function(){
        var the_id = $(this).attr('id');

        //content = getContent($(this).attr('id'));
        //$('#response').text(content);

        $('.toolbar').each(function() {
            if($(this).hasClass('selected')){
                var $img_off = $(this).attr('off');
                $(this).removeClass('selected');
                $(this).attr('src',$img_off);
            }
        });
        $(this).attr('src',$(this).attr('on'));
        $('#'+the_id).addClass('selected');
        uitoggle($(this).attr('id'));
    });

    $('#client').click(function(){

        uitoggle('client');
        var evento_id = $('#tb_eventoid').val();
        var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/client_data_source.php";
        var datax = $.parseJSON(
            $.ajax({
                data: {eventoid:evento_id},
                type: "POST",
                dataType: 'json',
                url: url,
                async: false
            }).responseText
        );

        var html = $.trim($("#template").html()), template = Mustache.compile(html);
        var view = function(record, index){
            return template({record: record, index: index});
        };


        var $summary = $('#summary');
        var $found = $('#found');

        $('#found').hide();


        var callbacks = {
            pagination: function(summary){
                if ($.trim($('#st_search').val()).length > 0){
                    $found.text('Encontrados : '+ summary.total).show();
                }else{
                    $found.hide();
                }
                $summary.text( summary.from + ' a '+ summary.to +' de '+ summary.total +' registros');
            }

        }

        st = StreamTable('#stream_table',
            { view: view,
                per_page: 10,
                callbacks: callbacks,
                pagination: {span: 5, next_text: 'Prox &rarr;', prev_text: '&larr; Ant'}
            },
            datax
        );

        // Jquery plugin
        //$('#stream_table').stream_table({view: view}, datax)

        $('.record_count .badge').text(datax.length);

        console.log(datax);

    });

    $('#home').click(function(){
        uitoggle('home');
    });

    $('#dash').click(function(){
        var options = {
            title: {
                text: 'Movimenta��o Financeira'
            },
            credits: {
                enabled: false
            },
            chart: {
                renderTo: 't_container',
                type: 'spline'
            },
            tooltip: {
                formatter: function() {
                    return 'Refer�cia: <b>'+ this.x +
                    '</b><br/><b>Valor: R$'+ Highcharts.numberFormat(this.y,2,',','.') +'</b>';
                }
            },
            xAxis: {
                type: 'date',
                dateTimeLabelFormats: { // don't display the dummy year
                    month: '%e. %b',
                    year: '%b'
                },
                tickInterval: 1,
                labels:
                {
                    enabled: false
                }
            },
            series: [{},{},{}]
        };

        var url =  "services/dash_data_source.php";
        var evento_id = $('#tb_eventoid').val();

        $.ajax({
            url: url,
            dataType: 'jsonp',
            data: {eventoid:evento_id},
            success: function(data) {
                if(data[0]!== null) { options.series[0].data = data[0][1]; }
                options.series[0].name = "Boletos";
                if(data[1]!== null) {options.series[1].data = data[1][1];}
                options.series[1].name = "Pagseguro";
                if(data[2]!== null) { options.series[2].data = data[2][1]; }
                options.series[2].name = "Mercado Pago";

                if( data[0]!==null || data[0]!==null || data[0]!==null) {
                    var chart = new Highcharts.Chart(options);

                    if(data[0]!==null) {
                        chart.xAxis[0].setCategories(data[0][0]);
                    } else if(data[1]!==null) {
                        chart.xAxis[0].setCategories(data[1][0]);
                    } else if(data[2]!==null) {
                        chart.xAxis[0].setCategories(data[2][0]);
                    }

                    //chart.xAxis[0].setTitle({text: data[3]});
                    chart.yAxis[0].setTitle({text: data[4]});
                    var $acumulado = $.isArray(data)? data[5]:0;
                    $('#saldo_acumulado').text('Faturamento atual: R$'+ Highcharts.numberFormat($acumulado,2,',','.'));
                } else {
                    $('#saldo_acumulado').text('Faturamento atual: R$0.00');
                    var chart = new Highcharts.Chart(options);
                }

            }
        });
        /*
        $.getJSON(url,{eventoid:$('#id_evento').text}, function(data) {

        if(data[0]!== null) {
        options.series[0].data = data[0][1];
        options.series[0].name = "Boletos";
        }
        if(data[1]!== null) {
        options.series[1].data = data[1][1];
        options.series[1].name = "Pagseguro";
        }
        if(data[2]!== null) {
        options.series[2].data = data[2][1];
        options.series[2].name = "Mercado Pago";
        }
        var chart = new Highcharts.Chart(options);
        chart.xAxis[0].setCategories(data[0][0]);
        chart.xAxis[0].setTitle({text: data[3]});
        chart.yAxis[0].setTitle({text: data[4]});
        });*/

    });

    $('#groundw').click(function(){

        uitoggle('groundw');
        var evento_id = $('#tb_eventoid').val();
        var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/groundw_data_source.php";
        var datax = $.parseJSON(
            $.ajax({
                data: {eventoid:evento_id},
                type: "POST",
                dataType: 'json',
                url: url,
                async: false
            }).responseText
        );

        console.log(datax);
        var html = $.trim($("#gw_template").html()), template = Mustache.compile(html);
        var view = function(record, index){
            return template({record: record, index: index});
        };


        var $summary = $('#summary');
        var $found = $('#found');

        $('#found').hide();


        var callbacks = {
            pagination: function(summary){
                if ($.trim($('#st_search').val()).length > 0){
                    $found.text('Encontrados : '+ summary.total).show();
                }else{
                    $found.hide();
                }
                $summary.text( summary.from + ' a '+ summary.to +' de '+ summary.total +' registros');
            }

        }

        st = StreamTable('#gw_stream_table',
            { view: view,
                per_page: 10,
                callbacks: callbacks,
                pagination: {span: 5, next_text: 'Prox &rarr;', prev_text: '&larr; Ant'}
            },
            datax
        );

        // Jquery plugin
        //$('#stream_table').stream_table({view: view}, datax)

        $('.record_count .badge').text(datax.length);



    });

    $('#stream_table tr > td').live('click',function(){

        //$('#transacaoid').html("<span style='font-size:8px';>TRANSA��O ID:</span><br/>"+$(this).attr('transacaoid'));
        var url = "http://bodysystems.net/_ferramentas/dashboard-iso/toolbar/pagto_details.php";
        var transa = $(this).attr('transacaoid');
        var orig = $(this).attr('origem');

        $.ajax({
            type: "POST",
            url: url,
            data: {transacaoid:transa,origem:orig}, // serializes the form's elements.
            dataType: "html" ,
            beforeSend: function(load) {
                $('#frame_detail').html("<div style='margin-top:30px;margin-left:20px;'><img src='http://bodysystems.net/_ferramentas/workshop/images/activity.gif' /></div>");

            } ,
            success: function(data)
            {

                $('#close').hide();
                $('#voltar_user_list').show();
                $('#frame_detail').html(data)

            } ,
            error: function (request, status, error)
            {
                $('#frame_detail').html('<span style="color: red">N�o foi possivel gerar o bot�o para pagamento.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
            }
        });
        $('#user_list').fadeOut();
        $('#pagto_detalhes').fadeIn();
    });

    $('#voltar_user_list').click(function(){
        $('#close').show();
        $(this).hide();
        $('#pagto_detalhes').fadeOut();

        $('#user_list').fadeIn();
    });

    $('#despacho').click(function(){
       getEnvio();
    });

    $('#acao').change(function(){
       if($('#acao').is(':checked')) {
           
           $('#ativo')
            .removeClass('stop')
            .addClass('go')
            //.html('ENVIO ATIVO');
            if($('#dta_liberacao').html()=='') {
                setEnvio();
            } else {
                upEnvio();
            }
              
             /*
            var d1=new Date();
            var mydate = d1.toLocaleDateString()+ " " + d1.toLocaleTimeString()
           
           $('#dta_liberacao').html('Data de libera��o:<br/>'+ mydate);
           $('#dta_alteracao').html('Data de altera��o:<br/>'+mydate);
           $('#alteracao')
            .html('ENVIO PRIMEIRO LOTE')
            .attr('alteracao','1');
            */

       } else {
           $('#ativo')
           .removeClass('go')
            .addClass('stop')
            //.html('ENVIO INATIVO');
            upEnvio();

       }
    });
});

function getContent(name) {
    var url = "http://bodysystems.net/_ferramentas/dashboard-iso/toolbar/getContent.php"
    $.ajax({
        type: "POST",
        url: url,
        data: {item:name}, // serializes the form's elements.
        dataType: "html" ,
        beforeSend: function(load) {
            $('#response').html("<div style='margin-top:30px;margin-left:20px;'><img src='http://bodysystems.net/_ferramentas/workshop/images/activity.gif' /></div>");

        } ,
        success: function(data)
        {
            $('#response').html(data);

        } ,
        error: function (request, status, error)
        {
            $('#response').html('<span style="color: red">N�o foi possivel gerar o bot�o para pagamento.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
        }
    });
}

function uitoggle(vitem) {

    if (vitem =='home'){
        $('.st_pagination').remove();
        $('[name=search]').remove();
        $('[name=per_page]').remove();
        $('#user_list').hide();
         $('#gw_stream_table').hide();
        $('#groundw_list').hide();
        $('#dashboard_response').hide();
        $('#despacho_response').hide();
        $('#change_page').hide();
        $('#start_page').show();

    }
    if (vitem == 'client'){
        $('.st_pagination').remove();
        $('[name=search]').remove();
        $('[name=per_page]').remove();
        $('#start_page').hide();
        $('#dashboard_response').hide();
        $('#gw_stream_table').hide();
        $('#groundw_list').hide();
        $('#despacho_response').hide();
        $('#change_page').hide();
        $('#stream_table').fadeIn();
        $('#user_list').fadeIn();
    }
    if (vitem == 'dash'){
        $('.st_pagination').remove();
        $('[name=search]').remove();
        $('[name=per_page]').remove();
        $('#start_page').hide();
        $('#stream_table').hide();
        $('#user_list').hide();
        $('#gw_stream_table').hide();
        $('#groundw_list').hide();
        $('#despacho_response').hide();
        $('#change_page').hide();
        $('#dashboard_response').fadeIn();
    }

    if (vitem == 'groundw'){
        $('.st_pagination').remove();
        $('[name=search]').remove();
        $('[name=per_page]').remove();
        $('#user_list').hide();
        $('#start_page').hide();
        $('#dashboard_response').hide();
        $('#stream_table').hide();
        $('#despacho_response').hide();
        $('#change_page').hide();
        $('#gw_stream_table').fadeIn();
        $('#groundw_list').fadeIn();
    }
    if (vitem == 'despacho'){
        $('.st_pagination').remove();
        $('[name=search]').remove();
        $('[name=per_page]').remove();
        $('#user_list').hide();
        $('#start_page').hide();
        $('#dashboard_response').hide();
        $('#stream_table').hide();
        $('#gw_stream_table').hide();
        $('#groundw_list').hide();
        $('#change_page').hide();
        $('#despacho_response').fadeIn();
    }
    if (vitem == 'change'){
        $('.st_pagination').remove();
        $('[name=search]').remove();
        $('[name=per_page]').remove();
        $('#user_list').hide();
        $('#start_page').hide();
        $('#dashboard_response').hide();
        $('#stream_table').hide();
        $('#gw_stream_table').hide();
        $('#groundw_list').hide();
        $('#change_page').hide();
        $('#despacho_response').hide();
        $('#change_page').show();
    }

}

function number_format (number, decimals, dec_point, thousands_sep) {

    number = (number + '').replace(/[^0-9+-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function (n, prec) {
        var k = Math.pow(10, prec);
        return '' + Math.round(n * k) / k;
    };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/B(?=(?:d{3})+(?!d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}


function getEnvio() {
    var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/envio_data_source.php";
    var evento_id = $('#tb_eventoid').val();
    var $task = 'get';
    $.ajax({
        type: "POST",
        url: url,
        data: {eventoid:evento_id,task:$task}, // serializes the form's elements.
        dataType: "json" ,
        beforeSend: function(load) {
            $('#controle_response').html("<div style='margin-top:30px;margin-left:20px;'><img src='http://bodysystems.net/_ferramentas/workshop/images/activity.gif' /></div>");

        } ,
        success: function(data)
        {
            ;
            $('#controle_response').html('');
            if(data!==null) {
                $('#info_controle').show();
                $('#dta_liberacao').html('Data de libera��o:<br/>' + data['dta_liberacao']) ;
                $('#dta_alteracao').html('Data de altera��o:<br/>' + data['dta_alteracao']) ;
                if(data['ativo']== 1) {
                        $('#ativo').removeClass('stop').addClass('go').html('ENVIO ATIVO');
                        $('#acao').attr('checked',true);
                } else {
                        $('#ativo').removeClass('go').addClass('stop').html('ENVIO INATIVO');
                        $('#acao').attr('checked',false);
                } ;

                $('#alteracao').html(data['alteracao']) ;
                $('#info').show();
            } else {
                $('#dta_liberacao').html('') ;
                $('#dta_alteracao').html('') ;
                $('#ativo').removeClass('go').addClass('stop');
                $('#acao').attr('checked',false);
                $('#alteracao').html('') ;
                $('#info').hide();
                $('#ativo').removeClass('go').addClass('stop').html('ENVIO INATIVO');
                $('#acao').attr('checked',false);
            }
        } ,
        error: function (request, status, error)
        {
            $('#controle_response').html('<span style="color: red">N�o foi possivel gerar o bot�o para pagamento.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
        }
    });
}

function setEnvio(){
    var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/envio_data_source.php";
    var evento_id = $('#tb_eventoid').val();
    var $nsa = $('#tb_nsaid').val();
    var $task = 'set';
    $.ajax({
        type: "POST",
        url: url,
        data: {eventoid:evento_id,task:$task,nsa:$nsa}, // serializes the form's elements.
        dataType: "json" ,
        beforeSend: function(load) {
            $('#controle_response').html("<div style='margin-top:30px;margin-left:20px;'><img src='http://bodysystems.net/_ferramentas/workshop/images/activity.gif' /></div>");

        } ,
        success: function(data)
        {
            
            $('#controle_response').html(data[1]);
            if(data[0]==1) {
                getEnvio();
            }
        } ,
        error: function (request, status, error)
        {
            $('#controle_response').html('<span style="color: red">N�o foi possivel gerar o bot�o para pagamento.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
        }
    });
}

function upEnvio(){
    var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/envio_data_source.php";
    var evento_id = $('#tb_eventoid').val();
    var $nsa = $('#tb_nsaid').val();
    var $task = 'update';
    var $ativo = 0;
    if($('#ativo').hasClass('go')){
        $ativo = 1;
    } else {
        $ativo = 0;
    }
    $.ajax({
        type: "POST",
        url: url,
        data: {eventoid:evento_id,task:$task,ativo:$ativo}, // serializes the form's elements.
        dataType: "json" ,
        beforeSend: function(load) {
            $('#controle_response').html("<div style='margin-top:30px;margin-left:20px;'><img src='http://bodysystems.net/_ferramentas/workshop/images/activity.gif' /></div>");

        } ,
        success: function(data)
        {
            
            $('#controle_response').html(data[1]);
            if(data[0]==1) {
                getEnvio();
            }
            
        } ,
        error: function (request, status, error)
        {
            $('#controle_response').html('<span style="color: red">N�o foi possivel gerar o bot�o para pagamento.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
        }
    });
}
