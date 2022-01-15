<?php
class MailHistoriesController extends AppController
{
    var $uses = array(
        'MailHistory',
        'MailHistoryDetail',
        'MailTemplate',
        'User',
        'CustomerOrganization',
    );
    var $title = 'メール送信';
    var $components = array(
        'ActionCommon',
        'ActionIndex',
        'Paginator',
        'Iggy.SearchRecall',
        'MailMake',
    );
    var $helpers = array(
        'Form',
        'Iggy.Tab',
        'Iggy.Menu',
    );
    public $paginate = array();

    function index()
    {
        $this->auto_breadcrumbs = false;
        $this->breadcrumbs .= ' > 履歴一覧';
        $this->set('breadcrumbs', $this->breadcrumbs);
        $this->set(
            'fieldnames',
            fgetyml("fieldnames_{$this->request->controller}")
        );
        
        if ($this->Auth->user('group_id') < 3) {
            $this->ActionCommon->setList(
                'CustomerOrganization',
                array(
                    'conditions' => array(
                        'level' => 1,
                    ),
                    'order' => array(
                        'sort' => 'ASC',
                    ),
                )
            );
            $this->paginate['conditions'] = array(
              'MailHistory.status > 0',
            );
        } else {
            $cids = explode('|',$this->Auth->user('mps_customer_id'));
            $this->ActionCommon->setList(
                'CustomerOrganization',
                array(
                    'conditions' => array(
                        'level' => 1,
                        'id' => $cids,
                    ),
                    'order' => array(
                        'sort' => 'ASC',
                    ),
                )
            );
            $this->paginate['conditions'] = array(
              'MailHistory.status > 0',
              'MailHistory.customer_organization_id' => $cids,
            );
        }
        $option = array(
            'customer_organization_id' => '=',
        );
        $this->ActionIndex->run($option);
    }
    
    // @param integer $id
    // @param integer $success
    function output($id, $success = 0)
    {
        $extension = 'csv';
        $basename  = 'maillog' . $this->Auth->user('id') . ".{$extension}";
        if (is_writable(DOWNLOADS . $basename)) unlink(DOWNLOADS . $basename);
        $handle = fopen(DOWNLOADS . $basename, 'a');
        $titles = array(
            '送信日時',
            '第1階層',
            '第2階層',
            '第3階層',
            '第4階層',
            '第5階層',
            '名前',
            'メールアドレス',
        );
        fputcsv($handle, mbo($titles));
        unset($titles);
        $page = 1;
        $customer_organization_name = '';
        timeout();
        // @exception
        if (empty($id)) {
            $alternative = '送信予定リスト';
            // メールテンプレート確認
            $params = array(
                'fields' => array(
                    'MailTemplate.customer_organization_id',
                ),
                'conditions' => array(
                    'MailTemplate.id' => $success,
                ),
                'recursive' => -1,
            );
            $mail_template = $this->MailTemplate->find('first', $params);
            // @exception
            if (empty($mail_template)) {
                $this->flash(
                    'メールテンプレートがありません。<br />'
                    . 'メールテンプレートを作成して下さい。',
                    '/mail_templates/edit/',
                    Configure::read('App.pause')
                );
                return;
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
                    'User.mail_templates LIKE' => "%[{$success}]%",
                        'or' => array(
                            array(
                                'concat(\'|\',User.mps_customer_id,\'|\') LIKE' => "%|{$mail_template['MailTemplate']['customer_organization_id']}|%",
                                'User.group_id >' => 3,
                            ),
                            array(
                                'User.top_customer_organization_id' =>
                                    $mail_template['MailTemplate']['customer_organization_id'],
                                'User.group_id' => 3,
                            ),
                        )
                ),
                'recursive' => -1,
            );
            $params['fields'] = array(
                'User.id',
                'User.name',
                'User.top_customer_organization_id',
                'User.customer_organization_id',
                'User.group_id',
                'User.email',
                'CustomerOrganization.name',
                'User.company_name_for_mail',
            );
            $params['order'] = array(
                'User.id' => 'ASC',
            );
            $params['recursive'] = 1;
            $params['limit'] = 100;
            while ($page <= 10) {
                $params['page'] = $page;
                $details = $this->User->find('all', $params);
                if (empty($details)) break;
                foreach ($details as $detail) {
                    if ($detail['User']['group_id']==3) {
                        $path = $this->CustomerOrganization->getpatharr(
                            $detail['User']['customer_organization_id']);
                    } else if ($detail['User']['group_id']==5) {
                        $path = array($detail['User']['company_name_for_mail'],);
                    } else {
                        $path = array('リコージャパン株式会社',);
                    }
                    if (empty($customer_organization_name) && !empty($path[0])) {
                        $customer_organization_name = $path[0];
                    }
                    $record = array(
                        '',
                        (empty($path[0])) ? '' : $path[0],
                        (empty($path[1])) ? '' : $path[1],
                        (empty($path[2])) ? '' : $path[2],
                        (empty($path[3])) ? '' : $path[3],
                        (empty($path[4])) ? '' : $path[4],
                        $detail['User']['name'],
                        $detail['User']['email'],
                    );
                    fputcsv($handle, mbo($record));
                }
                $page++;
            }
        } else {
            $this->loadModel('MailHistoryDetail');
            $this->MailHistoryDetail->recursive = -1;
            $params = array(
                'fields' => array('MailHistoryDetail.*'),
                'conditions' => array(
                    'MailHistoryDetail.mail_history_id' => intval($id),
                    'MailHistoryDetail.success' => intval($success),
                ),
                'order' => array(
                    'MailHistoryDetail.id' => 'ASC',
                ),
                'limit' => 100,
            );
            
            if ($success==2) {
                $alternative = '失敗宛先リスト';
            } elseif ($success==1) {
                $alternative = '成功宛先リスト';
            } elseif ($success==0) {
                $alternative = '送信予定リスト';
            }
            
            while ($page <= 10) {
                $params['page'] = $page;
                $details = $this->MailHistoryDetail->find('all', $params);
                if (empty($details)) break;
                foreach ($details as $detail) {
                    $path = explode(
                        ' > ',
                        $detail['MailHistoryDetail']['customer_organization_path']
                    );
                    if (empty($customer_organization_name) && !empty($path[0])) {
                        $customer_organization_name = $path[0];
                    }
                    $record = array(
                        $detail['MailHistoryDetail']['send_date'],
                        (empty($path[0])) ? '' : $path[0],
                        (empty($path[1])) ? '' : $path[1],
                        (empty($path[2])) ? '' : $path[2],
                        (empty($path[3])) ? '' : $path[3],
                        (empty($path[4])) ? '' : $path[4],
                        $detail['MailHistoryDetail']['user_name'],
                        $detail['MailHistoryDetail']['to'],
                    );
                    fputcsv($handle, mbo($record));
                }
                $page++;
            }
        }
        unset($record);
        fclose($handle);
        
