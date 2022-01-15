$(document).ready(function(){
    $("#Nav-"+document.body.id).val(function(index, val)
    {
        $(this).css("background-image", $(this).css("background-image").replace("_off", "_on"));
        $(this).css("color", "#000000");
    });
});
