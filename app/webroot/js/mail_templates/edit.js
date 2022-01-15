$(function()
{
    $('#MailTemplateCustomerOrganizationId').change(function()
    {
        if ($('#MailTemplateCustomerOrganizationId').val() == '') {
            $('#MailTemplateTitle, #MailTemplateBody').attr('readonly', true);
        }
        $('#mail_comment_output, input.save').attr('disabled', true);
        location.href = base  + '/' + controller
                      + '/edit/customer_organization_id:'
                      + $(this).val()
                      + '/';
        return false;
    });
    
    $('#MailTemplateId').change(function()
    {
        location.href = base  + '/' + controller
                      + '/edit/' + $(this).val()
                      + '/';
        return false;
    });

    $('#mail_comment_output').click(function()
    {
        location.href = base  + '/' + controller
                      + '/output/customer_organization_id:'
                      + $('#MailTemplateCustomerOrganizationId').val()
                      + '/';
        return false;
    });
    
    $('#btnFindCust').click(filterCust);
    $('#txtFindCust').keypress( function ( e ) {
        if ( e.which == 13 ) {
            filterCust();
            return false;
        }
    } );

    $('#MailTemplateEditForm').submit(function() {
        if ($("#new_flg").prop("checked")) {
            if (confirm("新規のテンプレートを作成しますか？")) {
                //$('#mail_comment_output, #Attachment0File').attr('disabled',true);
                $("#MailTemplateId").prop("disabled", true);
                $(this).attr('action', base  + '/' + controller + '/edit/');
                return true;
            }
        } else {
            if (confirm("既存のテンプレートを更新しますか？")) {
                //$('#mail_comment_output, #Attachment0File').attr('disabled',true);
                $("#MailTemplateId").prop("disabled", true);
                return true;
            }
        }
        $('input.save').val('保存').attr('disabled', false);
        return false;
    });
    
    if ($('#MailTemplateCustomerOrganizationId').val() == '') {
        $('#MailTemplateTitle, #MailTemplateBody').attr('readonly', true);
        $('input.save').attr('disabled', true);
    }
    if ($('#MailTemplateId').prop('length') == 0) {
        $("#new_flg").prop("checked", true);
        $("#new_flg").prop("disabled", true);
    } else {
         var tId = $('#MailTemplateId').prop('selectedIndex')+1;
         if (tId<10)
            $('#tId').val('t0'+tId);
         else
            $('#tId').val('t'+tId);
         if ($('#MailTemplateId').prop('length') >= 20) {
            $("#new_flg").prop("checked", false);
            $("#new_flg").prop("disabled", true);
         }
    }
    var options = [];
    $('#MailTemplateCustomerOrganizationId').find('option').each(function() {
        options.push({value: $(this).val(), text: $(this).text()});
    });
    $('#MailTemplateCustomerOrganizationId').data('options', options);
    function filterCust() {
        var select = $('#MailTemplateCustomerOrganizationId');
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
