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
});
