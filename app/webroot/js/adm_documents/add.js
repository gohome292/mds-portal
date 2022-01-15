$(function()
{
    $('#browser').click(function()
    {
        url = base + '/customer_organizations/refer';
        if ($('#DocumentPath').val() != '') {
           url = url + '/top:' + $('#DocumentPath').val();
        }
        window.open(
            url,'ReferCustomerOrganization'
        );
        return false;
    });
    
    $('#AdmDocumentAddForm').submit(function() {
        if ($("#DocumentYearMonth").val().length != 6) {
            $('input.save').val('保存').attr('disabled', false);
            alert('正しい日付形式で入力してください。');
            return false;
        } else {
            var y = $('#DocumentYearMonth').val().substring(0,4);
            var m = $('#DocumentYearMonth').val().substring(4,6);
            var d = new Date(y,m-1,1);
            if (isNaN(d)) {
                $('input.save').val('保存').attr('disabled', false);
                alert('正しい日付形式で入力してください。');
                return false;
            }
            now = new Date();
            now.setDate(1);
            if (d > now) {
               $('input.save').val('保存').attr('disabled', false);
                alert('翌月以降の報告書の登録できません。');
                return false;
            }
         }
    });
});
