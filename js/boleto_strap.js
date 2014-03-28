$(document).ready( function(){
   /*
    $docnum =           $_POST['evoid'];
    $cpf =              $_POST['cpf'];
    $nome =             $_POST['nome'];
    $endereco =         $_POST['endereco'];
    $valor_cobrado =    $_POST['valor_cobrado'] ;
     */


    $('#botao').click(function(){
        var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/boleto_strap.php";
        $.ajax({
            type: "POST",
            url: url,
            dataType: "html" ,
            data: {evoid:evoid,cpf:cpf,nome:nome,endereco:endereco,valor_cobrado:valor_cobrado,email:email},
            beforeSend: function(load) {

            } ,
            success: function(data)
            {
                $('#boleto_response').html(data)
            } ,
            error: function (request, status, error)
            {
                var msg = '<span style="color: red">Não foi possivel gerar o boleto.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>' ;

            }
        });
    })
});