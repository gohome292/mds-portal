$(function()
{
    $('#search_tab').click(function()
    {
        $('.search').find(':input:visible:first').eq(0).focus();
        return false;
    });
});
