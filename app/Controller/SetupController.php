<?php
class SetupController extends AppController
{
    var $title = 'セットアップ';
    var $uses = array();
    var $components = array(
        'Setup',
    );
    
    function beforeFilter()
    {
        parent::beforeFilter();
        if (DEBUG) {
            $this->Auth->allow('*');
        } else {
            $this->Auth->deny('*');
        }
        
        $database_config = new DATABASE_CONFIG;
        $database_config = $database_config->{$this->model->useDbConfig};
        $driver = array_shift(explode('_', $database_config['driver']));
        Configure::write('App.database', $database_config['database']);
        Configure::write('App.driver', $driver);
        
        App::import('Vendor', 'Iggy.timeout');
        timeout();
        Configure::write('App.pause', 1);
    }
    
    function index($i = 0)
    {
        $modelnames = fgetyml('setup');
        $i = intval($i);
        $url = "/{$this->request->controller}/index/" . ($i + 1);
        // セットアップ準備
        if ($i === 0) {
            $filenames = scandir(UPLOADS);
            foreach ($filenames as $filename) {
                if ($filename == '.' || $filename == '..') continue;
                if ($filename == '.svn' || $filename == 'empty') continue;
                unlink(UPLOADS . $filename);
            }
            
            $tablenames = $this->Setup->getTableNames();
            foreach ($tablenames as $tablename) {
                $this->Setup->makeEmpty($tablename);
            }
            $this->Setup->buildAcl();
            $this->flash(
                'データをクリアしました。<br />'
                . '存在するコントローラとアクションを設定しました。<br />'
                . $modelnames[$i] . 'データを登録します。',
                $url,
                Configure::read('App.pause'),
                'setup'
            );
            return;
        // データ挿入
        } elseif ($i < count($modelnames)) {
            App::import('Core', 'Inflector');
            $this->Setup->setData($modelnames[($i - 1)]);
            $this->flash(
                $modelnames[($i - 1)] . 'データを登録しました。<br />'
                . $modelnames[$i] . 'データを登録します。',
                $url,
                Configure::read('App.pause'),
                'setup'
            );
            return;
        // セットアップ完了
        } else {
            $this->Setup->setData($modelnames[($i - 1)]);
            $this->Setup->setAcl();
            $this->flash(
                $modelnames[($i - 1)] . 'データを登録しました。<br />'
                . '権限を設定しました。<br />インストールが完了しました。',
                '/',
                Configure::read('App.pause'),
                'setup'
            );
            return;
        }
    }
    
    // @param string $tablename
    function record($tablename)
    {
        // @exception
        if (empty($tablename)) die('not found tablename.');
        
        App::import('Core', 'Inflector');
        $modelname = Inflector::classify($tablename);
        $this->Setup->makeEmpty($tablename);
        $this->Setup->setData($modelname);
        $this->flash(
            "{$modelname}データを登録しました。",
            '/',
            Configure::read('App.pause'),
            'setup'
        );
        return;
    }
    
    function auth()
    {
        $tablenames = array(
            'acos',
            'aros_acos',
        );
        foreach ($tablenames as $tablename) {
            $this->Setup->makeEmpty($tablename);
        }
        $this->Setup->buildAcl();
        $this->Setup->setAcl();
        $this->flash(
            '権限を設定しました。',
            '/',
            Configure::read('App.pause'),
            'setup'
        );
        return;
    }
}
