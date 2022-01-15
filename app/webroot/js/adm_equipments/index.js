$(function()
{
    $('#tree_customerorganization_area').treeview({
        control: '#tree_customerorganization_control', 
        animated: 'medium',
        //unique: true,
        collapsed: true, 
        persist: 'cookie'
    });
    $('#left_area').show();
    
    $('#menu_add a').attr('href', 'javascript:void(0);');
    // 登録アイコンの遷移先URL
    var add_baseurl = base + '/' + controller + '/add/';
    // 報告書一覧URL
    var index_baseurl = base + '/' + controller + '/block_index/';
    
    // 組織をクリック
    $('a', '#tree_customerorganization').click(function()
    {
        $('#customer_organization_id').val($(this).attr('id'));
        
        $('#customer_organization_name').empty().text($(this).text());
        
        change_conditions();
    });
    
    // 初期状態で報告書一覧を表示する
    if ($('#customer_organization_id').val() > 0) {
        $(
            '#' + $('#customer_organization_id').val(),
            '#tree_customerorganization'
        ).click();
    }
    
    // 組織の指定が変更された時の処理
    function change_conditions()
    {
        var add_url =
            add_baseurl
            + 'customer_organization_id:'
            + $('#customer_organization_id').val() + '/'
            + 'base_customer_organization_id:'
            + $('#customer_organization_id').val() + '/';
        
        $('#menu_add a').attr('href', add_url);
        
        var index_url =
            index_baseurl
            + 'customer_organization_id:'
            + $('#customer_organization_id').val() + '/'
            + getAjaxParam();
        
        $('#right_area').empty().html(ajax_loader_image).load(index_url);
    }
});
