String.prototype.replaceAll = function(str1, str2, ignore)
{
   return this.replace(new RegExp(str1.replace(/([\,\!\\\^\$\{\}\[\]\(\)\.\*\+\?\|\<\>\-\&])/g, function(c){return "\\" + c;}), "g"+(ignore?"i":"")), str2);
};

$(document).ready(function() {
    
    $.datepicker.regional['pt-BR'] = {
                closeText: 'Fechar',
                prevText: '&#x3c;Anterior',
                nextText: 'Pr&oacute;ximo&#x3e;',
                currentText: 'Hoje',
                monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho',
                'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun',
                'Jul','Ago','Set','Out','Nov','Dez'],
                dayNames: ['Domingo','Segunda-feira','Ter&ccedil;a-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sabado'],
                dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
                dayNamesMin: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
                weekHeader: 'Sm',
                dateFormat: 'dd/mm/yy',
                firstDay: 0,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''};
    $.datepicker.setDefaults($.datepicker.regional['pt-BR']);
    var $dta_envio = '';
    
    loadUI();
    window.setInterval(loadUI,180000);
    $('#ex_elements').css('height', window.innerHeight - 30);

    $(".fb").fancybox({
        maxWidth    : 310,
        maxHeight    : 320,
        fitToView    : false,
        width        : 310,
        height        : 320,
        autoSize    : false,
        closeClick    : false,
        openEffect    : 'elastic',
        modal         : true,
        closeEffect    : 'elastic',
        scrolling     : 'no',
        padding : '5px',
        backgroundcolor: '#333',
        beforeLoad: function() {
            var fmt_title = this.title;
            fmt_title = fmt_title.replaceAll(':','').replaceAll('-','').replaceAll(' | ','');
            $dta_envio = $('#'+fmt_title).attr('data-envio');
            //console.log($dta_envio);
            if($dta_envio=='0') {
                $('#label_enviado').text('SALVAR COMO ENVIADO EM') ;
            } else {
                $('#label_enviado').text('ENVIO JÁ REGISTRADO EM');
                $('#dta_enviado').text($dta_envio);
                $('#datepicker').attr('envio',$dta_envio);
                $('#datepicker').datepicker({dateFormat: 'dd-mm-yyyy'}).datepicker('setDate', $dta_envio);
                $('#btnEnvio').hide();
            }
            $('#datepicker').attr('lote',this.title);
            
            //$('#datePicker').datepicker('refresh');
        },
    helpers : {
        title: null
    }
    });

    $('#criar_lote').click(function(){
        var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/fechar_lote.php";
        $.ajax({
            type: "POST",
            url: url,
            dataType: "html" ,
            beforeSend: function(load) {
                $('#lotes').html("<div style='margin-top:30px;margin-left:20px;'><img src='http://bodysystems.net/_ferramentas/workshop/images/activity.gif' /></div>");

            } ,
            success: function(data)
            {
                loadUI();
                $.notify({title:'SUCESSO!', text:data, icon:'img/dialog-information.png'});

            } ,
            error: function (request, status, error)
            {
                $('#lotes').html('<span style="color: red">Não foi possivel fechar este lote.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
            }
        });
    });
    
    $('#datepicker').datepicker({
        dateFormat: 'dd-mm-yy',
        onSelect: function(selected,evnt) {
            $('#dta_enviado').text(selected);
            var $env = evnt.selectedYear+'-'+evnt.selectedMonth+1+'-'+evnt.selectedDay+' 00:00:00';
            $(this).attr('enviado',$env);
        }
    });

    $('#btnEnvio').click(function(){
        var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/set_envio.php";
        var $lote = $('#datepicker').attr('lote') ;
        var $enviado = $('#datepicker').attr('enviado');
        
        if(!$enviado) {
            alert('Escolha uma data');
        } else {

            $.ajax({
                type: "POST",
                url: url,
                dataType: "text" ,
                data:{lote:$lote,enviado:$enviado},
                beforeSend: function(load) {
                    $('#p-response').html("<div style='margin-top:30px;margin-left:20px;'><img src='http://bodysystems.net/_ferramentas/workshop/images/activity.gif' /></div>");

                } ,
                success: function(data)
                {
                    $('#picker').hide();
                    if(data=='SUCCESS') {
                        $('#p-response').text('DADOS GRAVADOS COM SUCESSO!');
                        $('#response').addClass('envio-ok').fadeIn();
                    } else {
                        $('#p-response').text('OCORRERAM PROBLEMAS REGISTRANDO O LOTE.');
                        $('#response').addClass('envio-error').fadeIn();
                    }

                } ,
                error: function (request, status, error)
                {
                    alert( request.responseText + "\n" + status);
                }
            });
        }

    });

    $('#close-hs').click(function(){
        $.fancybox.close()
        $('#response').removeClass().hide();
        $('#picker').show()
    });

    $('#btnCancelEnvio').click(function(){
        $.fancybox.close()
        $('#response').removeClass().hide();
        $('#picker').show()
    });
    

});

$(window).load(function(){
    $('#ex_elements').css('height', window.innerHeight - 30);
    $('#frame_list').css('height', window.innerHeight - 230);
});

window.onresize = function(event) {
    $('#ex_elements').css('height', window.innerHeight - 30);
    $('#frame_list').css('height', window.innerHeight - 230);
}



function loadUI() {
    var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/lote_data_source.php";
    $.ajax({
        type: "POST",
        url: url,
        dataType: "html" ,
        beforeSend: function(load) {
            $('#ex_elements').html("<div style='margin-top:30px;margin-left:20px;'><img src='http://bodysystems.net/_ferramentas/workshop/images/activity.gif' /></div>");

        } ,
        success: function(data)
        {

            $('#ex_elements').html(data);
        } ,
        error: function (request, status, error)
        {
            $('#ex_elements').html('<span style="color: red">Não foi possivel gerar o botão para pagamento.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
        }
    });

    var url2 = "http://bodysystems.net/_ferramentas/dashboard-iso/services/load_lotes.php";
    $.ajax({
        type: "POST",
        url: url2,
        dataType: "html" ,
        beforeSend: function(load) {
            $('#lotes').html("<div style='margin-top:30px;margin-left:20px;'><img src='http://bodysystems.net/_ferramentas/workshop/images/activity.gif' /></div>");

        } ,
        success: function(data)
        {

            $('#lotes').html(data);
        } ,
        error: function (request, status, error)
        {
            $('#lotes').html('<span style="color: red">Não foi possivel carregar os lotes já fechados.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
        }
    });
}


function getLote($tfile){
    var $file = $tfile;
   // console.log($file);
    var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/etiquetas_data_source.php";
    $.ajax({
        type: "POST",
        url: url,
        dataType: "json" ,
        data: {arquivo:$file},
        beforeSend: function(load) {

        } ,
        success: function(data)
        {
            var win = window.open();
            win.document.write(data);
        } ,
        error: function (request, status, error)
        {
            var msg = '<span style="color: red">Não foi possivel fechar este lote.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>' ;
            var win = window.open();
            win.document.write(msg);

        }
    });
} ;
