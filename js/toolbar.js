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


    });

    $('#home').click(function(){
        uitoggle('home');
    });

    $('#dash').click(function(){
        var options = {
            title: {
                text: 'Movimentação Financeira'
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
                    return 'Referência: <b>'+ this.x +
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

    $('#stream_table .GOPAGTO').live('click',function(){
        //$('#stream_table tr > td').live('click',function(){

        //$('#transacaoid').html("<span style='font-size:8px';>TRANSAÇÃO ID:</span><br/>"+$(this).attr('transacaoid'));
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
                $('#frame_detail').html('<span style="color: red">Não foi possivel gerar o botão para pagamento.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
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

             $('#dta_liberacao').html('Data de liberação:<br/>'+ mydate);
             $('#dta_alteracao').html('Data de alteração:<br/>'+mydate);
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

    $('#change').click(function(){
        var evento_id = $('#tb_eventoid').val();
        $("#lst_evento").html('');
        $('#lst_evento').append($('#lst_evento_tpl').html());
        $("#lst_cliente").html('<option value="-1">Selecione o Cliente</option>');


        var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/client_data_source.php";
        $.ajax({
            type: "POST",
            url: url,
            dataType: "json" ,
            data: {eventoid:evento_id},
            beforeSend: function(load) {

            } ,
            success: function(data)
            {

                $.each(data,function(i){
                    if(data[i]['status']=='PAGO') {
                        var nome = data[i]['nome_evo'];
                        var transacaoid  = data[i]['transacaoid'];
                        var origem  = data[i]['origem'];
                        var userid  = data[i]['userid'];
                        var evoid  = data[i]['idcliente'];

                        var sdata = evento_id +';'+transacaoid +';'+ origem +';'+ userid +';'+ evoid ;

                        $('#lst_cliente').append('<option value='+ sdata +'>'+ nome.toUpperCase()+'</option>');
                    }

                });

                $('#lst_cliente').chosen('detroy');
                $('#lst_cliente').chosen();

            } ,
            error: function (request, status, error)
            {
                var msg = '<span style="color: red">Não foi possivel fechar este lote.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>' ;
                $('#change_response').html(msg) ;

            }
        });

        var listItems = $("#container li div");
        listItems.each(function(idx, li) {
            var programa = $(li);
            var id_evento = programa.children('#programa').html();
            var nome = programa.children('p[name*=nome_evento]').html();
            var cidade = programa.children('#cidade').html();
            var data = programa.children('#numero').html();
            var evento_desc = nome.toUpperCase() + ' ' + cidade.toUpperCase() + ' ' + ' EM '+ data;

            $('#lst_evento').append('<option value="'+ id_evento+';'+ evento_desc +'">'+ evento_desc +'</option>');

            // and the rest of your code
        });

        $('#lst_evento').chosen('destroy');
        $('#lst_evento').chosen();
        uitoggle('change');
    });

    // INFORMA QUAL É O CLIENTE
    $('#lst_cliente').change(function(){
        //$('#change_response').html($('#lst_cliente').val());
        $('#post-de').html($('#lst_cliente').val())


    });

    //INFORMA O NOVO CURSO (OU LIMBO)
    $('#lst_evento').change(function(){
        //$('#change_response').append('<br/>'+ $('#lst_evento').val());
        $('#post-para').html($('#lst_evento').val())

    });


//LISTA DE ESPERA

    $('#li_les').live('click',function(){


        //RECUPERA NOMES NO LIMBO
        var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/limbo_data_source.php";
        $.ajax({
            type: "POST",
            url: url,
            dataType: "json" ,
            async:  false,
            beforeSend: function(load) {

            } ,
            success: function(data)
            {
                $("#lst_cliente_espera").html('<option value="-1">Selecione o Cliente</option>');
                $("#lst_evento_espera").html('<option value="-1">Selecione o Evento</option>');
                $.each(data,function(i){
                    if(data[i]['status']=='PAGO') {
                        var nome = data[i]['nome_evo'];
                        var transacaoid  = data[i]['transacaoid'];
                        var origem  = data[i]['origem'];
                        var userid  = data[i]['userid'];
                        var evoid  = data[i]['idcliente'];

                        var sdata = transacaoid +';'+ origem +';'+ userid +';'+ evoid ;

                        $('#lst_cliente_espera').append('<option value='+ sdata +'>'+ nome.toUpperCase()+'</option>');
                    }

                });

            } ,
            error: function (request, status, error)
            {
                var msg = '<span style="color: red">Não foi possivel carregar as informações.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>' ;
                $('#change_response').html(msg) ;

            }
        });

        //RECUPERA EVENTOS NO DASHBOARD
        var listItems = $("#container li div");
        listItems.each(function(idx, li) {
            var programa = $(li);
            var id_evento = programa.children('#programa').html();
            var nome = programa.children('p[name*=nome_evento]').html();
            var cidade = programa.children('#cidade').html();
            var data = programa.children('#numero').html();
            var evento_desc = nome.toUpperCase() + ' ' + cidade.toUpperCase() + ' ' + ' EM '+ data;

            $('#lst_evento_espera').append('<option value="'+ id_evento+';'+ evento_desc +'">'+ evento_desc +'</option>');


            // and the rest of your code
        });

        //$('#lst_evento_espera').chosen('destroy').html('');
        //$('#lst_cliente_espera').chosen('destroy').html('');

        $.fancybox($("#li_les"),{
            href : '#lista_espera',
            type: 'inline',
            maxWidth    : 380,
            maxHeight    : 560,
            padding     : 10,
            fitToView    : true,
            modal        : true,
            locked        : true,
            width        : 420,
            height        : 560,
            autoSize    : false,
            closeClick    : false,
            openEffect    : 'elastic',
            closeEffect    : 'elastic',
            helpers : {
                overlay : {
                    css : {
                        'background' : 'rgba(0, 0, 0, 0.80)'
                    }
                }
            }
        });






        $('#lst_evento_espera').chosen();
        $('#lst_cliente_espera').chosen();

    });


    $('#li_ope').live('click',function(){
        $.fancybox($("#li_ope"),{
            href : '#frame_cupom',
            type: 'inline',
            maxWidth    : 560,
            maxHeight    : 400,
            padding     : 0,
            fitToView    : true,
            modal        : true,
            locked        : true,
            width        : 560,
            height        : 400,
            autoSize    : false,
            closeClick    : false,
            openEffect    : 'elastic',
            closeEffect    : 'elastic',
            helpers : {
                overlay : {
                    css : {
                        'background' : 'rgba(0, 0, 0, 0.80)'
                    }
                }
            }
        });
    })


    $('#li_bol').live('click',function(){
        $.fancybox($("#li_bol"),{
            href : '#boleto_frame',
            type: 'inline',
            maxWidth    : 580,
            maxHeight    : 410,
            padding     : 0,
            fitToView    : true,
            modal        : true,
            locked        : true,
            width        : 580,
            height        : 410,
            autoSize    : false,
            closeClick    : false,
            openEffect    : 'elastic',
            closeEffect    : 'elastic',
            helpers : {
                overlay : {
                    css : {
                        'background' : 'rgba(0, 0, 0, 0.80)'
                    }
                }
            }
        });
    })


    $('#li_oper').live('click',function(){
        $.fancybox($("#li_oper"),{
            href : '#frame',
            type: 'inline',
            maxWidth    : 840,
            maxHeight    : 568,
            padding     : 5,
            fitToView    : true,
            modal        : true,
            locked        : true,
            width        : 840,
            height        : 568,
            autoSize    : false,
            closeClick    : false,
            onComplete : function(){
                $('#slider').dateRangeSlider('resize');
            },
            openEffect    : 'elastic',
            closeEffect    : 'elastic',
            helpers : {
                overlay : {
                    css : {
                        'background' : 'rgba(0, 0, 0, 0.80)'
                    }
                }
            }

        });
    })


    //COMBOS DA LISTA DE ESPERA
    $('.clsEspera').change(function(){

        if ($(this).attr('id') != 'lst_cliente_espera') {

            $('#post-to').append($(this).val());
        } else {

            $('#post-who').append($(this).val());
        }

        //$('#espera_response').append('<br/>'+$(this).val());
    })

    $('.close').click(function(){
        $.fancybox.close();
        $('#lst_evento_espera').chosen('destroy').html('');
        $('#lst_cliente_espera').chosen('destroy').html('');
        //$('.chosen').chosen('destroy');


    });

    $('.op_close').click(function(){
        $.fancybox.close();
        $('#lst_evento_espera').chosen('destroy').html('');
        $('#lst_cliente_espera').chosen('destroy').html('');
        //$('.chosen').chosen('destroy');


    });

    $('#aplicar_escala').click(function(){
        setEscala(1);
    })

    $('#criar_cupom').click(function(){
        //RECUPERA NOMES NO LIMBO
        var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/create_voucher.php";
        var nome = $('#nome').val();
        var email = $('#email').val();
        var cpf = $('#cpf').val();

        $.ajax({
            type: "POST",
            url: url,
            dataType: "json" ,
            data: {nome:nome,email:email,cpf:cpf},
            async:  false,
            beforeSend: function(load) {
                $('#voucher_response').html('Aguarde, processando...')
            } ,
            success: function(data)
            {
                if(data[0][0]==0) {
                    $('#voucher_response').html('<div style="font-size:18px;">'+data[0][1]+'</div><div style="font-size: 12px;">'+data[1]+'</div>');
                } else {
                    //error
                    $('#voucher_response').html('<div>'+data[0][1]+'</div><div style="font-size: 12px;">'+data[1]+'</div>');
                }
            } ,
            error: function (request, status, error)
            {
                var msg = '<span style="color: red">Não foi possivel carregar as informações.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>' ;
                $('#voucher_response').html(msg) ;


            }
        });
    });

    $('#consultar_cpf').click(function(){
        //RECUPERA NOMES NO LIMBO
        var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/consulta_cpf.php";
        var cpf = $('#cpf_sacado').val();

        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json' ,
            data: {cpf_sacado:cpf},
            beforeSend: function(load) {
                $('#consulta_lcol').append('<div id="loader" style="margin-top: 80px; text-align: center" ><img src="img/loading.gif" /></div>');
            } ,
            success: function(data)
            {

                if(data[1]==0){
                    alert('Cliente não cadastrado no EVO. Impossível gerar boleto.');
                    $('#criar_boleto').hide();
                } else {

                    $('#loader').remove();
                    $('#userid').val(data[0]);
                    $('#evoid').val(data[1]);
                    $('#sacado').val(data[2]);
                    $('#endereco').val(data[3]);
                    $('#criar_boleto').show();
            }
            } ,
            error: function (request, status, error)
            {
                var msg = '<span style="color: red">Não foi possivel gerar o boleto.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>' ;

            }
        });

    });

    $('#criar_boleto').click(function(){
        //PROCESSA O  BOLETO
        /*
         $docnum =           $_POST['evoid'];
         $userid =           $_POST['userid']
         $cpf =              $_POST['cpf'];
         $nome =             $_POST['nome'];
         $endereco =         $_POST['endereco'];
         $valor_cobrado =    $_POST['valor_cobrado'] ;
         */

        var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/boleto_strap.php";
        var $nome = $('#sacado').val();
        var $cpf = $('#cpf_sacado').val();
        var $endereco = $('#endereco').val();
        var $valor_cobrado = $('#valor_cobrado').val();
        var $evoid = $('#evoid').val();
        var $userid = $('#userid').val();
		var $data_vcto = $('#data_vcto').val();

        $.ajax({
            type: "POST",
            url: url,
            dataType: "html" ,
            data: {userid:$userid,evoid:$evoid,cpf:$cpf,nome:$nome,endereco:$endereco,valor_cobrado:$valor_cobrado,data_vcto:$data_vcto},
            beforeSend: function(load) {
                $('#consulta_lcol').append('<div id="loader" style="margin-top: 80px; text-align: center" ><img src="img/loading.gif" /></div>');
            } ,
            success: function(data)
            {
                $('#loader').remove();
                $('#boleto_response').html(data)

            } ,
            error: function (request, status, error)
            {
                $('#loader').remove();
                var msg = '<span style="color: red">Não foi possivel gerar o boleto.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>' ;
                $('#boleto_response').html(msg);

            }
        });

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
            $('#response').html('<span style="color: red">Não foi possivel gerar o botão para pagamento.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
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
                $('#dta_liberacao').html('Data de liberação:<br/>' + data['dta_liberacao']) ;
                $('#dta_alteracao').html('Data de alteração:<br/>' + data['dta_alteracao']) ;
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
            $('#controle_response').html('<span style="color: red">Não foi possivel realizar a tarefa.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
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
            $('#controle_response').html('<span style="color: red">Não foi possivel registrar o envio.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
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
            $('#controle_response').html('<span style="color: red">Não foi possivel atualizar a informação.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
        }
    });
}

function goTransfer(){
    var vDados_de = $('#post-de').text();
    var vDados_para = $('#post-para').text() ;
    var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/transfere_cliente_treino.php";
    $.ajax({
        type: "POST",
        url: url,
        dataType: "html" ,
        data: {dados_de:vDados_de,dados_para:vDados_para},
        beforeSend: function(load) {
            $('#change_response').html('Por favor, aguarde...');
        } ,
        success: function(data)
        {

            $('#change_response').html(data);

        } ,
        error: function (request, status, error)
        {
            var msg = '<span style="color: red">Não foi possivel transferir este cliente.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>' ;
            $('#change_response').html(msg) ;

        }
    });

}

function goTransferLimbo(){
    var vDados_who = $('#post-who').text();
    var vDados_to = $('#post-to').text() ;
    var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/limbo_out.php";
    $.ajax({
        type: "POST",
        url: url,
        dataType: "html" ,
        data: {dados_who:vDados_who,dados_to:vDados_to},
        beforeSend: function(load) {
            $('#change_response').html('Por favor, aguarde...');
        } ,
        success: function(data)
        {

            $('#espera_response').html(data);

        } ,
        error: function (request, status, error)
        {
            var msg = '<span style="color: red">Não foi possivel transferir este cliente.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>' ;
            $('#change_response').html(msg) ;

        }
    });

}

function goT($item,$caption) {
    //FUNCAO PARA PAINEL DE OPERAÇOES E CONFIGURAÇÕES
    $('#boletos_frame').hide();
    $('#change_page').hide();
    $('#lista_espera').hide();
    $('#boleto_frame').hide();
    $('#frame_cupom').hide();
    $('#config').hide();

    $('#'+$item).fadeIn();
    $('#op_head').html($caption);
    if($item =='config') {
        $('#slider').dateRangeSlider('resize');
    }

}