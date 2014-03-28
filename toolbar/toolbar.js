$(document).ready(function() {    
    $('.toolbar').hover(
        function(){
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
        $(this).attr('src',$(this).attr('on'));

        $('.toolbar').each(function() {
            if($(this).hasClass('selected')){
                var $img_off = $(this).attr('off');
                $(this).removeClass('selected');
                $(this).attr('src',$img_off);
            }
        });

        $('#'+the_id).addClass('selected');

    });

    $('#close').click(function(){
        $.fancybox.close();
    })

    /*
    $('#load').click(function(){
    $.fancybox($(this),{
    href : '#div_frame',
    type: 'inline',
    maxWidth    : 680,
    maxHeight    : 600,
    fitToView    : true,
    modal        : true,
    locked        : true,
    width        : 680,
    height        : 600,
    autoSize    : false,
    closeClick    : false,
    openEffect    : 'elastic',
    closeEffect    : 'elastic',
    helpers : {
    overlay : {
    css : {
    'background' : 'rgba(0,0,0,0.80)',
    }
    }
    }
    });
    });
    */

    $('#client').click(function(){
        var evento_id = $('#tb_eventoid').val();
        console.log(evento_id);
        var datax = $.parseJSON(
            /*
            $.ajax({
            data: {eventoid:evento_id},
            type: "POST",
            dataType: 'json',
            url: "http://bodysystems.net/_ferramentas/dashboard-iso/toolbar/data_source.php",
            async: false
            }).responseText);
            */
            $.ajax({
                type: "POST",
                url: "http://bodysystems.net/_ferramentas/dashboard-iso/toolbar/data_source.php",
                data: { eventoid: evento_id }
            })
            .done(function( msg ) {
                console.log(msg);
                return msg ;
        }));
        console.log(datax);
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

        $('#add_more').on('click', function(){
            $('#spinner').show();
            addMovies(5);
            return false;
        });
        $('#stream_table').fadeIn();

        $('#user_list').fadeIn();
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