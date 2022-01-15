$(function()
{
    $('#ReferCustomerOrganization :input').click(function()
    {
        window.open(
            base + '/customer_organizations/refer',
            'ReferCustomerOrganization'
        );
        return false;
    });
    $('#UserGroupId').change(function()
    {
        // 権限がお客様
        if ($(this).val() == 3) {
            $('#customer_organization').css('display', '');
            $('#company_name_for_mail').css('display', '');
            $('#contact_address').css('display', '');
            $('#person_name_for_mail').css('display', '');
            $('#mps_customer').css("display","none");
            $('#customer_organization, #customer_organization :input')
                .attr('disabled', false);
            $('#company_name_for_mail, #company_name_for_mail :input')
                .attr('disabled', false);
            $('#company_name_for_mail, #company_name_for_mail :input')
                .val('');
            $('#person_name_for_mail, #person_name_for_mail :input')
                .attr('disabled', false);
            $('#contact_address, #contact_address :input')
                .attr('disabled', false);
        // 権限がお客様以外
        } else if ($(this).val() < 3) {
            $('#customer_organization').css('display', '');
            $('#company_name_for_mail').css('display', '');
            $('#contact_address').css('display', '');
            $('#person_name_for_mail').css('display', '');
            $('#mps_customer').css("display","none");
            $('#customer_organization, #customer_organization :input')
                .attr('disabled', true);
            $('#company_name_for_mail, #company_name_for_mail :input')
                .attr('disabled', true);
            $('#person_name_for_mail, #person_name_for_mail :input')
                .attr('disabled', true);
            $('#contact_address, #contact_address :input')
                .attr('disabled', true);
        } else if ($(this).val() == 5 ){
            $('#customer_organization').css('display', 'none');
            $('#company_name_for_mail').css('display', '');
            $('#contact_address').css('display', 'none');
            $('#mps_customer').css("display","");
            $('#customer_organization, #customer_organization :input')
                .attr('disabled', true);
            $('#company_name_for_mail, #company_name_for_mail :input')
                .attr('disabled', false);
            $('#company_name_for_mail, #company_name_for_mail :input')
                .val('リコージャパン株式会社');
            $('#person_name_for_mail, #person_name_for_mail :input')
                .attr('disabled', true);
            $('#contact_address, #contact_address :input')
                .attr('disabled', true);
        } else {
            $('#customer_organization').css('display', 'none');
            $('#company_name_for_mail').css('display', 'none');
            $('#contact_address').css('display', 'none');
            $('#mps_customer').css("display","");
            $('#customer_organization, #customer_organization :input')
                .attr('disabled', true);
            $('#company_name_for_mail, #company_name_for_mail :input')
                .attr('disabled', true);
            $('#person_name_for_mail, #person_name_for_mail :input')
                .attr('disabled', true);
            $('#contact_address, #contact_address :input')
                .attr('disabled', true);
        }
    });
    // 権限がお客様
    if ($('#UserGroupId').val() == 3) {
        $('#customer_organization').css('display', '');
        $('#company_name_for_mail').css('display', '');
        $('#person_name_for_mail').css('display', '');
        $('#contact_address').css('display', '');
        $('#mps_customer').css("display","none");
        $('#customer_organization, #customer_organization :input')
            .attr('disabled', false);
        $('#company_name_for_mail, #company_name_for_mail :input')
            .attr('disabled', false);
        $('#person_name_for_mail, #person_name_for_mail :input')
            .attr('disabled', false);
        $('#contact_address, #contact_address :input')
            .attr('disabled', false);
    // 権限がお客様以外
    } else if ($('#UserGroupId').val() < 3) {
        $('#customer_organization').css('display', '');
        $('#company_name_for_mail').css('display', '');
        $('#person_name_for_mail').css('display', '');
        $('#contact_address').css('display', '');
        $('#mps_customer').css("display","none");
        $('#customer_organization, #customer_organization :input')
            .attr('disabled', true);
        $('#company_name_for_mail, #company_name_for_mail :input')
            .attr('disabled', true);
        $('#person_name_for_mail, #person_name_for_mail :input')
            .attr('disabled', true);
        $('#contact_address, #contact_address :input')
            .attr('disabled', true);
    // MPS、SAユーザー
    } else {
        $('#customer_organization').css('display', 'none');
        $('#person_name_for_mail').css('display', '');
        $('#contact_address').css('display', 'none');
        $('#mps_customer').css("display","");
        if ($('#UserGroupId').val() == 5) {
            $('#company_name_for_mail').css('display', '');
            $('#company_name_for_mail, #company_name_for_mail :input')
                .attr('disabled', false);
        } else {
            $('#company_name_for_mail').css('display', 'none');
            $('#company_name_for_mail, #company_name_for_mail :input')
                .attr('disabled', true);
        }

        $('#customer_organization, #customer_organization :input')
            .attr('disabled', true);
        $('#person_name_for_mail, #person_name_for_mail :input')
            .attr('disabled', true);
        $('#contact_address, #contact_address :input')
            .attr('disabled', true);

        var mps_customer = $(':text[id="UserMpsCustomerId"]').val();
        var cust_sel = mps_customer.split( '|' );
        var cid,cname;
        var opt;
        for (var i=0;i<cust_sel.length;i++) {
          cid = cust_sel[i];
          if (cid!="") {
            cname = $("select#cust_all option[value="+cid+"]").text();
            $("select#cust_sel").append("<option value='"+cid+"'>"+cname+"</option>");
            $("select#cust_all option[value="+cid+"]").remove();
          }
        }
    }
});
