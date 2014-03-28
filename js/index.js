var st;
$(document).ready(function() {
/*
    $('#client').live('click',function(){    
        var datax = $.parseJSON($.ajax({type: "post",data:{eventoid:$('#tb_eventoid').val()},dataType: 'json', url: "http://bodysystems.net/_ferramentas/dashboard-iso/toolbar/data_source.php", async: false}).responseText);
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

    });
    */
});

function randomMovies(){
    var i = Math.floor(parseInt(Math.random()*100)) % Movies.length;
    console.log(Movies[i]);
    return Movies[i];

}

function addMovies(count){
    if (count){
        for(var i = 0; i < count; i++)
            st.addData(randomMovies());
    }else{
        st.addData(randomMovies());
    }

    $('.record_count .badge').text(st.data.length);
    $('#spinner').hide();
}

function getSource() {
    var url = 'http://bodysystems.net/_ferramentas/dashboard-iso/toolbar/data_source.php';  
    $.ajax({
        url: url,
        dataType: 'json',
        async: false
    })
    .done(function( data ) {
        if ( console && console.log ) {
            console.log( data);
        } 
        return data;
    });

}