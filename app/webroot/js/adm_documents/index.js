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
    
    // 登録アイコンの遷移先URL
    var add_baseurl = base + '/' + controller + '/add/';
    var add_url = add_baseurl
            + 'year_month:' + $('#year_month').val() + '/';
    // 報告書一覧URL
    var index_baseurl = base + '/' + controller + '/block_index/';
    $('#menu_add a').attr('href', add_url);
    // 対象年月を変更
    $('#year_month').change(function()
    {
    //    if ($('#customer_organization_id').val() > 0) {
            change_conditions();
    //    }
    });
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
    
    // 年月・組織の指定が変更された時の処理
    function change_conditions()
    {
        add_url = add_baseurl
            + 'year_month:' + $('#year_month').val() + '/'
            + 'customer_organization_id:'
            + $('#customer_organization_id').val() + '/'
            + 'base_customer_organization_id:'
            + $('#customer_organization_id').val() + '/';
        
        $('#menu_add a').attr('href', add_url);
        
        var index_url =
            index_baseurl
            + 'year_month:' + $('#year_month').val() + '/'
            + 'customer_organization_id:'
            + $('#customer_organization_id').val() + '/'
            + getAjaxParam();
        
        $('#right_area').empty().html(ajax_loader_image).load(index_url);
    }
    // ログダウンロード時の処理
    $('#log_output').click(function()
    {
        if ($('#AccesslogYYYYId').val() === null) {
            alert('操作ログ存在しません。');
            return false;
        }
        location.href = base  + '/' + controller
                      + '/index/AccesslogYYYYId:'
                      + $('#AccesslogYYYYId').val()
                      + '/';
        return false;
    });

});
