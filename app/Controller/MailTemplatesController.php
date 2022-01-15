<?php
App::import('Vendor', 'Iggy.fgetcsv');
App::import('Vendor', 'Iggy.convert');

class MailTemplatesController extends AppController
{
    var $uses = array(
        'MailTemplate',
        'CustomerOrganization',
        'User',
    );
    var $title = 'メール送信設定';
    var $components = array(
        'ActionAdd',
        'ActionEdit',
        'ActionCommon',
        'ActionSave',
    );

    function edit($id = null)
    {
        $this->auto_breadcrumbs = false;
        $this->breadcrumbs .= ' > 編集';
        $this->set('breadcrumbs', $this->breadcrumbs);
        
        $this->helpers[] = 'Iggy.Attachment';
        $this->helpers[] = 'Iggy.Cycle';
        
        $record = array();
        if ($this->Auth->user('group_id') < 3) {
            $params = array(
                'fields' => array(
                    'CustomerOrganization.id',
                    'CustomerOrganization.name',
                ),
                'conditions' => array(
                    'level' => '1',
                ),
                'order' => array(
                    'CustomerOrganization.lft' => 'ASC',
                ),
                'recursive' => -1,
            );
        } else {
            $params = array(
                'fields' => array(
                    'CustomerOrganization.id',
                    'CustomerOrganization.name',
                ),
                'conditions' => array(
                    'level' => '1',
                    'id' => explode('|',$this->Auth->user('mps_customer_id')),
                ),
                'order' => array(
                    'CustomerOrganization.lft' => 'ASC',
                ),
                'recursive' => -1,
            );
        }
        $customer_organizations =
            $this->CustomerOrganization->find('list', $params);
        $this->set(compact('customer_organizations'));

        // 指定テンプレート
        if (!empty($id)) {
            $params = array(
                'fields' => array(
                    'MailTemplate.id',
                    'MailTemplate.customer_organization_id'
                ),
                'conditions' => array(
                    'MailTemplate.id' => $id,
                ),
            );
            $record = $this->MailTemplate->find('first', $params);
            $customer_organization_id =
                $record['MailTemplate']['customer_organization_id'];
        // 指定顧客
        } elseif (!empty(
            $this->request->named['customer_organization_id']
        )) {
            $customer_organization_id =
                $this->request->named['customer_organization_id'];
            $params = array(
                'fields' => array(
                    'MailTemplate.id',
                ),
                'conditions' => array(
                    'customer_organization_id' => $customer_organization_id,
                ),
                'order' => array( 
                    'MailTemplate.id' => 'ASC',
                ),
                'recursive' => -1,
            );
            $record = $this->MailTemplate->find('first', $params);
        // 保存実行
        } elseif (!empty(
            $this->request->data['MailTemplate']['customer_organization_id']
        )) {
            $customer_organization_id =
                $this->request->data['MailTemplate']['customer_organization_id'];
        // 顧客指定していない
        } else {
            $customer_organization_id = key($this->viewVars['customer_organizations']);
            $this->redirect(array('action' => 'edit', 'customer_organization_id:'.$customer_organization_id));
        }

        $params = array(
            'fields' => array(
                'MailTemplate.id',
                'MailTemplate.title',
            ),
            'conditions' => array(
                'customer_organization_id' => $customer_organization_id,
            ),
            'order' => array(
                'MailTemplate.id' => 'ASC',
            ),
            'recursive' => -1,
        );
        $mail_templates =
            $this->MailTemplate->find('list', $params);
        $i=1;
        foreach ($mail_templates as $k => $v) {
           if ($i>9)
               $mail_templates[$k] = $i.' '.$v;
           else
               $mail_templates[$k] = '0'.$i.' '.$v;
           $i++;
        }
        $this->set(compact('mail_templates'));
        $options = array(
            'action' => 'edit',
            'flash'  => false,
        );
        if (!empty($this->request->data['MailTemplate']['body'])) {
            $this->request->data['MailTemplate']['body'] = str_replace(array("\r\n","\r"), "\n", $this->request->data['MailTemplate']['body']);
        };
        // add
        if (empty($record)) {
            $options['default'] = array(
                'MailTemplate' => array(
                    'customer_organization_id' => $customer_organization_id,
                ),
            );
            if ($this->ActionAdd->run($options)) {
                $this->_after($customer_organization_id, $this->model->id);
            }
        // edit
        } else {
            if ($this->ActionEdit->run(
                $record['MailTemplate']['id'],
                $options
            )) {
                $this->_after($customer_organization_id, $id);
            }
        }
    }
    
