$(document).ready(function() {
    
   var url = "http://bodysystems.net/_ferramentas/dashboard-iso/services/iso_ground.php";
    $.ajax({
        type: "POST",
        url: url,
        dataType: "html" ,
        beforeSend: function(load) {
            $('#dash').html("<div style='margin-top:30px;margin-left:20px;'><img src='http://bodysystems.net/_ferramentas/workshop/images/activity.gif' /></div>");

        } ,
        success: function(data)
        {
           
           $('#dash').html(data);
        } ,
        error: function (request, status, error)
        {
            $('#dash').html('<span style="color: red">Não foi possivel gerar o botão para pagamento.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
        }
    });   

});
