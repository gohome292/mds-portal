<?php
define('SHELLS', TMP . 'shells' . DS);
define('SHELL_UPLOADS', SHELLS . 'uploads' . DS);
App::import('Vendor', 'Iggy.filesize_format');

class AttachmentsController extends AppController
{
    var $uses = array('Attachment');
    var $title = '添付ファイル';
    var $components = array(
        'ActionCommon',
        'ActionIndex',
        'ActionRemove',
        'Paginator',
        'Iggy.SearchRecall',
    );
    var $helpers = array(
        'Iggy.Attachment',
        'Iggy.Menu',
    );
    
    function index()
    {
        $this->ActionIndex->run();
    }
    
    function add()
    {
        $this->helpers[] = 'Form';
        $this->set(
            'fieldnames',
            fgetyml("fieldnames_attachments")
        );
        $this->ActionCommon->setMenu();
        $this->set('backurl', "/attachments/index");
        
        $filenames = scandir(SHELL_UPLOADS);
        $tmp_names = array();
        foreach ($filenames as $filename) {
            if ($filename == '.' || $filename == '..') continue;
            if ($filename == '.svn' || $filename == 'empty') continue;
            $filesize = filesize_format(filesize(SHELL_UPLOADS . $filename));
            $filemtime = date(
                'Y-m-d H:i:s',
                filemtime(SHELL_UPLOADS . $filename)
            );
            $tmp_names[SHELL_UPLOADS . $filename] =
                "{$filename} [{$filesize}] [{$filemtime}]";
        }
        if (empty($tmp_names)) {
            $this->flash(
                '未登録のファイルはありません。',
                '/attachments/index',
                Configure::read('App.pause')
            );
            return;
        }
        $this->set(compact('tmp_names'));
        
        if (!empty($this->request->data['Attachment']['tmp_name'])) {
            $this->Attachment->create(false);
            $this->Attachment->set($this->request->data);
            $this->Attachment->loadValidate();
            if ($this->Attachment->save()) {
                $message = "登録しました。";
            // @exception
            } else {
                $message = "登録できませんでした。";
            }
            $this->flash(
                $message,
                '/attachments/index',
                Configure::read('App.pause')
            );
        }
    }
    
    // @param integer $id
    function remove($id)
    {
        $this->ActionRemove->run($id);
    }
}
