$(function()
{
    $('#tree_menu').treeview({
        collapsed: true
    });
    $("a:eq(1)", $('#tree_menu_control')).click();
    $('#mainmenu_button').click(function()
    {
        var tree_menu_area = $('#tree_menu_area');
        if (tree_menu_area.css('display') == 'none') {
            $('select').css('visibility', 'hidden');
            tree_menu_area.fadeIn('fast');
        } else if (tree_menu_area.css('display') == 'block') {
            tree_menu_area.fadeOut('fast', function(){
                $('select').css('visibility', '');
            });
        }
        return false;
    });
    $('input.close').click(function()
    {
        window.close();
        return false;
    });
});
