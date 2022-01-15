$(function()
{
    $(':input:not(:submit, :button, :reset, [type="image"])').focus(function()
    {
        $(this).css('background', '#FFFADD');
    }).blur(function()
    {
        $(this).css('background', '#FFFFFF');
    });
    
    $('.menu img, input[type="image"]').hover(function()
    {
        $(this).css('background', '#FEDFFE');
    },function()
    {
        $(this).css('background', '#FFFFFF');
    }).mousedown(function()
    {
        $(this).css('background', '#DFE0FE');
    });
});
var ajax_loader_image = '<center><img src="' + base + '/img/ajax-loader.gif">'
                      + '</center>';
