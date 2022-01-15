<?php
class ActionViewComponent extends Component
{
    var $components = array(
        'ActionCommon',
    );
    
    function initialize(Controller $controller)
    {
        $this->controller = $controller;
    }
    
    // @param integer $id
    // @param array $options
    // boolean "set_menu"
    // @return array
    function run($id, $options = array())
    {
        $_this = $this->controller;
        $_this->helpers[] = 'Iggy.Menu';
        $_this->set(
            'fieldnames',
            fgetyml("fieldnames_{$_this->request->controller}")
        );
        extract($options, EXTR_SKIP);
        if (!isset($set_menu)) 
           $set_menu = true;
        if ($record = $_this->model->findById($id)) {
            if (!empty($_this->model->belongsToTreeModel)) {
                foreach ($_this->model->belongsToTreeModel as $modelname) {
                    $record[$modelname]['path'] =
                        $_this->model->$modelname->getpathstr(
                            $record[$modelname][
                                $_this->model->$modelname->primaryKey
                            ]
                        );
                }
            }
            $_this->set(compact('record'));
        // @exception
        } else {
            $this->ActionCommon->notFound($id);
            return;
        }
        if ($set_menu) {
            $_this->helpers[] = 'Iggy.Menu';
            $this->ActionCommon->setMenu();
            $_this->set('backurl', "/{$_this->request->controller}/index");
            if ($_this->auto_breadcrumbs) {
                $actions = fgetyml('actions');
                if (isset($actions['index'])) {
                    $_this->breadcrumbs .= ' > ' . $actions['index'];
                }
            }
            $this->ActionCommon->setAcl(array('edit', 'remove'));
        }
    }
}
