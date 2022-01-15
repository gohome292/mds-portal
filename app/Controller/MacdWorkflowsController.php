<?php
class MacdWorkflowsController extends AppController
{
    var $today = '';
    var $title = '月次報告一覧';
    var $uses = array(
        'MacdWorkflow',
        'User',
        'Attachment',
    );
    var $components = array(
        'ActionCommon',
        'ActionIndex',
        'ActionEdit',
        'ActionAdd',
        'ActionSave',
        'Paginator',
        'Iggy.SearchRecall',
    );
    public $paginate = array();
    var $helpers = array(
        'Iggy.Attachment',
        'Form',
    );
    var $layout = 'default.guest';
    
    function beforeFilter()
    {
        $this->set('body_id', 'MacdWorkflow');
        parent::beforeFilter();

        // お客様
        if ($this->Auth->user('group_id') == 3) {
            if (!$this->Session->check('Auth.User.access_macd_workflows_rec')) {
                $this->User->id = $this->Auth->user('id');
                $this->User->saveField(
                    'access_macd_workflows',
                    date('Y-m-d H:i:s')
                );
                $this->Session->write(
                    'Auth.User.access_macd_workflows_rec',
                    true
                );
            }
        }
    }
    
    function index()
    {
        $this->helpers[] = 'Form';
        $this->helpers[] = 'Iggy.Attachment';
        
        if (empty($this->Auth->user('top_customer_organization_id'))) {
            $this->redirect('/information/index/');
        }
        if ($this->Auth->user('group_id') <= 3) {
            $this->paginate['conditions'] = array(
                    'MacdWorkflow.customer_organization_id' =>
                        $this->Auth->user('top_customer_organization_id'),
                );
        } else {
            $this->paginate['conditions'] = array(
                    'MacdWorkflow.customer_organization_id' =>
                        $this->Auth->user('top_customer_organization_id'),
                    'MacdWorkflow.status>1',
                );
        }
        $option = array();
        $this->ActionIndex->run($option);
        if (empty($this->request->data['Attachment'])) {
            $params = array(
                'conditions' => array(
                    'model' => 'CustomerOrganization',
                    'foreign_key' => $this->Auth->user('top_customer_organization_id'),
                ),
                'order' => array(
                    'id' => 'ASC',
                ),
            );
            $record =
                    $this->Attachment->find('first', $params);
            if ($record)
                $this->request->data['Attachment'][0] = $record['Attachment'];
        } else {
            $this->Attachment->create(false);
            $record = $this->request->data['Attachment'][0];
            $record['foreign_key']=$this->Auth->user('top_customer_organization_id');
            $record['model']='CustomerOrganization';
            if ($this->Attachment->save($record,false)) {
                $message = "登録しました。";
            // @exception
            } else {
                $message = "登録できませんでした。";
            }
            $this->flash(
                $message,
                '/macd_workflows/index/',
                Configure::read('App.pause')
            );
        }
    }
    
