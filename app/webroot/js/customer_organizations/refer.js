$(function()
{
    $('#tree_customerorganization_area').treeview({
        control: '#tree_customerorganization_control', 
        animated: 'medium',
        //unique: true,
        collapsed: true, 
        persist: 'cookie'
    });
    $('div.tree').show();
    
    $('#tree_customerorganization a').click(function()
    {
        window.opener.$('#ReferCustomerOrganization :text').val(
            $(this).attr('path')
        );
        window.opener.$('#ReferCustomerOrganization :hidden').val(
            $(this).attr('id')
        );
        window.close();
    });
    if ($('#hidden').val()) {
        $('#' + $('#hidden').val()).parent().hide();
    }
});
