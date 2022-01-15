$(function()
{
    $('#tree_customerorganization_area').treeview({
        control: '#tree_customerorganization_control', 
        animated: 'medium',
        //unique: true,
        collapsed: true, 
        persist: 'cookie'
    });
    $('#MENU').show();
    
    // 報告書一覧URL
    var index_baseurl = base + '/' + controller + '/block_index/';
    
    // 対象年月を変更
    $('#year_month').change(function()
    {
        if ($('#customer_organization_id').val() > 0) {
            change_conditions();
        }
    });
    // 組織をクリック
    $('a', '#tree_customerorganization').click(function()
    {
        $('#customer_organization_id').val($(this).attr('id'));
        
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
        var index_url =
            index_baseurl
            + 'year_month:' + $('#year_month').val() + '/'
            + 'customer_organization_id:'
            + $('#customer_organization_id').val() + '/'
            + getAjaxParam();
        
        $('#CONTENT').empty().html(ajax_loader_image).load(index_url);
    }
});