    function edit($id = null){      
        if (empty($this->Auth->user('top_customer_organization_id'))) {
            $this->redirect('/information/index/');
        }
        // テンプレートファイル取得
        $params = array(
            'conditions' => array(
                'model' => 'CustomerOrganization',
                'foreign_key' => $this->Auth->user('top_customer_organization_id'),
            ),
            'order' => array(
                'id' => 'ASC',
            ),
        );
        $template = $this->Attachment->find('first', $params);
        $this->set('template', $template);

        if (empty($id)) {
            if (!empty($this->request->data['act'])) {
                if (empty($this->request->data['MacdWorkflow']['customer_organization_id'])) {
                    $this->request->data['MacdWorkflow']['customer_organization_id'] = 
                      $this->Auth->user('top_customer_organization_id');
                }
                if ($this->request->data['act'] == 'save') { //一時保存
                   $this->request->data['MacdWorkflow']['applied_user_id'] = $this->Auth->user('id');
                   $this->request->data['MacdWorkflow']['status'] = 1;
                } elseif ($this->request->data['act'] == 'request') { //申請
                   $this->request->data['MacdWorkflow']['applied'] = date('Y-m-d H:i:s');
                   $this->request->data['MacdWorkflow']['applied_user_id'] = $this->Auth->user('id');
                   $this->request->data['MacdWorkflow']['status'] = 2;
                }
            }
            $this->set('username',  $this->Auth->user('name'));
            $no_render = true;
            $flash = false;
            if($this->ActionAdd->run(compact('default', 'no_render', 'flash'))){
            
                $message = '保存しました。';
                //メール送信
                if($this->request->data['act'] == 'request'){
                $message = '報告書提出しました。';
                    //$applieuser = $this->User->findById(intval($this->request->data['MacdWorkflow']['applied_user_id']));
                    $cid = $this->Auth->user('top_customer_organization_id');
                    $mpsuser = $this->User->find('all', array(
                         'conditions' => array(
                             'User.disabled' => 0,
                             'concat(\'|\',User.mps_customer_id,\'|\') like concat(\'%|\','.$cid.',\'|%\')',
                             'Group.id' => 4,
                         ),
                         'limit' => 20,
                      ));

                    $body  = file_get_contents(
                        TMP."templates/email - macd_workflows.application.txt"
                    );
                    $body  = CakeText::insert(
                        $body,
                        array(
                            'user'  => $this->Auth->user('company_name_for_mail'),
                            'applied' => $this->Auth->user('person_name_for_mail'),
                            'comment' => $this->request->data['MacdWorkflow']['comment'],
                            'url' => FULL_BASE_URL . $this->base.'/' ,
                        )
                    );
                    $title = "月次報告通知：：：お客様名：{$this->Auth->user('company_name_for_mail')}　報告者名：{$this->Auth->user('person_name_for_mail')}";
                    
                    if(!$this->sendmail(compact('mpsuser', 'body', 'title'))){
                        $message .= '</br>一部送信失敗しました。';
                    };
                }
                
                $redirectId = intval($this->model->id);
                $this->flash(
                    $message,
                    "/macd_workflows/index/{$redirectId}",
                    Configure::read('App.pause')
                );
            
            } else {
                if(!empty($this->MacdWorkflow->validationErrors)){
                    $this->request->data['MacdWorkflow']['status'] = 1;// 作成中
                    $this->request->data['MacdWorkflow']['applied'] = null;
                }
                
                $this->render();
            }

            return;
        }
        if (!empty($this->request->data['act'])) {
            if (empty($this->request->data['MacdWorkflow']['customer_organization_id'])) {
                $this->request->data['MacdWorkflow']['customer_organization_id'] = 
                  $this->Auth->user('top_customer_organization_id');
            }
            if ($this->request->data['act'] == 'request') {//申請
               $this->request->data['MacdWorkflow']['applied'] = date('Y-m-d H:i:s');
               $this->request->data['MacdWorkflow']['applied_user_id'] = $this->Auth->user('id');
               $this->request->data['MacdWorkflow']['status'] = 2;
            } elseif ($this->request->data['act'] == 'reciept') {//受付
               $this->request->data['MacdWorkflow']['status'] = 3;
            } elseif ($this->request->data['act'] == 'finish') {//完了
               $this->request->data['MacdWorkflow']['status'] = 4;
            } elseif ($this->request->data['act'] == 'save') { //一時保存
               $this->request->data['MacdWorkflow']['applied_user_id'] = $this->Auth->user('id');
               $this->request->data['MacdWorkflow']['status'] = 1;
            }
        }
        if ($this->ActionEdit->run($id, array('flash' => false))) {
            //メール送信
            if($this->request->data['act'] == 'request'){
                $message = '報告書提出しました。';
                //$applieuser = $this->User->findById(intval($this->request->data['MacdWorkflow']['applied_user_id']));
                $cid = $this->Auth->user('top_customer_organization_id');
                $mpsuser = $this->User->find('all', array(
                     'conditions' => array(
                         'User.disabled' => 0,
                         'concat(\'|\',User.mps_customer_id,\'|\') like concat(\'%|\','.$cid.',\'|%\')',
                         'Group.id' => 4,
                     ),
                     'limit' => 20,
                  ));

                $body  = file_get_contents(
                    TMP."templates/email - macd_workflows.application.txt"
                );
                $body  = CakeText::insert(
                    $body,
                    array(
                        'user'  => $this->Auth->user('company_name_for_mail'),
                        'applied' => $this->Auth->user('person_name_for_mail'),
                        'comment' => $this->request->data['MacdWorkflow']['comment'],
                        'url' => FULL_BASE_URL . $this->base.'/' ,
                    )
                );
                $title = "月次報告通知：：：お客様名：{$this->Auth->user('company_name_for_mail')}　報告者名：{$this->Auth->user('person_name_for_mail')}";
                
                if(!$this->sendmail(compact('mpsuser', 'body', 'title'))){
                    $message .= '</br>一部送信失敗しました。';
                };
            } elseif($this->request->data['act'] == 'reciept') {
                $message = '受付しました。';
                $applied_user = $this->User->findById(intval($this->request->data['MacdWorkflow']['applied_user_id']));
                $cid = $this->Auth->user('top_customer_organization_id');
                $mpsuser = $this->User->find('all', array(
                     'conditions' => array(
                         'User.disabled' => 0,
                         'concat(\'|\',User.mps_customer_id,\'|\') like concat(\'%|\','.$cid.',\'|%\')',
                         'Group.id' => 4,
                     ),
                     'limit' => 20,
                  ));

                $body  = file_get_contents(
                    TMP."templates/email - macd_workflows.reception.txt"
                );
                $body  = CakeText::insert(
                    $body,
                    array(
                        'applied_data'  => $this->request->data['MacdWorkflow']['applied'],
                        'applied_title' => $this->request->data['MacdWorkflow']['applied_title'],
                        'comment' => $this->request->data['MacdWorkflow']['comment'],
                        'url' => FULL_BASE_URL . $this->base.'/' ,
                    )
                );
                $title = "月次報告受付連絡";
                if(!$this->sendmail(compact('applied_user', 'mpsuser', 'body', 'title'))){
                    $message .= '</br>一部送信失敗しました。';
                };
            } elseif($this->request->data['act'] == 'finish') {
                $message = '完了しました。';
                $applied_user = $this->User->findById(intval($this->request->data['MacdWorkflow']['applied_user_id']));
                $cid = $this->Auth->user('top_customer_organization_id');
                $mpsuser = $this->User->find('all', array(
                     'conditions' => array(
                         'User.disabled' => 0,
                         'concat(\'|\',User.mps_customer_id,\'|\') like concat(\'%|\','.$cid.',\'|%\')',
                         'Group.id' => 4,
                     ),
                     'limit' => 20,
                  ));

                $body  = file_get_contents(
                    TMP."templates/email - macd_workflows.finish.txt"
                );
                $body  = CakeText::insert(
                    $body,
                    array(
                        'applied_data'  => $this->request->data['MacdWorkflow']['applied'],
                        'applied_title' => $this->request->data['MacdWorkflow']['applied_title'],
                        'comment' => $this->request->data['MacdWorkflow']['comment'],
                        'url' => FULL_BASE_URL . $this->base.'/' ,
                    )
                );
                $title = "月次報告完了連絡";
                if(!$this->sendmail(compact('applied_user', 'mpsuser', 'body', 'title'))){
                    $message .= '</br>一部送信失敗しました。';
                };
            } else {
                $message = '保存しました。';
            }
            $redirectId = intval($this->model->id);
            $this->flash(
                $message,
                "/macd_workflows/index/{$redirectId}",
                Configure::read('App.pause')
            );
            return;
        } else {
            if ($this->Auth->user('group_id') == 3) {
                // @exception
                if ($this->request->data['MacdWorkflow']['customer_organization_id'] != 
                  $this->Auth->user('top_customer_organization_id')) {
                    error404('MacdWorkflowsController.edit');
                }
            } elseif ($this->Auth->user('group_id') > 3) {
                // @exception
                if (!in_array($this->request->data['MacdWorkflow']['customer_organization_id'],
                  explode('|',$this->Auth->user('mps_customer_id')))) {
                    error404('MacdWorkflowsController.edit');
                }
            }
            if(!empty($this->MacdWorkflow->validationErrors)){
                $this->request->data['MacdWorkflow']['status'] = 1;// 作成中
                $this->request->data['MacdWorkflow']['applied'] = null;
            }
        }
        if (empty($this->request->data['MacdWorkflow']['applied_user_id'])) {
            $this->set('username',  $this->Auth->user('name'));
        } else {
            $applieuser = $this->User->findById(intval($this->request->data['MacdWorkflow']['applied_user_id']));
            $this->set('username', $applieuser['User']['name']);
        }
    }
    
    // メール送信
    // $options array():
    // $mpsuser array()
    // $applied_user array()
    // $body
    // $title
    function sendmail($options) {
    
        $rtn = true;
        
        $body = mb_convert_encoding( $options['body'], 'ISO-2022-JP-MS', 'UTF-8' );
        if(!empty($options['applied_user'])){
            $from  = Configure::read('Mds.sendmail.from');
            $to    = $options['applied_user']['User']['email'];
            $bcc   = null;
            
            $headers = 'From: '.$from;
            $result = @mb_send_mail($to, $options['title'], $body, $headers);
            if(!$result){
                $rtn = false;
            }
        }
        
        foreach($options['mpsuser'] as $mpsuser){
            
            $from  = Configure::read('Mds.sendmail.from');
            $to    = $mpsuser['User']['email'];
            $bcc   = null;

            $headers = 'From: '.$from;
            $result = @mb_send_mail($to, $options['title'], $body, $headers);
            if(!$result){
                $rtn = false;
            }
        }
        return $rtn;
    
    }
}
