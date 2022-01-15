<?php
class ActionEditComponent extends Component
{
    var $components = array(
        'ActionCommon',
        'ActionSave',
    );
    
    function initialize(Controller $controller)
    {
        $this->controller = $controller;
    }
    
    // @param integer $id
    // @param array $options
    // string "controller"
    // string "action"
    // boolean "flash"
    // @return boolean
    function run($id, $options = array())
    {
        $_this = $this->controller;
        $_this->helpers[] = 'Form';
        $_this->helpers[] = 'Iggy.Menu';
        $_this->set(
            'fieldnames',
            fgetyml("fieldnames_{$_this->request->controller}")
        );
        extract($options, EXTR_SKIP);
        if (!isset($controller)) {
            $controller = $_this->request->controller;
        }
        if ($_this->Session->read(
            "Past.{$_this->request->controller}.action"
        ) != 'edit') {
            $_this->Session->write(
                "Past.{$_this->request->controller}.edit.action",
                $_this->Session->read(
                    "Past.{$_this->request->controller}.action"
                )
            );
        }
        if (!isset($action)) {
            if ($_this->Session->check(
                "Past.{$_this->request->controller}.edit.action"
            )) {
                $back_action = $_this->Session->read(
                    "Past.{$_this->request->controller}.edit.action"
                );
            } else {
                if (in_array('view', $_this->methods)) {
                    $back_action = 'view';
                } else {
                    $back_action = 'index';
                }
            }
        } else {
            $back_action = $action;
        }
        $_this->set('backurl', "/{$controller}/{$back_action}/{$id}");
        if ($_this->auto_breadcrumbs) {
            $back_action = str_replace('admin_', '', $back_action);
            $actions = fgetyml('actions');
            if ($back_action == 'view') {
                if (isset($actions['index'])) {
                    $_this->breadcrumbs .= ' > ' . $actions['index'];
                }
            }
            if (isset($actions[$back_action])) {
                $_this->breadcrumbs .= ' > ' . $actions[$back_action];
            }
        }
        $this->ActionCommon->setMenu();
        $this->ActionCommon->setAcl(array('remove'));
        if (empty($_this->request->data)) {
            $_this->request->data = $_this->model->findById($id);
            if ($_this->request->data) {
                if (!empty($_this->model->belongsToTreeModel)) {
                    foreach ($_this->model->belongsToTreeModel as $modelname) {
                        $_this->request->data[$modelname]['path'] =
                            $_this->model->$modelname->getpathstr(
                                $_this->request->data[$modelname][
                                    $_this->model->$modelname->primaryKey
                                ]
                            );
                    }
                }
            // @exception
            } else {
                $this->ActionCommon->notFound($id);
                return false;
            }
            // ツリー構造
            if (in_array('Tree', $_this->model->actsAs)) {
                $_this->request->data[$_this->modelClass]['path'] =
                    $_this->model->getpathstr($id, false);
            }
            return false;
        }
        if (empty(
            $_this->request->data[$_this->modelClass][$_this->model->primaryKey]
        )) {
            $_this->request->data[$_this->modelClass][$_this->model->primaryKey] = $id;
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
            if (!isset($_this->request->data['Attachment'])) return false;
            if ($_data = $_this->model->findById($id)) {
                $_this->request->data['Attachment'] = $_data['Attachment'];
            }
            return false;
        }
    }
}
