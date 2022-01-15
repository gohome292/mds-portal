<?php
App::uses('AppShell', 'Console/Command');
App::uses('ComponentCollection', 'Controller'); //これが大事
App::uses('MailMakeComponent', 'Controller/Component');

class MdsSendmailShell extends AppShell
{
    public $uses = array(
        'MailHistory',
        'MailHistoryDetail',
        'CustomerOrganization',
        'MailTemplate',
        'User',
        'Document',
    );

    public function startup(){
        parent::startup();
    }

    public function main()
    {
        // タイムアウト時間設定
        timeout(60 * 60 * TIMEOUT_HOUR);
        //App::import('Core', 'Controller');
        $collection = new ComponentCollection();
        $this->base = $this->_getBaseUrl();
        $this->MailMake = new MailMakeComponent($collection);
        $this->MailMake->initShell($this);
        
        $mhs = $this->MailHistory->find('all',  array(
            'recursive' => 0,
            'fields' => array('MailHistory.id',
                'MailHistory.send_order_count',
                'MailHistory.template_id',
                'MailHistory.template_seq',
                'MailHistory.user_id',
                'MailHistory.customer_organization_id',
                'MailHistory.modified_user_id',
                'MailHistory.plan_start',
                'MailHistory.sendflg',
                'MailHistory.document_check',
                'MailHistory.fix_send_date',
                'CustomerOrganization.name',
                'cUser.email',
            ),
            'conditions' => array(
                'MailHistory.status' => 0,
                'MailHistory.plan_start <= now()',
            ),
        ));
        $sleepCnt = 0;
        foreach ($mhs as $mh) {
            $this->MailHistory->begin();
            if ($mh['MailHistory']['document_check'] > 0) {
                if ($mh['MailHistory']['document_check'] == 1) {
                    $year_month = date('Ym', strtotime('last day of -1 month'));
                    $ym_name = '前月';
                } else {
                    $year_month = date('Ym');
                    $ym_name = '当月';
                }
                if ($this->MailMake->getDocumentCnt($mh['MailHistory']['customer_organization_id'],$year_month) == 0) {
                    $this->_endMail($mh, $year_month);
                    $this->MailHistory->commit();
                    continue;
                }
            }
            if ($sleepCnt > MAX_MAIL_COUNT_30MINUTES) {
                sleep(5*60-1);
                $sleepCnt = 0;
            }
            $details = $this->MailHistoryDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'MailHistoryDetail.mail_history_id' => $mh['MailHistory']['id'],
                    'MailHistoryDetail.success' => 0,
                ),
            ));
            $success_count = 0;
            $failure_count = 0;
            $mh['MailHistory']['send_start_date'] = date('Y-m-d H:i:s');
            foreach ($details as $detail) {
                $from  = $detail['MailHistoryDetail']['from'];
                $headers = 'From: ' . $from;
                $to    = $detail['MailHistoryDetail']['to'];
                $title = mb_convert_encoding($detail['MailHistoryDetail']['title'], 'ISO-2022-JP-MS', 'UTF-8' );
                $body = mb_convert_encoding($detail['MailHistoryDetail']['body'], 'ISO-2022-JP-MS', 'UTF-8' );
                // メール送信
                $result = mb_send_mail($to, $title, $body, $headers);
                // メール送信履歴明細を更新
                $this->MailHistoryDetail->id = $detail['MailHistoryDetail']['id'];
                $this->MailHistoryDetail->saveField('send_date', date('Y-m-d H:i:s'));
                if ($result === false) {
                    // 送信失敗
                    $failure_count++;
                    $this->MailHistoryDetail->saveField('success', 2);
                } else {
                    // 送信成功
                    $success_count++;
                    $this->MailHistoryDetail->saveField('success', 1);
                }
            }
            $mh['MailHistory']['send_end_date'] = date('Y-m-d H:i:s', time());
            if ($failure_count > 0) {
                $mh['MailHistory']['status'] = 2;
            } else {
                $mh['MailHistory']['status'] = 1;
            }
            $mh['MailHistory']['success_count'] = $success_count;
            $mh['MailHistory']['failure_count'] = $failure_count;
            $this->MailHistory->save($mh);
            // 公開フラグ更新
            $this->MailMake->setDocOpenFlag($mh, 0);

            $this->_endMail($mh);
            $this->MailHistory->commit();
            $sleepCnt += $mh['MailHistory']['send_order_count'];
        }
    }

    function _getBaseUrl()
    {
        $url = 'none';
        $paths = explode(DS, ROOT);
        foreach ($paths as $p) {
            if ($url=='none') {
                if (preg_match('/(h*.docs)/', $p)) {
                    $url = '';
                }
            } else {
                $url = $url . '/' . $p ;
            }
        }
        return $url;
    }
    
    // @return void
    function _endMail($data, $ym_name=null)
    {
        if (!empty($data['cUser']['email'])) {
            if (empty($ym_name)) {
                $title = '【MDSカスタマーポタルサイト】時間指定メールが送信されました';
                $body  = file_get_contents(
                    TMP."templates/email - sendmail.finished.txt"
                );
                $body  = CakeText::insert(
                    $body,
                    array(
                        'user'  => $data['CustomerOrganization']['name'],
                        'success_count' => $data['MailHistory']['success_count'],
                        'send_end_date' => date('Y/n/j H:i:s'),
                    )
                );
                if ($data['MailHistory']['sendflg'] == 2) {
                    $plan_start = $this->_newNextMonthMail($data);
                    $body = $body."\n毎月定期送信を設定しているため、次回メール送信を予約しました。\n送信予定日は"
                      . date('Y-m-d H:i:s', $plan_start);
                }
            } else {
                $title = '【MDSカスタマーポタルサイト】報告書がないため予約メール送信を取り消しました';
                $body = "予約メール送信時、${ym_name}の報告書が登録されていないためメール送信を取り消しました。"
                      . "\n詳細はMDSカスタマーポータルサイトにてご確認ください。" 
                      . "\n\nお客様名： ". $data['CustomerOrganization']['name'];
                if ($data['MailHistory']['sendflg'] == 2) {
                    $plan_start = $this->MailMake->getNextMonth(strtotime($data['MailHistory']['plan_start']),
                        strtotime($data['MailHistory']['fix_send_date']));
                    $data['MailHistory']['plan_start'] =  date('Y-m-d H:i:s', $plan_start);
                    $this->MailHistory->id = $data['MailHistory']['id'];
                    $this->MailHistory->saveField('plan_start', date('Y-m-d H:i:s', $plan_start));
                    $this->MailHistoryDetail->deleteAll(array('mail_history_id' => $data['MailHistory']['id']), false);
                    $this->MailMake->saveHistoryDetail($data, $data['MailHistory']['id']);
                    $body = $body."\n\n毎月定期送信を設定しているため、次回メール送信を予約しました。\n送信予定日は"
                      . date('Y-m-d H:i:s', $plan_start);
                } else {
                    $this->MailHistory->delete($data['MailHistory']['id']);
                    $this->MailHistoryDetail->deleteAll(array('MailHistoryDetail.mail_history_id' => $data['MailHistory']['id']));
                }
            }
            $body  = mb_convert_encoding($body, 'ISO-2022-JP-MS', 'UTF-8');
            $to = $data['cUser']['email'];
            $title =  mb_convert_encoding($title, 'ISO-2022-JP-MS', 'UTF-8');
            $headers = 'From: '. Configure::read('Mds.sendmail.from');
            // メール送信
            $result = mb_send_mail($to, $title, $body, $headers);
        }
    }

    function _newNextMonthMail($data)
    {
        unset($data['MailHistory']['id']);

        $plan_start = $this->MailMake->getNextMonth(strtotime($data['MailHistory']['plan_start']),
                       strtotime($data['MailHistory']['fix_send_date']));
        $this->MailHistory->begin();
        $this->MailHistory->create(false);
        $data['MailHistory']['status'] = 0;
        $data['MailHistory']['send_start_date'] = null;
        $data['MailHistory']['send_end_date'] = null;
        $data['MailHistory']['success_count'] = 0;
        $data['MailHistory']['failure_count'] = 0;
        $data['MailHistory']['modified'] = date('Y-m-d H:i:s');
        $data['MailHistory']['plan_start'] = date('Y-m-d H:i:s', $plan_start);
        $this->MailHistory->save($data);
        $id = $this->MailHistory->id;
        $this->MailHistoryDetail->deleteAll(array('mail_history_id' => $id), false);
        $this->MailMake->saveHistoryDetail($data, $id);
        //$this->MailMake->setDocOpenFlag($data, 2);
        $this->MailHistory->commit();
        return $plan_start;
    }
}
