$(document).ready(function(){

    window.setInterval(startRefresh,180000);
    var container = $("#container");

    container.isotope({
        itemSelector : 'ul#container > li',
        layoutMode : 'fitRows'
    });

    $.filtrify("container", "placeHolder", {
        close    : true,
        hide     : false,
        /*query :{"mes" : ["10"]}, */
        callback : function ( query, match, mismatch ) {
            container.isotope({ filter : $(match) });
        }
    });


    //ITENS OPERACIONAIS DO MENU
    //$operacoes = '<div id="mnu_content" class="context">Operacoes</div>';

    $('.ft-menu').append('<li id="li_oper" style="float:right;margin-right:10px;font-weight: bold;cursor:pointer;"><span class="icon-tasks">Op&ccedil;&otilde;es</span></li>');
    /*

    //$('.ft-menu').append($operacoes);
    $('.ft-menu').append('<li id="li_oper" style="float:right;margin-right:10px;font-weight: bold;cursor:pointer;"><span class="icon-tasks">|=|</span></li>');
    $('.ft-menu').append('<li id="li_les" style="float:right;margin-right:10px;font-weight: bold;cursor:pointer;">&#187; Lista de Espera</li>');
    $('.ft-menu').append('<li id="li_ope" style="float:right;margin-right:10px;font-weight: bold;cursor:pointer;"> &#187; Cupom</li>');
    $('.ft-menu').append('<li id="li_bol" style="float:right;margin-right:10px;font-weight: bold;cursor:pointer;"> &#187; Boletos</li>');

    */

    $('#data_vcto').datetimepicker({
            lang:'pt',
            timepicker:false,
            format:'d/m/Y',
            formatDate:'d/m/Y',
            mask:'39/19/9999'
    }); 


    $('.fb').click(function(){
        $('.toolbar').each(function(){
            $(this).removeClass('selected');
            $(this).attr('src',$(this).attr('off'));
        });
        $('#home').addClass('selected');
        $('#home').attr('src','toolbar/home_on.png');


        $('#start_page').show();
        $.fancybox($(this),{
            href : '#div_frame',
            type: 'inline',
            maxWidth    : 710,
            maxHeight    : 585,
            padding     : 10,
            fitToView    : true,
            modal        : true,
            locked        : true,
            width        : 710,
            height        : 585,
            autoSize    : false,
            closeClick    : false,
            openEffect    : 'fade',
            closeEffect    : 'elastic',
            helpers : {
                overlay : {
                    css : {
                        'background' : 'rgba(0, 0, 0, 0.80)'
                    }
                }
            }
        });

        $('#frm_programas')[0].reset();
        $('#response').html('');
        $('#head').text('WS '+($(this).closest("div").children('#cidade').text()));
        $('#local').text($(this).closest("div").children('#endereco').text());
        $('#id_evento').text($(this).closest("div").children('#programa').text());
        $('#tb_nsaid').val($(this).closest("div").attr('data-nsa'));
        $('#dta_evento').text('Realiza??o: '+$(this).closest("div").attr('data-evento'));
        $('#eventoid').val($('#id_evento').text());
        $('#descricao').val($('#head').text());
        $('#data_evento').val($(this).closest("div").attr('data-evento'));
        $('#tb_eventoid').val($('#id_evento').text());

        //-----START PAGE ------------------
        var endereco = $(this).closest("div").children('[name=endereco]').text();
        var local = $(this).closest("div").children('[name=local]').text();
        var nome_evento = $(this).closest("div").children('[name=nome_evento]').text();
        $('#desc_ev').html("<div class='startpage'><span style='font-size:24px;'>"+nome_evento+"</span><br/><span id='span_local' style='font-size:14px;'>"+local+"<br/><span id='span_endereco' style='font-size:14px;'>"+endereco+"</span></div>") ;
        setEscala(0);
        map = new GMaps({
            el: '#map_ev',
            lat: -23.4621650,
            lng: -46.8742740
        });
        var $cidade = $(this).closest("div").children('#cidade').text();
        var $ende = endereco +','+ $cidade +',Brazil';
        $ende.replace('-',',');
        GMaps.geocode({
            address: $ende, //"Rua Coral,71,Guarulhos,Brasil",
            callback: function(results, status){
                if(status=='OK'){
                    var latlng = results[0].geometry.location;
                    map.setCenter(latlng.lat(), latlng.lng());
                    map.addMarker({
                        lat: latlng.lat(),
                        lng: latlng.lng()
                    });
                }
            }
        });

        //-----END START PAGE


    });

    $('#close').click(function(){
        // NECESSARIO REMOVER ESTES ELEMENTOS PARA NãO DUPLICA-LOS NA PROXIMA CHAMADA
        $.fancybox.close();
        $('.st_pagination').remove();
        $('[name=search]').remove();
        $('[name=per_page]').remove();
        $('#user_list').hide();
        $('#dashboard_response').hide();
        $('#gw_stream_table').hide();
        $('#groundw_list').hide();
        $('#despacho_response').hide();
        $('#info').hide();
        $('#change_page').hide();

        //EXPEDI??O
        $('#dta_liberacao').html('') ;
        $('#dta_alteracao').html('') ;
        $('#ativo').removeClass('go').addClass('stop');
        $('#acao').attr('checked',false);
        $('#alteracao').html('') ;
        $('#info').hide();
        $('#ativo').removeClass('go').addClass('stop').html('ENVIO INATIVO');
        $('#acao').attr('checked',false);


        $('#lst_cliente').chosen('destroy');
        $('#lst_evento').chosen('destroy');


    });

    $('#cancel').click(function(){
        $.fancybox.close();

    });

    $('#apply').click(function(){
        var url = "http://bodysystems.net/_dev/iso/service/data_source.php";
        $.ajax({
            type: "POST",
            url: url,
            data: $('#frm_programas').serialize(), // serializes the form's elements.
            dataType: "html" ,
            beforeSend: function(load) {
                $('#response').html("<img src='http://bodysystems.net/_ferramentas/workshop/images/activity.gif' />");
            } ,
            success: function(data)
            {
                if(data=='Success') {
                    $.fancybox.close();
                } else {
                    $('#response').html(data);
                }
            } ,
            error: function (request, status, error)
            {
                $('#response').html('<span style="color: red">Não foi possivel salvar a informação.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
            }
        });

    });

});

$(window).load(function(){
    //
    $("#slider").dateRangeSlider();
});

function startRefresh() {
    if ( $('#div_frame').css('display') == 'none' ){
        location.reload();
    }
}

function setEscala($task) {
    var evento_id = $('#tb_eventoid').val();
    var professor = $('#treinador').val();
    var valor = $('#pagto_treinador').val();
    var avisado = $('#aviso_escala').prop('checked');
    var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/escala.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {eventoid:evento_id,professor:professor,valor:valor,avisado:avisado,task:$task}, // serializes the form's elements.
        dataType: "json" ,
        beforeSend: function(load) {

            $('#treinador').removeClass('ok error');

        } ,
        success: function(data)
        {
            if($task==1) {
                $('#treinador').addClass('ok');
                //alert(data[1]);
            } else {
                $('#treinador').val(data[1]);
                $('#pagto_treinador').val(data[2]);
                if(data[3]==1){
                    $('#aviso_escala').attr('checked',true);
                } else {
                    $('#aviso_escala').attr('checked',false);
                }
            }


        } ,
        error: function (request, status, error)
        {
            $('#treinador').addClass('error');
            alert("Não foi possivel realizar a tarefa\n" + request.responseText + "\n" + status);
        }
    });


}