    function output()
    {
        $i=0;
        timeout();
        
        // 顧客指定していない
        if (empty($this->request->named['customer_organization_id'])) {
            $this->redirect(array('action' => 'edit'));
        // 指定顧客
        } else {
            $orgid = $this->request->named['customer_organization_id'];
            $this->CustomerOrganization->recursive = -1;
            $customer_organization = $this->CustomerOrganization->findById($orgid);
            
            $alternative =
                $customer_organization['CustomerOrganization']['name']
              . 'メールリスト';

            $params = array(
                'fields' => array(
                    'MailTemplate.id',
                    'MailTemplate.title',
                ),
                'conditions' => array(
                    'customer_organization_id' => $orgid,
                ),
                'order' => array(
                    'MailTemplate.id' => 'ASC',
                ),
                'recursive' => -1,
            );
            $mail_templates =
                $this->MailTemplate->find('list', $params);
            $i=1;
            foreach ($mail_templates as $k => $v) {
               $mail_templates[$k] = $i;
               $i++;
            }
            $params = array(
                'fields' => array(
                    'User.id',
                    'User.name',
                    'User.email',
                    'User.customer_organization_id',
                    'User.sendmail',
                    'User.comment',
                    'User.freeword1',
                    'User.freeword2',
                    'User.group_id',
                    'User.mps_customer_id',
                    'User.mail_templates',
                ),
                'conditions' => array(
                    'or' => array(
                        array(
                            'concat(\'|\',User.mps_customer_id,\'|\') LIKE' => "%|{$orgid}|%",
                            'User.group_id >' => 3,
                        ),
                        array(
                            'User.top_customer_organization_id' => $orgid,
                            'User.group_id' => 3,
                        ),
                    )
                ),
                'order' => array(
                    'User.group_id' => 'ASC',
                    'User.id' => 'ASC',
                ),
                'limit' => 100,
            );
        }
        
        $extension = 'csv';
        $basename  = 'mailadd' . $this->Auth->user('id') . ".{$extension}";
        
        if (is_writable(DOWNLOADS . $basename)) unlink(DOWNLOADS . $basename);
        $handle = fopen(DOWNLOADS . $basename, 'a');
        $titles = array(
            'ＩＤ',
            '氏名',
            'メールアドレス',
            '第1階層',
            '第2階層',
            '第3階層',
            '第4階層',
            '第5階層',
            '送信有無',
            'ユーザ別コメント',
            '自由項目1（100文字以内）',
            '自由項目2（100文字以内）',
        );
        $temp='';
        for ($i=1;$i<=20;$i++) {
            if ($i<10)
                $temp = 't0'.$i;
            else
                $temp = 't'.$i;
            $titles[] = $temp;
        }
        fputcsv($handle, mbo($titles));
        unset($titles);
        unset($temp);
        $page = 1;
        while ($page < 50) {
            $params['page'] = $page;
            $users = $this->User->find('all', $params);
            if (empty($users)) break;
            foreach ($users as $user) {
                $tmps = preg_split('/\]|\[/',$user['User']['mail_templates'],-1,PREG_SPLIT_NO_EMPTY);
                $t = array();
                foreach ($tmps as $tmp) {
                    if (!empty($mail_templates[$tmp]))
                        $t[] = $mail_templates[$tmp];
                }
                $path = $this->CustomerOrganization->getpatharr(
                    $user['User']['customer_organization_id']
                );
                $record = array(
                    $user['User']['id'],
                    $user['User']['name'],
                    $user['User']['email'],
                    (empty($path[0])) ? '' : $path[0],
                    (empty($path[1])) ? '' : $path[1],
                    (empty($path[2])) ? '' : $path[2],
                    (empty($path[3])) ? '' : $path[3],
                    (empty($path[4])) ? '' : $path[4],
                    (empty($user['User']['sendmail'])) ? '0' : '1',
                    $user['User']['comment'],
                    $user['User']['freeword1'],
                    $user['User']['freeword2'],
                );
                for ($i=1;$i<=20;$i++) {
                    if (in_array($i, $t))
                        $record[] = '1';
                    else
                        $record[] = '0';
                }
                fputcsv($handle, mbo($record));
            }
            $page++;
        }
        unset($record);
        fclose($handle);
        $this->log(mbo($alternative));
        $this->viewClass = 'Media';
        $params = array(
            'id'        => $basename,
            'name'      => mbo($alternative),
            'download'  => true,
            'extension' => $extension,
            'path'      => DOWNLOADS,
        );
        $this->set($params);
    }
    
