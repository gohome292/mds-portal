$(function()
{
    $('div.list').find(':input:visible:first').focus();
    $('button[type="reset"]').click(function()
    {
        $('div.list').find(':input:visible:first').focus();
        return false;
    });
    $('input.save').click(function()
    {
        $(this).val('保存中...').attr('disabled', true)
        .closest('form').submit();
        return false;
    });
    $('div.error-message').map(function()
    {
        if ($(this).text() == '入力してください'
        && $(this).prev().get(0).tagName == 'SELECT') {
            $(this).text('選択してください');
        }
    });
});
