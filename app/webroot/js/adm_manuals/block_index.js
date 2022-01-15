$(function()
{
    $('.mdscomment span', '#right_area').tinyTips('title');
    
    // 登録アイコンの遷移先URL
    var add_baseurl = base + '/' + controller + '/edit/';
    
    // 編集リンクをクリック
    $('.edit_link').click(function()
    {
        var add_url =
            add_baseurl
            + $(this).attr('manual_id') + '/';
        location.href = add_url;
        return false;
    });
});
