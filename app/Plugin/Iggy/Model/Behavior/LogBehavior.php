<?php
App::import('Vendor', 'Iggy.get_session');

class LogBehavior extends ModelBehavior
{
    // @param object $model
    // @param boolean $created
    // @return void
    function afterSave(Model $model, $created, $options = Array())
    {
        // INSERT
        if ($created) {
            $type = 'INSERT';
        // UPDATE
        } else {
            $type = 'UPDATE';
        }
        //$fieldnames = fgetyml("fieldnames_{$model->table}");
        $content = '';
        foreach ($model->data[$model->alias] as $key => $var) {
            //$fieldname = $fieldnames[$key];
            $fieldname = $key;
            if (!is_numeric($var)) $var = "'{$var}'";
            $content .= "{$fieldname}={$var}\n";
        }
        $this->insert($model, compact('type', 'content'));
    }
    
    // @param object $model
    // @return void
    function afterDelete(Model $model)
    {
        $type = 'DELETE';
        $content = '';
        $this->insert($model, compact('type', 'content'));
    }
    
    // @param array $params
    //  string "type" : "INSERT", "UPDATE", "DELETE"
    //  string "content"
    // @return void
    function insert(Model $model, $params)
    {
        //$tablenames = fgetyml('tablenames');
        $Session =& get_session();
        extract($params, EXTR_SKIP);
        $data = array(
            'AffectedLog' => array(
                'modelname'        => $model->alias,
                'key'              => $model->id,
                'type'             => $type,
                'content'          => $content,
                'created_user_id'  => $Session->read('Auth.User.id'),
            ),
        );
        $AffectedLog = ClassRegistry::init('AffectedLog');
        $AffectedLog->create(false);
        $AffectedLog->set($data);
        $AffectedLog->save($data);
    }
}
