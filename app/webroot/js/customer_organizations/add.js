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
});
