<?php
class ActionAddComponent extends Component
{
    var $components = array(
        'ActionCommon',
        'ActionSave',
    );
    
    function initialize(Controller $controller)
    {
        $this->controller = $controller;
    }
    
    // @param array $options
    // array "default"
    // boolean "no_render"
    // string "controller"
    // string "action"
    // boolean "flash"
    // @return boolean
    function run($options = array())
    {
        $_this = $this->controller;
        $_this->helpers[] = 'Form';
        $_this->helpers[] = 'Iggy.Menu';
        $_this->set(
            'fieldnames',
            fgetyml("fieldnames_{$_this->request->controller}")
        );
        extract($options, EXTR_SKIP);
        if (!isset($controller))
            $controller = $_this->request->controller;
        $_this->set('backurl', "/{$controller}/index");
        if ($_this->auto_breadcrumbs) {
            $actions = fgetyml('actions');
            if (isset($actions['index'])) {
                $_this->breadcrumbs .= ' > ' . $actions['index'];
            }
        }
        $this->ActionCommon->setMenu();
        if (empty($_this->data)) {
            if (!empty($default)) $_this->data = $default;
            if (empty($no_render)) $_this->render('edit');
            return false;
        }
        if ($this->ActionSave->run($options)) {
            if (!isset($flash))
               $flash = true;
            if ($flash) {
                if (Configure::read('App.simple_message')) {
                    $message = '保存しました。';
                } else {
                    $message = "ID:{$_this->model->id}を保存しました。";
                }
                if (!isset($action)) {
                    if (in_array('view', $_this->methods)) {
                        $action = 'view';
                    } else {
                        $action = 'index';
                    }
                }
                $_this->flash(
                    $message,
                    "/{$controller}/{$action}/{$_this->model->id}",
                    Configure::read('App.pause')
                );
            }
            return true;
        // @exception
        } else {
            if (empty($no_render)) $_this->render('edit');
            return false;
        }
    }
}
