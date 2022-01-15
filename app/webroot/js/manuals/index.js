$(function()
{
    // 分類リストを変更
    $('#ManualDriverManualTypeId').change(function()
    {
        location.href = base  + '/' + controller
                      + '/index/' + $(this).val()
                      + '/';
    });

});
