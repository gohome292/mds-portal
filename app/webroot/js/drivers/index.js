$(function()
{
    // 分類リストを変更
    $('#DriverDriverManualTypeId').change(function()
    {
        location.href = base  + '/' + controller
                      + '/index/' + $(this).val()
                      + '/';
    });

});
