$(function()
{
    $('#cancel_button').click(function()
    {
        $('#search_area').find(':input:visible:not(:submit,:button)').val('');
        $(this).closest('form').submit();
        return false;
    });
});