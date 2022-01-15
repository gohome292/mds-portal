$(function()
{
    $('input.confirm').click(function()
    {
        $(this).val('確認中...').attr('disabled', true)
        .closest('form').submit();
        return false;
    });

    $('#MailHistoryCustomerOrganizationId').change(function()
    {
        location.href = base  + '/' + controller
                      + '/address/' + $(this).val() + '/';
        return false;
    });

    $('#btnFindCust').click(filterCust);
    $('#txtFindCust').keypress( function ( e ) {
        if ( e.which == 13 ) {
            filterCust();
            return false;
        }
    } );

    $('#MailHistoryTemplateId').change(function()
    {
         var tId = $(this).prop('selectedIndex')+1;
         if (tId<10)
            $('#tId').val('t0'+tId);
         else
            $('#tId').val('t'+tId);
    });

    if ($('#MailHistoryTemplateId').prop('length') > 0) {
         var tId = $('#MailHistoryTemplateId').prop('selectedIndex')+1;
         if (tId<10)
            $('#tId').val('t0'+tId);
         else
            $('#tId').val('t'+tId);
    }
    $('#customer_organization_name').text($("select[id=MailHistoryCustomerOrganizationId] :selected").text() + 
    ' の送信タスク一覧');
    var options = [];
    $('#MailHistoryCustomerOrganizationId').find('option').each(function() {
        options.push({value: $(this).val(), text: $(this).text()});
    });
    $('#MailHistoryCustomerOrganizationId').data('options', options);
    function filterCust() {
        var select = $('#MailHistoryCustomerOrganizationId');
        var selectVal = select.val();
        var options = $(select).empty().data('options');
        var search = $('#txtFindCust').val().trim();
        var regex = new RegExp(search,"gi");
        $.each(options, function(i) {
            var option = options[i];
            if(option.text.match(regex) !== null || option.value == selectVal) {
                $(select).append(
                   $('<option>').text(option.text).val(option.value)
                );
            }
        });
        select.val(selectVal);
//        $(select).children().get(0).selected = true;
//        location.href = base  + '/' + controller
//                      + '/edit/customer_organization_id:'
//                      + $(select).val()
//                      + '/';
    }

});
