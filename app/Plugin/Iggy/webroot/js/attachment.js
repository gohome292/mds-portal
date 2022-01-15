$(function()
{
    $('.attachment_removes').click(function()
    {
        if ($(this).attr('checked')) {
            $(
                '.attachment_files[identifier="'
                + $(this).attr('identifier') + '"]'
            ).val('').attr('disabled', true);
            $(
                '.attachment_attaches[identifier="'
                + $(this).attr('identifier') + '"]'
            ).attr('disabled', true);
        } else {
            $(
                '.attachment_files[identifier="'
                + $(this).attr('identifier') + '"]'
            ).attr('disabled', false);
            $(
                '.attachment_attaches[identifier="'
                + $(this).attr('identifier') + '"]'
            ).attr('disabled', false);
        }
    });
    $('.attachment_attaches').change(function()
    {
        if ($(this).val() == '') {
            $(
                '.attachment_files[identifier="'
                + $(this).attr('identifier') + '"]'
            ).attr('disabled', false);
            $(
                '.attachment_removes[identifier="'
                + $(this).attr('identifier') + '"]'
            ).attr('disabled', false);
        }
        changeDisable();
    });
    changeDisable();
    
    function changeDisable()
    {
        $('option', '.attachment_attaches').attr('disabled', false);
        $('option:selected:not([value=""])', '.attachment_attaches').map(
        function()
        {
            $(
                'option[value="' + $(this).val() + '"]',
                '.attachment_attaches'
            ).attr('disabled', true);
            $(this).attr('disabled', false);
            $(
                '.attachment_files[identifier="'
                + $(this).parent().attr('identifier') + '"]'
            ).val('').attr('disabled', true);
            $(
                '.attachment_removes[identifier="'
                + $(this).parent().attr('identifier') + '"]'
            ).attr('disabled', true);
        });
    }
});
