$(document).ready(function(){

    $('#switchLang').change(function() {
        window.location = $(this).val();

    });

    $("#test").click(function(){
        $(this).hide();
    });

});