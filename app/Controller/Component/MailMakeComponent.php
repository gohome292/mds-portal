<?php
class MailMakeComponent extends Component
{
    function initialize(Controller $controller)
    {
        $this->controller = $controller;
    }
    
    function initShell(AppShell $controller) {
        $this->controller = $controller;
    }
    

    function getHistoryDetail($mh)
    {
        $_this = $this->controller;
        $details = $_this->MailHistoryDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'MailHistoryDetail.mail_history_id' => $mh['MailHistory']['id'],
                'MailHistoryDetail.success' => 0,
            ),
            'limit' => 1,
        ));
        $data = array(
            'MailHistoryDetail' => array(
                'mail_history_id' => $mh['MailHistory']['id'],
                'customer_organization_path' => $details[0]['MailHistoryDetail']['customer_organization_path'],
                'user_name' => $details[0]['MailHistoryDetail']['user_name'],
                'from'      => $details[0]['MailHistoryDetail']['from'],
                'to'        => $details[0]['MailHistoryDetail']['to'],
                'title'     => $details[0]['MailHistoryDetail']['title'],
                'body'      => $details[0]['MailHistoryDetail']['body'],
            ),
            'MailHistory' => array(
                'users_count' => $mh['MailHistory']['send_order_count'],
            ),
        );
        return $data;
    }

    function saveHistoryDetail($mh, $mail_history_id = 0, $sendflg = 0)
    {
        $_this = $this->controller;
        $tid = $mh['MailHistory']['template_id'];
        // ------------------------------------------------------------
        // メールテンプレート確認
        $params = array(
            'fields' => array(
                'MailTemplate.title',
                'MailTemplate.body',
            ),
            'conditions' => array(
                'MailTemplate.id' => $tid ,
            ),
            'recursive' => -1,
        );
        $mail_template = $_this->MailTemplate->find('first', $params);
        // @exception
        if (empty($mail_template)) {
            $data = array(
                'MailHistoryDetail' => array(
                    'mail_history_id' => -1,
                    'to'    => '/mail_templates/edit/customer_organization_id:'.$tid,
                    'title' => 'メールテンプレートがありません。<br/>メールテンプレートを作成して下さい。',
                ),
            );
            return $data;
        }
       // ------------------------------------------------------------
        // メール送信対象数確認
        $params = array(
            'conditions' => array(
                'not' => array(
                    'or' => array(
                        array('User.email' => ''),
                        array('User.email' => null),
                    ),
                ),
                'User.sendmail' => true,
                'User.mail_templates LIKE' => "%[{$tid}]%",
                    'or' => array(
                        array(
                            'concat(\'|\',User.mps_customer_id,\'|\') LIKE' => "%|{$mh['MailHistory']['customer_organization_id']}|%",
                            'User.group_id >' => 3,
                        ),
                        array(
                            'User.top_customer_organization_id' =>
                                $mh['MailHistory']['customer_organization_id'],
                            'User.group_id' => 3,
                        ),
                    )
            ),
            'recursive' => -1,
        );
        $users_count = $_this->User->find('count', $params);
        // @exception
        if (empty($users_count)) {
            $data = array(
                'MailHistoryDetail' => array(
                    'mail_history_id' => -1,
                    'to'    => '/mail_templates/edit/customer_organization_id:'. $tid,
                    'title' => 'メールを送信する対象がありません。<br/>メールリストを編集して下さい。',
                ),
            );
            return $data;
        }
        $host = 'https://www.rj-mds.com';
        // ------------------------------------------------------------
        // メール計画作成
        // メール件名・本文に挿入する動的変動文字列
        $this_month = strtotime($mh['MailHistory']['plan_start']);
        $last_month = strtotime('last day of -1 month', $this_month);
        $vars = array(
            'company_name'      => '', // ユーザ毎に設定値あり
            'organization_name' => '', // ユーザ毎に設定値あり
            'person_name'       => '', // ユーザ毎に設定値あり
            'comment'           => '', // ユーザ毎に設定値あり
            'freeword1'         => '', // ユーザ毎に設定値あり
            'freeword2'         => '', // ユーザ毎に設定値あり
            'last_month'        => date('Y年n月', $last_month),
            'this_month'        => date('Y年n月', $this_month),
            'information_url'   => $host . $_this->base . '/information/index/',
            'documents_last_month_url' =>
                $host . $_this->base . '/documents/index/' . date('Ym', $last_month),
            'documents_this_month_url' =>
                $host . $_this->base . '/documents/index/' . date('Ym', $this_month),
            'equipment_url'     => $host . $_this->base . '/equipment/index/',
            'driver_url'     => $host . $_this->base . '/drivers/index/',
            'manual_url'     => $host . $_this->base . '/manuals/index/',
            'macd_workflow_url'     => $host . $_this->base . '/macd_workflows/index/',
        );
        $params['fields'] = array(
            'User.id',
            'User.name',
            'User.top_customer_organization_id',
            'User.customer_organization_id',
            'User.email',
            'User.company_name_for_mail',
            'User.person_name_for_mail',
            'User.comment',
            'User.freeword1',
            'User.freeword2',
            'User.group_id',
            'User.mps_customer_id',
            'User.mail_templates',
            'CustomerOrganization.name',
        );
        $params['order'] = array(
            'User.id' => 'ASC',
        );
        $params['recursive'] = 1;
        $params['limit'] = MAX_MAIL_COUNT_30MINUTES;
        $page = 1;
        $failure_count = 0;
        $success_count = 0;
        while ($page <= 10) {
            $params['page'] = $page;
            $users = $_this->User->find('all', $params);
            if (empty($users)) break;
            foreach ($users as $user) {
                if ($user['User']['group_id']==3) {
                    // ユーザ毎の設定値を取得
                    $vars['company_name'] = _default(
                        $user['User']['company_name_for_mail'],
                        ''
                    );
                    // 所属組織が第1階層だった場合は空白とする
                    if ($user['User']['top_customer_organization_id']
                    == $user['User']['customer_organization_id']) {
                        $vars['organization_name'] = '';
                    } else {
                        $vars['organization_name'] =
                            $user['CustomerOrganization']['name'];
                    }
                    $vars['person_name'] = _default(
                        $user['User']['person_name_for_mail'],
                        ''
                    );
                    $vars['comment'] = _default(
                        $user['User']['comment'],
                        ''
                    );
                    $vars['freeword1'] = _default(
                        $user['User']['freeword1'],
                        ''
                    );
                    $vars['freeword2'] = _default(
                        $user['User']['freeword2'],
                        ''
                    );
                    $path = $_this->CustomerOrganization->getpathstr(
                        $user['User']['customer_organization_id']
                    );
                } else {
                    if ($user['User']['group_id']==5) {
                        $vars['company_name'] = _default(
                            $user['User']['company_name_for_mail'],
                            'リコージャパン株式会社');
                        $path = $vars['company_name'];
                    } else {
                        $vars['company_name'] = '';
                        $path = 'リコージャパン株式会社';
                    }
                    $vars['organization_name'] = '';
                    $vars['person_name'] = $user['User']['name'];
                    $vars['comment'] = '';
                    $vars['freeword1'] = '';
                    $vars['freeword2'] = '';
                }
                // 件名と本文に動的文字列を割り当てる
                $title = CakeText::insert(
                    $mail_template['MailTemplate']['title'],
                    $vars
                );
                $title = mb_substr($title, 0, 80);
                $body = CakeText::insert(
                    $mail_template['MailTemplate']['body'],
                    $vars
                );
                
                $data = array(
                    'MailHistoryDetail' => array(
                        'mail_history_id' => $mail_history_id,
                        'customer_organization_path' => $path,
                        'user_name' => $user['User']['name'],
                        'from'      => Configure::read('Mds.sendmail.from'),
                        'to'        => $user['User']['email'],
                        'title'     => $title,
                        'body'      => $body,
                    ),
                    'MailHistory' => array(
                        'users_count' => $users_count,
                    ),
                );
                
                // 最初の1通を例として表示する
                if ($mail_history_id == 0) {
                    return $data;
                }
                if ($mh['MailHistory']['sendflg'] == 0) {
                    $to = $user['User']['email'];
                    $headers = 'From: '. Configure::read('Mds.sendmail.from');
                    $body = mb_convert_encoding( $body, 'ISO-2022-JP-MS', 'UTF-8' );
                    $title = mb_convert_encoding( $title, 'ISO-2022-JP-MS', 'UTF-8' );
                    // メール送信
                    $result = mb_send_mail($to, $title, $body, $headers);
                    if ($result === false) {
                        // 送信失敗
                        $failure_count++;
                        $data['MailHistoryDetail']['success'] = 2;
                    } else {
                        // 送信成功
                        $success_count++;
                        $data['MailHistoryDetail']['success'] = 1;
                    }
                    $data['MailHistoryDetail']['send_date'] = date('Y-m-d H:i:s');
                }
                $_this->MailHistoryDetail->create(false);
                $_this->MailHistoryDetail->save($data);
            }
            $page++;
        }
        $_this->MailHistory->id = $mail_history_id;
        if ($mh['MailHistory']['sendflg'] == 0) {
            $d['MailHistory']['send_order_count'] = $users_count;
            $d['MailHistory']['success_count'] = $success_count;
            $d['MailHistory']['failure_count'] = $failure_count;
            $d['MailHistory']['send_end_date'] = date('Y-m-d H:i:s');
            if ($failure_count > 0) {
                $d['MailHistory']['status'] = 2;
            } else {
                $d['MailHistory']['status'] = 1;
            }
            $_this->MailHistory->save($d);
        } else {
            $_this->MailHistory->saveField('send_order_count', $users_count);
        }
    }
    
    function getDocumentCnt($customer_organization_id, $year_month) {
        $_this = $this->controller;
        $cust = $_this->CustomerOrganization->find('list', array(
            'fields' => array('id'),
            'conditions' => array(
                'CustomerOrganization.top_parent_id = ' => $customer_organization_id,
            ),)
        );
        if (empty($year_month)) {
            $count = $_this->Document->find('count', array(
                  'conditions' => array(
                        'Document.customer_organization_id' => $cust,
                        'Document.open_flag > ' => '0',
                        )
                    ));
        } else {
            $count = $_this->Document->find('count', array(
                  'conditions' => array(
                        'Document.customer_organization_id' => $cust,
                        'Document.year_month' => $year_month,
                        )
                    ));
        }
        return $count;
    }

    function getNextMonth($plan_start, $fix_send_date) {
        $d = mktime(date('H',$fix_send_date),0,0,date('n',$plan_start)+1,date('d',$fix_send_date),date('y',$plan_start));
        if (date('d',$fix_send_date) == date('d',$d))
            return $d;
        $d = strtotime('last day of -1 month', $d);
        return $d;
        //$fix_send_date = mktime(12,0,0,1,31,1970);
        //$plan_start = mktime(12,0,0,12,31,2019);
        //for ($count = 0; $count < 12; $count++){
        //    $plan_start = $this->MailMake->getNextMonth($plan_start, $fix_send_date);
        //    debug(date('Y-m-d H',$plan_start));
        //}
    }

    function setDocOpenFlag($mh, $open_flag) {
        $_this = $this->controller;
        $cust = $_this->CustomerOrganization->find('list', array(
            'fields' => array('id'),
            'conditions' => array(
                'CustomerOrganization.top_parent_id = ' => $mh['MailHistory']['customer_organization_id'],
            ),)
        );
        if ($mh['MailHistory']['document_check'] == 0) {
            $_this->Document->updateAll(
              array(
                'open_flag' => $open_flag,
                'modified_user_id' => $mh['MailHistory']['modified_user_id'],
                'modified' => "'".date('Y-m-d H:i:s')."'",
              ),
              array(
                'Document.customer_organization_id' => $cust,
                'Document.open_flag > ' => '0',
              )
            );
            return 0;
        }
        $now = strtotime($mh['MailHistory']['plan_start']);
        if ($mh['MailHistory']['document_check'] == 1) {
            $year_month = date('Ym', strtotime('last day of -1 month', $now));
        } else {
            $year_month = date('Ym', $now);
        }
        $_this->Document->updateAll(
          array(
            'open_flag' => $open_flag,
            'modified_user_id' => $mh['MailHistory']['modified_user_id'],
            'modified' => "'".date('Y-m-d H:i:s')."'",
          ),
          array(
            'Document.customer_organization_id' => $cust,
            'Document.open_flag > ' => '0',
            'Document.year_month = ' => $year_month,
          )
        );
        return 1;
    }

}
