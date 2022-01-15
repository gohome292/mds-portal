<?php
class AppValidationBehavior extends ModelBehavior
{
    var $_settings = array(
        'disable_field' => 'disabled',
    );
    
    // 一意な値どうか判定
    // @param object $model
    // @param array $data
    // @return boolean or string
    function unique(&$model, $data)
    {
        $field = key($data);
        $value = $data[$field];
        $params = array(
            'conditions' => array(
                "{$model->alias}.{$field}" => $value,
            ),
            'recursive' => -1,
        );
        if (!empty($model->data[$model->alias][$model->primaryKey])) {
            $id = $model->data[$model->alias][$model->primaryKey];
        }
        if (!empty($id)) {
            $field = "{$model->alias}.{$model->primaryKey} <>";
            $params['conditions'][$field] = $id;
        }
        if ($model->hasField($this->_settings['disable_field'])) {
            $field = "{$model->alias}.{$this->_settings['disable_field']}";
            $params['conditions'][$field] = false;
        }
        if ($model->find('count', $params) === 0) return true;
        // @exception
        return 'この値は既に登録済です';
    }
    
    // 指定テーブルに存在する値(ID)かどうか判定
    // @param object $model
    // @param array $data
    // @param string $modelname
    // @return boolean or string
    function inListDB(&$model, $data, $modelname = null)
    {
        $fieldname = key($data);
        $id = $data[$fieldname];
        if (empty($id)) {
            return true;
        }
        if (is_array($modelname)) {
            $modelname = str_replace('_id', '', $fieldname);
            App::import('Core', 'Inflector');
            $modelname = Inflector::camelize($modelname);
        }
        if ($model->alias == $modelname) {
            $_model =& $model;
        } else {
            App::import('Model', $modelname);
            $_model = new $modelname;
        }
        $params = array(
            'conditions' => array(
                "{$modelname}.{$_model->primaryKey}" => $id,
            ),
        );
        if ($_model->find('count', $params) > 0) {
            return true;
        // @exception
        } else {
            return 'その選択肢は存在しません';
        }
    }
}
