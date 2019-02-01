function sendClap(){
    var url = $(this).attr("data-url");
    var clickElement =this;
    $.ajax({
        url:url,
        method: 'post'
    }).done(function (response) {
        var newClapNumber = response.data.claps;
        //$(".clap-num").html(newClapNumber)
        //un clic par bouton seulement
        $(clickElement).next().html(newClapNumber);
    });
}


//sur clic du couton de clap, on lance une requête ajax
$(".clap-btn").on("click", sendClap);
