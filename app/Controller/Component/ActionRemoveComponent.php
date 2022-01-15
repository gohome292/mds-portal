<?php
class ActionRemoveComponent extends Component
{
    var $components = array(
        'ActionCommon',
    );
    var $use_simple_message = true;
    
    function initialize(Controller $controller)
    {
        $this->controller = $controller;
    }
    
    // @param integer $id
    // @return boolean
    function run($id)
    {
        $_this = $this->controller;
        $_this->model->id = $id;
        // ツリー構造
        if (in_array('Tree', $_this->model->actsAs)) {
            $children = $_this->model->children();
        }
        $result = $_this->model->delete(null, true);
        // @exception
        if (!$result && !in_array('Iggy.Disable', $_this->model->actsAs)) {
            $_this->flash(
                '削除に失敗しました。',
                "/{$_this->request->controller}/index",
                Configure::read('App.pause')
            );
            return false;
        }
        // ツリー構造
        if (in_array('Tree', $_this->model->actsAs)) {
            foreach ($children as $child) {
                $_this->model->create(false);
                $_this->model->id =
                    $child[$_this->model->alias][$_this->model->primaryKey];
                $_this->model->delete();
            }
            if (Configure::read('App.simple_message')) {
                $message = '削除しました。';
            } else {
                $message = "ID:{$id}を削除しました。\n"
                         . "ID:{$id}の子孫も同時に削除しました。";
            }
        } else {
            if (Configure::read('App.simple_message')) {
                $message = '削除しました。';
            } else {
                $message = "ID:{$id}を削除しました。";
            }
        }
        $_this->flash(
            $message,
            "/{$_this->request->controller}/index",
            Configure::read('App.pause')
        );
        return true;
    }
}