        $alternative = "{$customer_organization_name}_{$alternative}";
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
    
    // 宛先指定
    function address($customer_organization_id = '')
    {
        $this->auto_breadcrumbs = false;
        $this->breadcrumbs .= ' > 履歴一覧 > 宛先指定';
        $this->set('breadcrumbs', $this->breadcrumbs);
        $this->ActionCommon->setMenu();
        $this->set(
            'fieldnames',
            fgetyml("fieldnames_{$this->request->controller}")
        );
        $this->set('backurl', "/{$this->request->controller}/index/");
        if ($this->Auth->user('group_id') < 3) {
            $this->ActionCommon->setList(
                'CustomerOrganization',
                array(
                    'conditions' => array(
                        'exists (select null from mail_templates tp where tp.customer_organization_id=CustomerOrganization.id)',
                        'level' => 1,
                    ),
                    'order' => array(
                        'sort' => 'ASC',
                    ),
                )
            );
        } else {
            $this->ActionCommon->setList(
                'CustomerOrganization',
                array(
                    'conditions' => array(
                        'exists (select null from mail_templates tp where tp.customer_organization_id=CustomerOrganization.id)',
                        'level' => 1,
                        'id' => explode('|',$this->Auth->user('mps_customer_id')),
                    ),
                    'order' => array(
                        'sort' => 'ASC',
                    ),
                )
            );
        }
        $org_all = array('' => 'すべて');
        $this->viewVars['customer_organizations'] = $org_all + $this->viewVars['customer_organizations'];
        if (!empty($customer_organization_id)) {
            // 指定顧客
            $params = array(
                'fields' => array(
                    'MailTemplate.id',
                    'MailTemplate.title',
                ),
                'conditions' => array(
                    'customer_organization_id' => $customer_organization_id,
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
            $this->paginate['conditions'] = array(
              'MailHistory.status' => 0,
              'MailHistory.customer_organization_id' => $customer_organization_id,
            );
        } else {
            if ($this->Auth->user('group_id') < 3) {
                $this->paginate['conditions'] = array(
                  'MailHistory.status' => 0,
                );
            } else {
                $this->paginate['conditions'] = array(
                  'MailHistory.status' => 0,
                  'MailHistory.customer_organization_id' => explode('|',$this->Auth->user('mps_customer_id')),
                );
            }
        }
        $this->request->data['MailHistory']['customer_organization_id'] = $customer_organization_id;
        $this->ActionIndex->run();
    }
    
    // 送信確認
    function confirm($id = null)
    {
        $this->auto_breadcrumbs = false;
        $this->breadcrumbs .= ' > 履歴一覧 > 宛先指定 > 送信確認';
        $this->set('breadcrumbs', $this->breadcrumbs);
        $this->set('backurl', "/{$this->request->controller}/address");
        $this->loadModel('Document');
        $this->ActionCommon->setMenu();
        $this->set(
            'fieldnames',
            fgetyml("fieldnames_{$this->request->controller}")
        );
        if (empty($this->request->data[$this->modelClass]['send_order_count'])) {
            // 分類リスト取得
            $document_check_list = array('0'=>'報告書チェックしない', '1'=>'前月報告書チェック', '2'=>'当月報告書チェック');
            $this->set(
                'document_check_list',
                $document_check_list
            );
            if (empty($id)) {
                // @exception
                if (empty(
                    $this->request->data[$this->modelClass]['template_id']
                )) {
                    $this->redirect(array('action' => 'index'));
                }
                
                timeout();
                
                // ------------------------------------------------------------
                // 顧客名称取得
                $custOrg = $this->CustomerOrganization->findById(
                        $this->request->data[$this->modelClass]['customer_organization_id']
                );
                $this->request->data['CustomerOrganization'] = array(
                    'name' => $custOrg['CustomerOrganization']['name'],
                );

                $tid = $this->request->data[$this->modelClass]['template_id'];
                if (empty($this->request->data[$this->modelClass]['template_seq'])) {
                    if (!empty($this->request->data['tId'])) {
                        $tseq = intval(substr($this->request->data['tId'],1));
                        $this->request->data[$this->modelClass]['template_seq'] = $tseq;
                    }
                } else {
                    $tseq = $this->request->data[$this->modelClass]['template_seq'];
                }
                $this->request->data[$this->modelClass]['user_id'] = $this->Auth->user('id');
                $this->request->data[$this->modelClass]['modified_user_id'] = $this->Auth->user('id');

                $this->request->data['cUser'] = array(
                    'name' => $this->Auth->user('name'),
                );
                $this->request->data['mUser'] = array(
                    'name' => $this->Auth->user('name'),
                );
                $now = time();
                $this->request->data[$this->modelClass]['modified'] = date('Y-m-d H:i:s', $now);
                $this->request->data[$this->modelClass]['plan_start'] = date('Y-m-d H', $now + (60 * 60)) . ':0:0';
                $this->request->data[$this->modelClass]['fix_send_date'] = date('1970-1-d H', $now + (60 * 60)) . ':0:0';
                // ------------------------------------------------------------
                // プレービュー、メールサンプル表示
                $mail_sample = $this->MailMake->saveHistoryDetail($this->request->data);
                if ($mail_sample['MailHistoryDetail']['mail_history_id']<0) {
                    $this->flash(
                        $mail_sample['MailHistoryDetail']['title'],
                        $mail_sample['MailHistoryDetail']['to'],
                        Configure::read('App.pause')
                    );
                    return;
                }
            } else {
                $this->request->data = $this->model->findById($id);
                if (!empty($this->request->data['MailHistory']['send_start_date'])) {
                     $this->flash(
                         'メールは送信中です。しばらくお待ちください。',
                         "/{$this->request->controller}/index",
                         Configure::read('App.pause')
                     );
                     return;
                }
                $mail_sample = $this->MailMake->getHistoryDetail($this->request->data);
            }
            $year_month1 = date('Ym', strtotime('last day of -1 month'));
            $year_month2 = date('Ym');
            $count0 = $this->MailMake->getDocumentCnt($this->request->data[$this->modelClass]['customer_organization_id'],null);
            $count1 = $this->MailMake->getDocumentCnt($this->request->data[$this->modelClass]['customer_organization_id'],$year_month1);
            $count2 = $this->MailMake->getDocumentCnt($this->request->data[$this->modelClass]['customer_organization_id'],$year_month2);
            $this->set(compact('count0','count1','count2'));
            
            $this->request->data['MailHistory']['send_order_count'] = $mail_sample['MailHistory']['users_count'];
            $this->set('mail_sample', $mail_sample);
        } else {
            if ($this->request->data[$this->modelClass]['sendflg'] == 0) {
                $act = 1;
                $this->request->data[$this->modelClass]['plan_start'] = date('Y-m-d H:0:0');
                $this->request->data[$this->modelClass]['fix_send_date'] = date('1970-1-d H:0:0');
            } else {
                $act = 0;
                $d = $this->request->data[$this->modelClass]['plan_start'];
                $plan_start = mktime($d['hour'],0,0,$d['month'],$d['day'],$d['year']);
                $this->request->data[$this->modelClass]['plan_start'] = date('Y-m-d H:i:s', $plan_start);
                if ($this->request->data[$this->modelClass]['sendflg'] == 2) {
                    $d = $this->request->data[$this->modelClass]['fix_send_date'];
                    $plan_start = mktime($d['hour'],0,0,1,$d['day'],1970);
                    $this->request->data[$this->modelClass]['fix_send_date'] = date('Y-m-d H:i:s', $plan_start);
                } else {
                    $this->request->data[$this->modelClass]['fix_send_date'] = date('1970-1-d H:0:0', $plan_start);
                }
            }
            // メール送信履歴にレコード挿入
            $this->MailHistory->begin();
            if (empty($id)) {
                if ($act == 1) {
                    $this->request->data['MailHistory']['send_start_date'] = date('Y-m-d H:i:s');
                }
                $this->MailHistory->create(false);
                $this->MailHistory->save($this->request->data);
                $id = $this->MailHistory->id;
            } else {
                $data = $this->MailHistory->findById($id);
                if (!empty($data['MailHistory']['send_start_date'])) {
                     $this->MailHistory->commit();
                     unset($data);
                     $this->flash(
                         'メールは送信中です。しばらくお待ちください。',
                         "/{$this->request->controller}/index",
                         Configure::read('App.pause')
                     );
                     return;
                }
                $this->request->data[$this->modelClass]['modified_user_id'] = $this->Auth->user('id');
                $this->request->data[$this->modelClass]['modified'] = date('Y-m-d H:i:s');
                if ($act == 1) {
                    $this->request->data['MailHistory']['send_start_date'] = date('Y-m-d H:i:s');
                }
                $this->MailHistory->save($this->request->data);
            }
            $this->MailHistoryDetail->deleteAll(array('mail_history_id' => $id), false);
            $this->MailMake->saveHistoryDetail($this->request->data, $id);
            if ($this->request->data[$this->modelClass]['open_flag']=='doOpen'||
                $this->request->data[$this->modelClass]['document_check']>'0') {
                if ($act == 1)
                    $this->MailMake->setDocOpenFlag($this->request->data, 0);
                else
                    $this->MailMake->setDocOpenFlag($this->request->data, 2);
            }
            $this->MailHistory->commit();
            
            if ($act == 1) {
                $this->flash(
                    'メールを送信します。<br />'
                    . 'メールの送信状況は、メール送信履歴一覧で<br />確認して下さい。',
                    "/{$this->request->controller}/index",
                    Configure::read('App.pause')
                );
            } else {
                $this->flash(
                    'メール送信を予約しました。<br />'
                    . 'メールの予約状況は、メール送信一覧で<br />確認して下さい。',
                    "/{$this->request->controller}/address/" . $this->request->data['MailHistory']['customer_organization_id'],
                    Configure::read('App.pause')
                );
            }
        }
    }
    
    function delete($id = 0)
    {
        if (!empty($id)) {
            $this->MailHistory->begin();
            $data = $this->MailHistory->findById($id);
            if (!empty($data['MailHistory']['send_start_date'])) {
                 $this->MailHistory->commit();
                 unset($data);
                 $this->flash(
                     'メールは送信中です。しばらくお待ちください。',
                     "/{$this->request->controller}/index",
                     Configure::read('App.pause')
                 );
                 return;
            }
            $this->MailHistory->delete($id);
            $this->MailHistoryDetail->deleteAll(array('MailHistoryDetail.mail_history_id' => $id));
            $this->MailHistory->commit();
            $this->flash(
                'メール予約を削除しました。',
                "/{$this->request->controller}/address/" . $data['MailHistory']['customer_organization_id'],
                Configure::read('App.pause')
            );
        } else {
            $this->flash(
                'メール予約を削除しませんでした。',
                "/{$this->request->controller}/address/",
                Configure::read('App.pause')
            );
        }
    }

}
