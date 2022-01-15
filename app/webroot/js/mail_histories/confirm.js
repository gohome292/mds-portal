$chkOpenFlag = function(){
   if ($('#MailHistoryDocumentCheck').val() == '0') {
       if ($('#count0').text()!='0') {
           if ($('#auth_group_id').text()=='1' || $('#auth_group_id').text()=='2' ) {
               var ret = confirm('非公開の報告書があります。このメールで公開しますか？');
               if (ret)
                 $('#MailHistoryOpenFlag').val('doOpen');
               return true;
           } else {
               alert('非公開の報告書があります。管理者に連絡してください。');
               return false;
           }
       }
   } else if ($('#MailHistorySendflg').val()=='0' && $('#MailHistoryDocumentCheck').val()=='1' && $('#count1').text()=='0') {
       alert('前月の報告書が登録されていないためメール送信できません。');
       return false;
   } else if ($('#MailHistorySendflg').val()=='0' && $('#MailHistoryDocumentCheck').val()=='2' && $('#count2').text()=='0') {
       alert('当月の報告書が登録されていないためメール送信できません。');
       return false;
   }
   return true;
}
$(function()
{
    $('#sendmail').click(function()
    {
        $('#MailHistorySendflg').val('0');
        if (!$chkOpenFlag()) {
            return false;
        }
        var ret = confirm('送信してよろしいですか？');
        // はい
        if (ret) {
            $(this).attr('disabled', true)
            .closest('form').submit();
        }
        return false;
    });

    $('#reserve').click(function()
    {
        var d = new Date($('#MailHistoryPlanStartYear').val(), $('#MailHistoryPlanStartMonth').val()-1, $('#MailHistoryPlanStartDay').val(),
             $('#MailHistoryPlanStartHour').val(),0,0,0);
        if (d.getDate() != $('#MailHistoryPlanStartDay').val()) {
            alert('正しい日付形式で入力してください。');
            return false;
        }
        now = new Date();
        if (d < now ) {
            alert('送信予定日時は過去の日時指定できません。');
            return false;
        }
        if ($('#mailLoopFlg').prop('checked')) {
            $('#MailHistorySendflg').val('2');
        } else {
            $('#MailHistorySendflg').val('1');
        }
        if (!$chkOpenFlag()) {
            return false;
        }
        var ret = confirm('送信予約してよろしいですか？');
        if (ret) {
            $(this).attr('disabled', true)
            .closest('form').submit();
        }
        return false;
    });

    $('#sendmailFlg').click(function()
    {
        if ($(this).prop('checked')) {
            ChangeSendChecked(1);
        } else {
            ChangeSendChecked(0);
        }
    });

    $('#mailLoopFlg').click(function()
    {
        if ($(this).prop('checked')) {
            ChangeLoopChecked(1);
        } else {
            ChangeLoopChecked(0);
        }
    });

    if ($('#MailHistorySendflg').val()=='' || $('#MailHistorySendflg').val()=='0') {
        $('#sendmailFlg').prop('checked', false);
        $('#mailLoopFlg').prop('checked', false);
        ChangeSendChecked(0);
    } else {
        $('#sendmailFlg').prop('checked', true);
        if ($('#MailHistorySendflg').val()=='2') {
            $('#mailLoopFlg').prop('checked', true);
        } else {
            $('#mailLoopFlg').prop('checked', false);
        }
        ChangeSendChecked(1);
    }
});


function ChangeSendChecked(isChecked) {
    if (isChecked) {
        $('#sendmail').attr('disabled', true);
        $('#reserve').attr('disabled', false);
        $('#MailHistoryPlanStartYear').attr('disabled', false);
        $('#MailHistoryPlanStartMonth').attr('disabled', false);
        $('#MailHistoryPlanStartDay').attr('disabled', false);
        $('#MailHistoryPlanStartHour').attr('disabled', false);
        $('#MailHistoryPlanStartMin').attr('disabled', false);
        $('#mailLoopFlg').attr('disabled', false);
        if ($('#mailLoopFlg').prop('checked')) {
            ChangeLoopChecked(1);
        } else {
            ChangeLoopChecked(0);
        }
    } else {
        $('#sendmail').attr('disabled', false);
        $('#reserve').attr('disabled', true);
        $('#MailHistoryPlanStartYear').attr('disabled', true);
        $('#MailHistoryPlanStartMonth').attr('disabled', true);
        $('#MailHistoryPlanStartDay').attr('disabled', true);
        $('#MailHistoryPlanStartHour').attr('disabled', true);
        $('#MailHistoryPlanStartMin').attr('disabled', true);
        $('#mailLoopFlg').attr('disabled', true);
        ChangeLoopChecked(0);
    }
}
function ChangeLoopChecked(isChecked) {
    if (isChecked) {
        $('#MailHistoryFixSendDateDay').attr('disabled', false);
        $('#MailHistoryFixSendDateHour').attr('disabled', false);
        $('#MailHistoryFixSendDateMin').attr('disabled', false);
    } else {
        $('#MailHistoryFixSendDateDay').attr('disabled', true);
        $('#MailHistoryFixSendDateHour').attr('disabled', true);
        $('#MailHistoryFixSendDateMin').attr('disabled', true);
    }
}

