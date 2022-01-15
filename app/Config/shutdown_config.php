<?php
function my_shutdown_handler()
{
    $isError = false;
    if ($error = error_get_last()) {
        switch($error['type']) {
        case E_ERROR:
        case E_PARSE:
        case E_CORE_ERROR:
        case E_CORE_WARNING:
        case E_COMPILE_ERROR:
        case E_COMPILE_WARNING:
            $isError = true;
            break;
        }
    }
    if ($isError) {
        $emails = array(
            'kazuteru.tsuchihashi@jrits.ricoh.co.jp', // 土橋
            'yusuke.akamae@jrits.ricoh.co.jp', // 赤前さん
        );
        
        /*
        $host = '165.96.170.160';
        ini_set('SMTP', $host);
        */
        
        $subject = '【MDSポータル】エラー発生';
        $text    = print_r($error, true);
        
        foreach ($emails as $email) {
            $from = $email;
            $to   = $email;
            $return = mb_send_mail(
                $to,
                mb_convert_encoding($subject, 'JIS'),
                mb_convert_encoding($text, 'JIS'),
                "From: {$from}"
            );
            if ($return === false) {
                err('エラー検知メールの送信エラー', 'my_shutdown_handler');
            }
        }
    }
}
register_shutdown_function('my_shutdown_handler');
