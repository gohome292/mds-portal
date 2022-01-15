<?php
class UsersController extends AppController
{
    var $title = 'ユーザ管理';
    var $components = array(
        'ActionCommon',
        'ActionIndex',
        'ActionView',
        'ActionAdd',
        'ActionEdit',
        'ActionRemove',
        'ActionSave',
        'RequestHandler',
        'Paginator',
        'Iggy.SearchRecall',
    );
    var $helpers = array(
        'Iggy.Tab',
        'Mds',
    );
    public $paginate = array();

    function beforeFilter()
    {
        if ($this->request->action == 'self_edit') {
            $this->title = 'アカウント編集';
        }
        parent::beforeFilter();
        $this->Auth->allow(array('login', 'logout', 'request_reset', 'reset'));
        $this->Auth->autoRedirect = false;
        $this->model->action = $this->request->action;
        if ($this->model->hasField('login')) {
            $this->Auth->loginRedirect = '/users/login_rec';
        }
        $this->Auth->logoutRedirect = '/';
    }
    
    function login()
    {
        // ログイン済み
        if ($this->Auth->user('id')) {
            // @exception
            if ($this->Session->check('Auth.User.login_rec')) {
                $this->redirect(Configure::read('App.loginRedirect'));
                die;
            } else {
                $this->setAction('login_rec');
                return;
            }
        }
        if ($this->request->is('post')) {
            // Important: Use login() without arguments! See warning below.
            if ($this->Auth->login()) {
                $this->setAction('login_rec');
                return;
            }
            $this->Session->setFlash(__('Username or password is incorrect'), 'default', array(), 'auth');
        }
        $this->set(
            'fieldnames',
            fgetyml("fieldnames_{$this->request->controller}")
        );
        
        // システムメッセージ
        $this->set('system_messages', fgetyml('mds.system_messages'));
        
        $this->set('ClientIP', $this->RequestHandler->getClientIP());
        
        $this->helpers[] = 'Form';
        $this->helpers[] = 'Iggy.RichText';
        $this->layout  = 'login.guest';
        $this->title = Configure::read('App.name') . ' - ログイン';
        $this->render('login.guest');
    }
    
    function login_rec()
    {
        // @exception
        if ($this->Session->check('Auth.User.login_rec')) {
            $this->redirect(Configure::read('App.loginRedirect'));
            die;
        }
        $this->model->id = $this->Auth->user('id');
        $this->model->saveField('login', date('Y-m-d H:i:s'));
        session_regenerate_id();
        $this->Session->write('Auth.User.login_rec', true);
        
        // お客様
        if ($this->Auth->user('group_id') == 3) {
            Configure::write(
                'App.loginRedirect',
                Configure::read('Mds.loginRedirect.guest')
            );
        // MPSユーザー
        } elseif ($this->Auth->user('group_id') == 4) {
            Configure::write(
                'App.loginRedirect',
                Configure::read('Mds.loginRedirect.mps')
            );
        // SAユーザー
        } elseif ($this->Auth->user('group_id') == 5) {
            Configure::write(
                'App.loginRedirect',
                Configure::read('Mds.loginRedirect.sa')
            );
        // RJ・RTS
        } else {
            Configure::write(
                'App.loginRedirect',
                Configure::read('Mds.loginRedirect.admin')
            );
        }
        // お客様
        if ($this->Auth->user('group_id') == 3) {
            // お知らせ閲覧権限判定の為、階層レベルを取得
            $this->loadModel('CustomerOrganization');
            $this->CustomerOrganization->id =
                $this->Auth->user('customer_organization_id');
            $this->Session->write(
                'Auth.User.level',
                $this->CustomerOrganization->field('level')
            );
            
            // お客様提供サービスを取得
            $this->loadModel('CustomerNav');
            $nav = $this->CustomerNav->findById($this->Auth->user('top_customer_organization_id'));
            if (empty($nav)) {
                $nav['CustomerNav'] = array('start_year_month' => date('Y/m'),
                    'documents' => false,'equipments' => false,'drivers' => false,'manuals' => false,'macd_workflows' => false);
            }
            $this->Session->write('Auth.User.nav', $nav['CustomerNav']);
        }
        
        if (Configure::read('App.loginAutoRedirect')
        && $this->Session->check('Auth.redirect')) {
            $this->redirect($this->Session->read('Auth.redirect'));
            die;
        } else {
            $this->redirect(Configure::read('App.loginRedirect'));
            die;
        }
    }
    
