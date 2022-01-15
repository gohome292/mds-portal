<?php
class AttachmentsController extends AppController
{
    function beforeFilter()
    {
        $this->Auth->allow('upload');
        if ($this->Auth->user('id')) {
            $this->Auth->allow('download');
        }
        $this->model = $this->{$this->modelClass};
    }
    
    // @param string $id
    // @param integer $download
    function download($id, $download = 1)
    {
        $this->model->recursive = -1;
        // @exception
        if (!$data = $this->model->findById($id)) {
            error404('Iggy.AttachmentsController.download');
        }
        if ($download) {
            $download = true;
        } else {
            $download = false;
        }
        $name = $data[$this->model->alias]['alternative'];
        // IEの場合
        if (stripos(getenv('HTTP_USER_AGENT'), 'MSIE') !== false ||
            stripos(getenv('HTTP_USER_AGENT'), 'rv:11.0') !== false) {
            $name = mbo($name);
        }
        $this->viewClass = 'Media';
        $params = array(
            'id'        => $data[$this->model->alias]['basename'],
            'name'      => $name,
            'download'  => $download,
            'extension' => $data[$this->model->alias]['extension'],
            'path'      => UPLOADS,
        );
        $this->set($params);
    }
    
    function upload()
    {
        if (Configure::read('App.auto_attach') == false) die;
        
        $client = $this->RequestHandler->getClientIP();
        
        l($this->request->data);
        // @exception
        if (empty($this->request->data['Security']['salt'])
        || $this->request->data['Security']['salt'] != Configure::read('App.salt')) {
            err(
                "client:{$client}\n不正なアクセスを受けました。",
                'auto_attach'
            );
            die;
        }
        $this->model->create(false);
        $this->model->set($this->request->data);
        
        // @exception
        if (!$this->model->validates()) {
            err(
                "client:{$client}\n不正な拡張子のファイルが指定された、"
                . 'もしくは、ファイルサイズが制限を超過しています。',
                'auto_attach'
            );
            die;
        }
        
        // @exception
        if (!$this->model->save($this->request->data, false)) {
            err(
                "client:{$client}\n"
                . '原因不明のエラーが発生し、保存出来ませんでした。',
                'auto_attach'
            );
            die;
        }
        
        $this->request->data['Attachment']['client'] = $client;
        l(
            $this->request->data,
            array(
                'name'   => 'auto_attach_' . date('Ymd'),
                'delete' => false,
            )
        );
        exit;
    }
}
