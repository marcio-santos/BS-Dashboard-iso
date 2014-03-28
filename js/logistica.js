$(document).ready(function() {


function getEnvio() {
    var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/logistica_data_source.php";
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
                $('#info_controle').fadeIn();
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
                $('#info').fadeIn();
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
            $('#controle_response').html('<span style="color: red">Não foi possivel gerar o botão para pagamento.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
        }
    });
}
    
    
    
});