    function logout()
    {
        // ログアウト日時を記録
        if ($this->model->hasField('logout')) {
            $id = $this->Auth->user('id');
            // @exception
            if (empty($id)) {
                $url = '/';
                $this->flash(null, $url, 0, 'flash.logout.guest');
                return;
            }
            $this->model->create(false);
            $this->model->id = $id;
            $this->model->saveField('logout', date('Y-m-d H:i:s'));
        }
        $this->Auth->logout();
        $this->Session->destroy();
//          $this->redirect('/');
        $url = '/';
//        $this->flash(null, $url, 0, 'flash.logout.guest');
        $this->layout  = 'flash.logout.guest';
        $this->render('logout');

    }
    
    function index()
    {
        if (Configure::read('App.maintenance.user_id')) {
            // メンテナンスユーザは一覧に表示しない
            $field = "{$this->model->name}.{$this->model->primaryKey} <>";
            $this->paginate['conditions'][$field] =
                Configure::read('App.maintenance.user_id');
        }
        $this->ActionCommon->setList('Group');
        $this->ActionCommon->setList(
            'CustomerOrganization',
            array(
                'conditions' => array(
                    'parent_id' => null,
                ),
                'order' => array(
                    'lft' => 'ASC',
                ),
            )
        );
        $option = array(
            'name' => 'LIKE',
            'group_id' => '=',
            'top_customer_organization_id' => '=',
        );
        $this->ActionIndex->run($option);
    }
    
    // @param integer $id
    function view($id)
    {
        if (Configure::read('App.maintenance.user_id')) {
            // 編集対象がメンテナンスユーザ @exception
            if ($id == Configure::read('App.maintenance.user_id')) {
                $this->ActionCommon->notFound($id);
                return;
            }
        }
        $this->ActionView->run($id);
        $this->ActionCommon->setList(
            'CustomerOrganization',
            array(
                'conditions' => array(
                    'level' => 1,
                    'id' => explode('|',$this->viewVars['record']['User']['mps_customer_id']),
                ),
                'order' => array(
                    'sort' => 'ASC',
                ),
            )
        );
    }
    
    function add()
    {
        $this->ActionCommon->setList('Group');
        if (!empty($this->request->data['User']['password1'])) {
            $this->model->password = $this->Auth->password(
                $this->request->data['User']['password1']
            );
        }
        $default = array(
            'User' => array('group_id' => 3),
        );
        $this->ActionCommon->setList(
            'CustomerOrganization',
            array(
                'conditions' => array(
                    'level' => 1,
                ),
                'fields' => array('id', 'name'), 
                'order' => array(
                    'sort' => 'ASC',
                ),
            )
        );
        
        // 権限がお客様以外
        if (!empty($this->request->data['User']['group_id'])
        && $this->request->data['User']['group_id'] != 3) {
            unset($this->request->data['CustomerOrganization']['path']);
            $this->request->data['User']['customer_organization_id'] = NULL;
            $this->request->data['User']['person_name_for_mail']     = NULL;
            if ($this->request->data['User']['group_id'] != 5 ) {
                $this->request->data['User']['company_name_for_mail']    = NULL;
            }
        }
        
        
        if ($this->ActionAdd->run(array(
            'default'   => $default,
            'no_render' => true,
        ))) return;
        unset(
            $this->request->data['User']['password1'],
            $this->request->data['User']['password2']
        );
        
        
        //if (!empty($_POST)) {
        //    $this->set('errors', $this->User->invalidFields());
        //}
        
        
        $this->render('edit');
    }
    
