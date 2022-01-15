$(function()
{
    $('#left_area').show();
    
    $('#menu_add a').attr('href', 'javascript:void(0);');
    // 登録アイコンの遷移先URL
    var add_baseurl = base + '/' + controller + '/add/';
    // ドライバ一覧URL
    var index_baseurl = base + '/' + controller + '/block_index/';
    
    // 分類リスト追加ボタン押下
    $('#type_list_add').click(function(){
    
        if($('#customer_organization_id').val() > 0){
            var add_url = base+'/driver_manual_types/edit/0/customer_organization_id:'
                          + $('#customer_organization_id').val() + '/driver_manual_id:'
                          + $('#driver_manual_id').val() + '/';
            location.href=add_url;
        } else {
            alert("顧客組織を選択してください。");
            return;
        }

    });
    
    // 分類リスト編集ボタン押下
    $('#type_list_edit').click(function(){
        if($('#type_list').val() > 0){
            var edit_url = base+'/driver_manual_types/edit/' + $('#type_list').val() +'/customer_organization_id:'
                          + $('#customer_organization_id').val() + '/driver_manual_id:'
                          + $('#driver_manual_id').val() + '/';
            location.href=edit_url;
        } else {
            alert("分類情報を選択ください。");
            return;
        }

    });
    
    // 分類リスト削除ボタン押下
    $('#type_list_remove').click(function(){
        if($('#type_list').val() > 0){
            if($('#driver_file_count').attr('value') > 0){
                alert("プリンタドライバ情報が存在するので、先に削除してください。");
                return;
            } else {
                var remove_url = base+'/driver_manual_types/remove/' + $('#type_list').val() + '/';
                location.href=remove_url;
            }
        } else {
            alert("分類情報を選択ください。");
            return;
        }
        
    });
    
    $('#type_list').change(function()
    {
        change_conditions();
    });
        
    // ドライバ一覧を表示する
    if ($('#type_list').val() > 0) {
        $('#type_list').change();
    }
    
    // 組織の指定が変更された時の処理
    function change_conditions()
    {
        
        var add_url = add_baseurl 
                      + 'driver_manual_type_id:' + $('#type_list').val() + '/'
                      + 'customer_organization_id:'
                      + $('#customer_organization_id').val() + '/';
        
        $('#menu_add a').attr('href', add_url);
        
        var index_url =
            index_baseurl
            + 'type_list:' + $('#type_list').val() + '/'
            + 'customer_organization_id:'
            + $('#customer_organization_id').val() + '/'
            + getAjaxParam();

        $('#right_area').empty().html(ajax_loader_image).load(index_url);
    }
    
});
