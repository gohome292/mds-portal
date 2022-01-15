<?php
App::import('Component', 'Iggy.Qdmail');
App::import('Component', 'Iggy.Qdsmtp');

class MailComponent extends Component
{
    var $mail = null; // Qdmail Instance
    
    // @param array $param
    // @return void
    function create($param = null)
    {
        $mail =& $this->mail;
        $mail = new QdmailComponent();
        $mail->errorDisplay(false);
        //$mail->errorlogLevel(3);
        if (empty($param)) return;
        $mail->smtp(true);
        $mail->smtpObject()->error_display = false;
        $mail->smtpServer($param);
    }
    
    // @param mixed $from
    // @param mixed $to
    // @param mixed $bcc
    // @param string $subject
    // @param string $text
    // @return boolean
    function send($from, $to, $bcc = null, $subject, $text)
    {
        $mail =& $this->mail;
        $mail->from($from);
        $mail->to($to);
        if (!empty($bcc)) $mail->bcc($bcc);
        $mail->replyTo($from);
        $_from = $from;
        if (is_array($_from)) $_from = array_shift($_from);
        if (is_array($_from)) $_from = array_shift($_from);
        $mail->mtaOption("-f {$_from}");
        $mail->kana(true);
        $mail->subject($subject);
        $mail->text($text);
        return $mail->send();
    }
    
    // @return void
    function attach($param, $add = false)
    {
        $this->mail->attach($param, $add);
    }
    
    // @return array
    function error()
    {
        return $this->mail->errorStatment();
    }
}