    // @param integer $id
    function edit($id)
    {
        $this->ActionCommon->setList('Group');
        if (Configure::read('App.maintenance.user_id')) {
            // 編集対象がメンテナンスユーザ @exception
            if ($id == Configure::read('App.maintenance.user_id')) {
                $this->ActionCommon->notFound($id);
                return;
            }
        }
        $this->ActionCommon->setList(
            'CustomerOrganization',
            array(
                'conditions' => array(
                    'level' => 1,
                ),
                'fields' => array('id', 'name'), 
                'order' => array(
                    'sort' => 'ASC',
                ),
            )
        );
        if (!empty($this->request->data['User']['password1'])) {
            $this->model->password = $this->Auth->password(
                $this->request->data['User']['password1']
            );
        }
        
        // 権限がお客様以外
        if (!empty($this->request->data['User']['group_id'])) {
          if ($this->request->data['User']['group_id'] == 3) {
            $this->request->data['User']['mps_customer_id'] = NULL;
          } elseif ($this->request->data['User']['group_id'] < 3) {
            unset($this->request->data['CustomerOrganization']['path']);
            $this->request->data['User']['customer_organization_id'] = NULL;
            $this->request->data['User']['company_name_for_mail']    = NULL;
            $this->request->data['User']['person_name_for_mail']     = NULL;
            $this->request->data['User']['mps_customer_id'] = NULL;
          } elseif ($this->request->data['User']['group_id'] == 5) {
            unset($this->request->data['CustomerOrganization']['path']);
            $this->request->data['User']['customer_organization_id'] = NULL;
            $this->request->data['User']['person_name_for_mail']     = NULL;
          } else {
            unset($this->request->data['CustomerOrganization']['path']);
            $this->request->data['User']['customer_organization_id'] = NULL;
            $this->request->data['User']['company_name_for_mail']    = NULL;
            $this->request->data['User']['person_name_for_mail']     = NULL;
          }
        }
        
        if ($this->ActionEdit->run($id)) return;
        unset(
            $this->request->data['User']['password1'],
            $this->request->data['User']['password2']
        );
        
        
        //if (!empty($_POST)) {
        //    $this->set('errors', $this->User->invalidFields());
        //}
    }
    
    // @param integer $id
    function remove($id)
    {
        if (Configure::read('App.maintenance.user_id')) {
            // 編集対象がメンテナンスユーザ @exception
            if ($id == Configure::read('App.maintenance.user_id')) {
                $this->ActionCommon->notFound($id);
                return;
            }
        }
        // 削除対象が自分自身 @exception
        if ($id == $this->Auth->user('id')) {
            $this->flash(
                'あなた自身を削除することは出来ません。',
                "/{$this->request->controller}",
                Configure::read('App.pause')
            );
            return;
        }
        $this->ActionRemove->run($id);
    }
    
    // アカウント編集
    function self_edit()
    {
        // お客様
        if ($this->Auth->user('group_id') >= 3) {
            $this->_self_edit_for_guest();
            return;
        }
        $this->helpers[] = 'Form';
        $this->helpers[] = 'Iggy.Menu';
        $this->ActionCommon->setMenu();
        $this->set(
            'fieldnames',
            fgetyml("fieldnames_{$this->request->controller}")
        );
        $this->model->id = $this->Auth->user('id');
        if (empty($this->request->data)) {
            $this->request->data = $this->model->read();
            return;
        }
        if (!empty($this->request->data['User']['password1'])) {
            $this->model->password = $this->Auth->password(
                $this->request->data['User']['password1']
            );
        }
        // 指定ユーザIDと自分ユーザIDが異なる @exception
        if ($this->request->data['User']['id'] != $this->model->id) {
            $this->redirect('/');
            die;
        }
        // @exception
        if (!$this->ActionSave->run()) {
            unset(
                $this->request->data['User']['password1'],
                $this->request->data['User']['password2']
            );
            $this->render('self_edit');
            return;
        }
        /*$this->model->recursive = -1;
        $Auth = $this->model->read();
        unset($Auth['User']['password']);
        $Auth['User']['login_rec'] = true;
        $this->Session->write('Auth', $Auth);*/
        $this->flash(
            '保存しました。',
            Configure::read('App.loginRedirect'),
            Configure::read('App.pause')
        );
    }
    