// --------------------- #

function unformatNumber(pNum)
{
    return String(pNum).replace(/\D/g, "").replace(/^0+/, "");
}
function isCpf(cpf) {
	var soma;
	var resto;
	var i;
	 
	 if ( (cpf.length != 11) ||
	 (cpf == "00000000000") || (cpf == "11111111111") ||
	 (cpf == "22222222222") || (cpf == "33333333333") ||
	 (cpf == "44444444444") || (cpf == "55555555555") ||
	 (cpf == "66666666666") || (cpf == "77777777777") ||
	 (cpf == "88888888888") || (cpf == "99999999999") ) {
		return false;
	 }
	 
	 soma = 0;
	 
	 for (i = 1; i <= 9; i++) {
		soma += Math.floor(cpf.charAt(i-1)) * (11 - i);
	 }
	 
		resto = 11 - (soma - (Math.floor(soma / 11) * 11));
	 
	 if ( (resto == 10) || (resto == 11) ) {
		resto = 0;
	 }
	 
	 if ( resto != Math.floor(cpf.charAt(9)) ) {
		return false;
	 }
	 
		soma = 0;
	 
	 for (i = 1; i<=10; i++) {
		soma += cpf.charAt(i-1) * (12 - i);
	 }
	 
		resto = 11 - (soma - (Math.floor(soma / 11) * 11));
	 
	 if ( (resto == 10) || (resto == 11) ) {
		resto = 0;
	 }
	 
	 if (resto != Math.floor(cpf.charAt(10)) ) {
		return false;
	 }
	 
	 return true;
}
 
function isCnpj(s){
	var i;
	var c = s.substr(0,12);
	var dv = s.substr(12,2);
	var d1 = 0;
 
	 for (i = 0; i < 12; i++){
		d1 += c.charAt(11-i)*(2+(i % 8));
	 }
 
	if (d1 == 0) return false;
 
	d1 = 11 - (d1 % 11);
 
	if (d1 > 9) d1 = 0;
	 if (dv.charAt(0) != d1){
		return false;
	 }
 
	d1 *= 2;
 
	 for (i = 0; i < 12; i++){
		d1 += c.charAt(11-i)*(2+((i+1) % 8));
	 }
 
	d1 = 11 - (d1 % 11);
 
	if (d1 > 9) d1 = 0;
	 if (dv.charAt(1) != d1){
	 return false;
	 }
 
	return true;
}
 
function isCpfCnpj(valor) {
	 var retorno = false;
	 var numero  = valor;
 
	numero = unformatNumber(numero);
	 if (numero.length > 11){
		 if (isCnpj(numero)) {
		 retorno = true;
		 }
	 } else {
		 if (isCpf(numero)) {
		 retorno = true;
		 }
	 }
 
 return retorno;
}