    // @param integer $customer_organization_id
    // @return void
    function _after($customer_organization_id, $id)
    {
        // CSVインポート無し
        if (empty($this->request->data['Attachment'][0]['file']['tmp_name'])) {
            $this->flash(
                'メールテンプレートを保存しました。',
                '/mail_templates/edit/'. $id . '/',
                Configure::read('App.pause')
            );
            return;
        }
        
        timeout();
        
        $params = array(
            'fields' => array(
                'MailTemplate.id',
                'MailTemplate.title',
            ),
            'conditions' => array(
                'customer_organization_id' => $customer_organization_id,
            ),
            'order' => array(
                'MailTemplate.id' => 'ASC',
            ),
            'recursive' => -1,
        );
        $mail_templates =
            $this->MailTemplate->find('list', $params);
        $i=1;
        foreach ($mail_templates as $k => $v) {
           $mt_keys[$i] = $k;
           $i++;
        }
        $record = $this->MailTemplate->read();
        // CSVファイルパス
        $filename = UPLOADS . $record['Attachment'][0]['basename'];
        // 成功件数・失敗件数
        $count_success = $count_failure = 0;
        // ログファイル名
        $logname = 'csvimp[mail_comments]u' . $this->Auth->user('id');
        // 行数
        $line = 0;
        
        $this->User->action = 'mail_comment';
        $handle = fopen($filename, 'r');
        while ($record = fgetcsv_reg($handle)) {
            $line++;
            
            // タイトル行無視
            if (1 >= $line) continue;
            
            $record = mbi($record);
            $record = array_trim($record);
            
            // @exception
            if (empty($record[0])) {
                $log = sprintf(
                    'CSV%s行目:%s',
                    $line,
                    'IDが指定されていません。'
                );
                log_for_guest($log, $logname);
                $count_failure++;
                continue;
            }
            $user_id = $record[0];
            $userInfo = $this->User->findById($user_id);
            // ユーザが存在しない @exception
            if (empty($userInfo)) {
                $log = sprintf(
                    'CSV%s行目:%s',
                    $line,
                    'IDが不正です。'
                );
                log_for_guest($log, $logname);
                $count_failure++;
                continue;
            }
            $template = $userInfo['User']['mail_templates'];
            foreach ($mail_templates as $k => $v) {
               $template=str_replace('['.$k.']','',$template);
            }
            
            if (!empty($record[9])) {
                $record[9] = str_replace(array("\r\n","\r"), "\n", $record[9]);
                $record[9] = str_replace("\n", "\r\n", $record[9]);
            } else {
                $record[9] = '';
            }
            $sendmail = (!empty($record[8])) ? 1 : 0;
            if ($sendmail === 1) {
                //メールテンプレート取得
                for ($i=1; $i<=20; $i++) {
                   if (isset($record[$i+11])) {
                       if (!isset($mt_keys[$i]))
                           break;
                       $t = $record[$i+11];
                       $tid = $mt_keys[$i];
                       if (!empty($t)&&$t=='1'&&!empty($tid))
                           $template = $template.'['.$tid.']';
                   }
                }
            }
            $data = array(
                'id'        => $user_id,
                'sendmail'  => $sendmail,
                'comment'   => $record[9],
                'freeword1' => (isset($record[10])) ? $record[10] : '',
                'freeword2' => (isset($record[11])) ? $record[11] : '',
                'mail_templates' => $template ,
            );
            
            $data = array_nullval($data);
            $data = array('User' => $data);
            $this->User->create(false);
            if ($this->User->save($data)) {
                $count_success++;
            // @exception
            } else {
                l($this->validateErrors($this->User));
                $log = sprintf(
                    'CSV%s行目:%s',
                    $line,
                    var_export(
                        $this->validateErrors($this->User),
                        true
                    )
                );
                $log = str_replace(
                    array('freeword1', 'freeword2'),
                    array('自由項目1', '自由項目1'),
                    $log
                );
                log_for_guest($log, $logname);
                $count_failure++;
                continue;
            }
        }
        fclose($handle);
        
        $records = array();
        $records[] = array(
            'message' => 'メールテンプレートを保存しました。',
            'url'     => '/mail_templates/edit/'. $id . '/',
        );
        $records[] = array(
            'message' => 'メールリストCSVを取込しました。<br />'
                       . "成功件数：{$count_success}件　"
                       . "失敗件数：{$count_failure}件",
            'url'     => '',
        );
        if ($count_failure > 0) {
            $message = '失敗した原因をログで確認して下さい。';
            $url     = '/mail_templates/log_for_guest/';
            $pause   = 60;
        } else {
            $message = '';
            $url     = '';
            $pause   = Configure::read('App.pause');
        }
        $records[] = array(
            'message' => $message,
            'url'     => $url,
        );
        
        $this->set(compact('records'));
        $this->flash(
            '',
            $records[0]['url'],
            $pause,
            'flash_custom'
        );
    }
    
    function log_for_guest()
    {
        $extension = 'txt';
        $basename = 'guest_csvimp[mail_comments]u'
                  . $this->Auth->user('id') . ".{$extension}";
        
        // @exception
        if (!is_readable(LOGS . $basename)) {
            err('読めるログが無い。');
            die;
        }
        
        $alternative = 'メールリストCSV取込ログ';
        $this->viewClass = 'Media';
        $params = array(
            'id'        => $basename,
            'name'      => mbo($alternative),
            'download'  => true,
            'extension' => $extension,
            'path'      => LOGS,
        );
        $this->set($params);
    }
}