    function _self_edit_for_guest()
    {
        $this->set('body_id', 'Pwchg');
        $this->helpers[] = 'Form';
        $this->helpers[] = 'Iggy.Menu';
        $this->ActionCommon->setMenu();
        $this->set(
            'fieldnames',
            fgetyml("fieldnames_{$this->request->controller}")
        );
        $this->model->id = $this->Auth->user('id');
        if (empty($this->request->data)) {
            $this->request->data = $this->model->read();
            $this->layout = 'default.guest';
            $this->render('self_edit.guest');
            return;
        }
        if (!empty($this->request->data['User']['password1'])) {
            $this->model->password = $this->Auth->password(
                $this->request->data['User']['password1']
            );
        }
        // 指定ユーザIDと自分ユーザIDが異なる @exception
        if ($this->request->data['User']['id'] != $this->model->id) {
            $this->redirect('/');
            die;
        }
        // @exception
        if (!$this->ActionSave->run()) {
            unset(
                $this->request->data['User']['password1'],
                $this->request->data['User']['password2']
            );
            // お客様
            if ($this->Auth->user('group_id') == 3) {
                $this->layout = 'default.guest';
                $this->render('self_edit.guest');
            // RJ・RTS
            } else {
                $this->render('self_edit');
            }
            return;
        }
        /*$this->model->recursive = -1;
        $Auth = $this->model->read();
        unset($Auth['User']['password']);
        $Auth['User']['login_rec'] = true;
        $this->Session->write('Auth', $Auth);*/
        unset(
            $this->request->data['User']['password1'],
            $this->request->data['User']['password2']
        );
        $this->Session->setFlash('パスワードを変更しました。');
        $this->layout = 'default.guest';
        $this->render('self_edit.guest');
    }
    
