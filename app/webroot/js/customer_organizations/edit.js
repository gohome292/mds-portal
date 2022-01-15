$(function()
{
    $('#ReferCustomerOrganization :button').click(function()
    {
        window.open(
            base + '/customer_organizations/refer/'
                + $('#CustomerOrganizationId').val(),
            'ReferCustomerOrganization'
        );
        return false;
    });
    
    $('#CustomerOrganizationEditForm').submit(function() {
        if ($("#CustomerOrganizationStartYearMonth").length) {
            var d = Date.parse($('#CustomerOrganizationStartYearMonth').val()+"/01");
            if (!d) {
                $('input.save').val('保存').attr('disabled', false);
                alert('正しい日付形式で入力してください。');
                return false;
            }
        }
    });
});
