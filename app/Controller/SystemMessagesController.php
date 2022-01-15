<?php
class SystemMessagesController extends AppController
{
    var $title = 'ログイン画面メッセージ';
    var $uses = array();
    var $components = array(
        'ActionCommon',
    );
    var $helpers = array(
        'Form',
    );
    var $auto_breadcrumbs = false;
    
    function index()
    {
        $this->ActionCommon->setMenu();
        if (empty($this->request->data)) {
            $this->request->data = fgetyml('mds.system_messages');
            return;
        }
        fputyml('mds.system_messages', $this->request->data);
        $this->flash(
            'システムメッセージを更新しました',
            "/{$this->request->controller}/index/",
            Configure::read('App.pause')
        );
    }
}
