$(function()
{
    $("#return_btn").click(function(){
       location.href=base+'/macd_workflows/index/';
    });

    $("#templateDl").click(function(){
       location.href=base+'/iggy/attachments/download/' + $(this).val();
    });
});
