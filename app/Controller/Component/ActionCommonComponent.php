<?php
class ActionCommonComponent extends Component
{
    function initialize(Controller $controller)
    {
        $this->controller = $controller;
    }
    
    // メインメニュー設置
    // @return void
    function setMenu()
    {
        $_this = $this->controller;
        $_this->helpers[] = 'Iggy.Tree';
        if (!$_this->Session->check('Menu')) {
            $fields = array(
                'Menu.link',
            );
            $conditions = array(
                'and' => array(
                    array(
                        'or' => array(
                            'Menu.groups'      => null,
                            'Menu.groups LIKE' =>
                                "%[{$_this->Auth->user('group_id')}]%",
                        ),
                    ),
                    array(
                        'or' => array(
                            'Menu.users'      => null,
                            'Menu.users LIKE' =>
                                "%[{$_this->Auth->user('id')}]%",
                        ),
                    ),
                ),
            );
            $_this->Session->write(
                'Menu',
                $_this->Menu->getTree(compact('fields', 'conditions'))
            );
        }
        $_this->set('mainmenus', $_this->Session->read('Menu'));
    }
    
    // @param array $actions
    // @return void
    function setAcl($actions)
    {
        $_this = $this->controller;
        $acl = array();
        
        $aros = array(
            'Group' => 'group_id',
            'User'  => 'id',
        );
        foreach ($aros as $modelname => $fieldname) {
            $aro = "{$modelname}::" . $_this->Auth->user($fieldname);
            foreach ($actions as $action) {
                if (!empty($acl[$action])) continue;
                if (!in_array($action, $_this->methods)) {
                    $acl[$action] = false;
                    continue;
                }
                $aco = "controllers/{$_this->name}/{$action}";
                $acl[$action] = $_this->Acl->check($aro, $aco);
            }
        }
        $_this->set(compact('acl'));
    }
    
    // IDが存在しない時の例外処理
    // @param integer $id
    // @return void
    function notFound($id)
    {
        $_this =& $this->controller;
        
        if (Configure::read('App.simple_message')) {
            $message = '指定された情報はありません。';
        } else {
            $message = "ID:{$id}は存在しません。";
        }
        $_this->flash(
            $message,
            "/{$_this->request->controller}",
            Configure::read('App.pause')
        );
        return;
    }
    
    // 選択肢作成
    // @param string $modelname
    // @param array $params
    // @return void
    function setList($modelname, $params = array())
    {
        $_this = $this->controller;
        if (!isset($_this->$modelname)) $_this->loadModel($modelname);
        $_this->$modelname->recursive = -1;
        $_this->set(
            $_this->$modelname->table,
            $_this->$modelname->find('list', $params)
        );
    }
    
    // 選択肢作成[名称マスタ用]
    // @param string $modelname
    // @return void
    function setListNM($modelname)
    {
        $this->_setList($modelname);
    }
    
    // 選択肢作成[裏名称マスタ用]
    // @param string $modelname
    // @return void
    function setListINM($modelname)
    {
        $this->_setList($modelname, true);
    }
    
    // @param string $modelname
    // @param boolean $inside
    // @return void
    function _setList($modelname, $inside = false)
    {
        $_this =& $this->controller;
        $prefix = '';
        if ($inside) $prefix = 'Inside';
        $basemodel = "{$prefix}NameMaster";
        if (!isset($_this->$basemodel)) $_this->loadModel($basemodel);
        $_this->$basemodel->recursive = 0;
        $params = array(
            'fields' => array(
                "{$basemodel}.id",
                "{$basemodel}.name",
            ),
            'conditions' => array(
                "{$prefix}NameDivision.model" => $modelname,
            ),
            'order' => array(
                "{$basemodel}.sort" => 'ASC',
            ),
        );
        $records = $_this->$basemodel->find('all', $params);
        App::import('Core', 'Inflector');
        App::import('Core', 'Set');
        $_this->set(
            Inflector::tableize($modelname),
            Set::combine(
                $records,
                "{n}.{$basemodel}.id",
                "{n}.{$basemodel}.name"
            )
        );
    }
}