    // 閲覧ログ出力
    function output_log()
    {
        timeout();
        
        $params = array(
            'fields' => array(
                'User.customer_organization_id',
                'User.name',
                'User.login',
                'User.access_information',
                'User.access_documents',
                'User.access_macd_workflows',
                'User.access_drivers',
                'User.access_manuals',
            ),
            'conditions' => array(
                'User.group_id' => 3, // お客様
            ),
            'order' => array(
                'CustomerOrganization.lft' => 'ASC',
            ),
            'limit' => 100,
        );
        
        $alternative = '閲覧ログ';
        $extension = 'csv';
        $basename  = 'accesslog' . $this->Auth->user('id') . ".{$extension}";
        
        if (is_writable(DOWNLOADS . $basename)) unlink(DOWNLOADS . $basename);
        $handle = fopen(DOWNLOADS . $basename, 'a');
        $titles = array(
            '第1階層',
            '第2階層',
            '第3階層',
            '第4階層',
            '第5階層',
            '名前',
            'ログイン日付',
            'ログイン時間',
            'お知らせ閲覧日付',
            'お知らせ閲覧時間',
            '報告書閲覧日付',
            '報告書閲覧時間',
            'MACD申請アクセス日付',
            'MACD申請アクセス時間',
            'プリンタドライバー閲覧日付',
            'プリンタドライバー閲覧時間',
            'マニュアル閲覧日付',
            'マニュアル閲覧時間',
        );
        fputcsv($handle, mbo($titles));
        unset($titles);
        $keys = array('login', 'access_information', 'access_documents', 'access_macd_workflows', 'access_drivers', 'access_manuals' );
        $page = 1;
        while ($page <= 10) {
            $params['page'] = $page;
            $users = $this->User->find('all', $params);
            if (empty($users)) break;
            foreach ($users as $user) {
                $path = $this->User->CustomerOrganization->getpatharr(
                    $user['User']['customer_organization_id']
                );
                $vars = array();
                foreach ($keys as $key) {
                    if (empty($user['User'][$key])) {
                        $vars["{$key}_date"] = '';
                        $vars["{$key}_time"] = '';
                    } else {
                        $vars["{$key}_date"] =
                            substr($user['User'][$key], 0, 10);
                        $vars["{$key}_time"] =
                            substr($user['User'][$key], 11, 8);
                    }
                }
                $record = array(
                    (empty($path[0])) ? '' : $path[0],
                    (empty($path[1])) ? '' : $path[1],
                    (empty($path[2])) ? '' : $path[2],
                    (empty($path[3])) ? '' : $path[3],
                    (empty($path[4])) ? '' : $path[4],
                    $user['User']['name'],
                    $vars['login_date'],
                    $vars['login_time'],
                    $vars['access_information_date'],
                    $vars['access_information_time'],
                    $vars['access_documents_date'],
                    $vars['access_documents_time'],
                    $vars['access_macd_workflows_date'],
                    $vars['access_macd_workflows_time'],
                    $vars['access_drivers_date'],
                    $vars['access_drivers_time'],
                    $vars['access_manuals_date'],
                    $vars['access_manuals_time'],
                );
                fputcsv($handle, mbo($record));
            }
            $page++;
        }
        unset($record);
        fclose($handle);
        
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
    
    /*
        パスワード初期化申請
        @return void
    */
    function request_reset()
    {
        $this->layout = 'simple';
        $this->title = 'パスワード初期化申請';
        $this->helpers[] = 'Form';
        
        // 初期アクセス
        if (empty($this->request->data)) return;
        
        // ログインIDに入力なし @exception
        if (empty($this->request->data['User']['username'])) {
            $this->Session->setFlash('ログインIDを入力してください。');
            return;
        }
        // メールアドレス不一致(確認用と) @exception
        if ($this->request->data['User']['email'] != $this->request->data['User']['email2']) {
            $this->Session->setFlash('メールアドレスに誤りがあります。');
            return;
        }
        
        $user = $this->User->findByUsername($this->request->data['User']['username']);
        
        // 指定ログインIDのユーザーが存在しない @exception
        if (empty($user)) {
            $this->Session->setFlash('入力内容に誤りがあります。');
            return;
        }
        // メールアドレス不一致(DBと) @exception
        if ($user['User']['email'] != $this->request->data['User']['email']) {
            $this->Session->setFlash('入力内容に誤りがあります。');
            return;
        }
        
        // バリデート処理終了.
        // メール送信処理開始.
        
        $key = md5(
            $user['User']['username'].
            $user['User']['email'].
            date('Ymd')
        );
        
        $from  = Configure::read('Mds.sendmail.from');
        $to    = $user['User']['email'];
        $bcc   = null;
        $title = "パスワード初期化申請受付完了";
        $body  = file_get_contents(
            TMP."templates/email - users.request_reset - confirm.txt"
        );
        $body  = CakeText::insert(
            $body,
            array(
                'url'  => FULL_BASE_URL . $this->base."/users/reset/1{$user['User']['id']}/{$key}/",
                'date' => date('Y').'年'.date('n').'月'.date('j').'日',
                'from' => Configure::read('Mds.sendmail.from'),
            )
        );
        
        $headers = 'From: '.Configure::read('Mds.sendmail.from');
        $result = @mb_send_mail($to, $title, $body, $headers);
        
        // メール送信失敗 @exception
        if ($result === false) {
            err(
                "[パスワード初期化]メール送信失敗／IP:".
                $this->RequestHandler->getClientIP()
            );
            $this->Session->setFlash(
                'メールの送信に失敗しました。'.
                'リコー窓口担当者にご連絡ください。'
            );
            
            $from  = Configure::read('Mds.sendmail.from');
            $to    = Configure::read('Mds.sendmail.from');
            $bcc   = Configure::read('Mds.sendmail.bccOnErr');
            $title = "【MDSポータルサイト】エラー発生";
            $body  = file_get_contents(
                TMP."templates/email - users.request_reset - error.txt"
            );
            $body  = CakeText::insert(
                $body,
                array(
                    'username' => $user['User']['username'],
                    'email'    => $user['User']['email'],
                    'name'     => $user['User']['name'],
                    'top_customer_organization_name' =>
                        $user['TopCustomerOrganization']['name'],
                )
            );
            
            $headers = 'From: '.Configure::read('Mds.sendmail.from');
            $result = @mb_send_mail($to, $title, $body, $headers);
            return;
        }
        
        $this->flash(null, '/', 0, 'flash.request_reset.guest');
    }
    
    /*
        パスワード初期化実行
        @param integer $user_id
        @param string $receive_key md5ハッシュ値
        @return void
    */
    function reset($user_id = NULL, $receive_key = NULL)
    {
        // ログインIDかキーがない @exception
        if (empty($user_id) || empty($receive_key)) {
            err(
                "[パスワード初期化]ログインIDかキーがない／IP:".
                $this->RequestHandler->getClientIP()
            );
            $this->flash(null, '/', 0, 'flash.wrong.guest');
            return;
        }
        
        $user = $this->User->findById(substr($user_id, 1));
        
        // 指定ログインIDのユーザーが存在しない @exception
        if (empty($user)) {
            err(
                "[パスワード初期化]指定ログインIDのユーザーが存在しない／IP:".
                $this->RequestHandler->getClientIP()
            );
            $this->flash(null, '/', 0, 'flash.wrong.guest');
            return;
        }
        $key = md5(
            $user['User']['username'].
            $user['User']['email'].
            date('Ymd')
        );
        $key2 = md5(
            $user['User']['username'].
            $user['User']['email'].
            date('Ymd', strtotime('-1 day'))
        );
        
        // キー不一致 @exception
        if ($receive_key != $key && $receive_key != $key2) {
            err(
                "[パスワード初期化]キー不一致／IP:".
                $this->RequestHandler->getClientIP()
            );
            $this->flash(null, '/', 0, 'flash.wrong.guest');
            return;
        }
        
        // バリデート処理終了.
        // パスワード初期化処理開始.
        
        $passwd = substr(md5($key.mt_rand(0, 1000)), 5, 16);
        $this->User->id = $user['User']['id'];
        $this->User->saveField('password', $this->Auth->password($passwd));
        
        $from  = Configure::read('Mds.sendmail.from');
        $to    = $user['User']['email'];
        $bcc   = null;
        $title = "パスワード初期化完了";
        $body  = file_get_contents(
            TMP."templates/email - users.reset - fin.txt"
        );
        $body  = CakeText::insert(
            $body,
            array(
                'password' => $passwd,
                'date'     => date('Y').'年'.date('n').'月'.date('j').'日',
                'from'     => Configure::read('Mds.sendmail.from'),
            )
        );
        
        $headers = 'From: '.Configure::read('Mds.sendmail.from');
        $result = @mb_send_mail($to, $title, $body, $headers);
        
        // メール送信失敗 @exception
        if ($result === false) {
            err(
                "[パスワード初期化]メール送信失敗／IP:".
                $this->RequestHandler->getClientIP()
            );
            $this->Session->setFlash(
                'メールの送信に失敗しました。'.
                'リコー窓口担当者にご連絡ください。'
            );
            
            $from  = Configure::read('Mds.sendmail.from');
            $to    = Configure::read('Mds.sendmail.from');
            $bcc   = Configure::read('Mds.sendmail.bccOnErr');
            $title = "【MDSポータルサイト】エラー発生";
            $body  = file_get_contents(
                TMP."templates/email - users.reset - error.txt"
            );
            $body  = CakeText::insert(
                $body,
                array(
                    'username' => $user['User']['username'],
                    'email'    => $user['User']['email'],
                    'name'     => $user['User']['name'],
                    'top_customer_organization_name' =>
                        $user['TopCustomerOrganization']['name'],
                )
            );
            
            $headers = 'From: '.Configure::read('Mds.sendmail.from');
            $result = @mb_send_mail($to, $title, $body, $headers);
            return;
        }
        
        $this->flash(null, '/', 0, 'flash.reset.guest');
    }
}
