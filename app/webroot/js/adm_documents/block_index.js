$(function()
{
    $('.mdscomment span', '#right_area').tinyTips('title');
    
    // 登録アイコンの遷移先URL
    var add_baseurl = base + '/' + controller + '/add/';
    
    // 編集リンクをクリック
    $('.edit_link').click(function()
    {
        var add_url =
            add_baseurl
            + 'year_month:' + $('#year_month').val() + '/'
            + 'customer_organization_id:'
            + $(this).attr('customer_organization_id') + '/'
            + 'base_customer_organization_id:'
            + $('#customer_organization_id').val() + '/';
        location.href = add_url;
        return false;
    });
    
    // 戻るリンクをクリック
    $('.ret_link').click(function()
    {
        var ret_url =
            base + '/' + controller + '/index/0/'
        location.href = ret_url;
        return false;
    });
});
