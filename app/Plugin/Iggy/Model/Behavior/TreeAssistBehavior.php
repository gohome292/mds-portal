<?php
class TreeAssistBehavior extends ModelBehavior
{
    // @param object $model
    // @param array $options
    // array "fields"
    // array "conditions"
    // boolean "path"
    // @return array
    function getTree($model, $options = array())
    {
        extract($options, EXTR_SKIP);
        if (!isset($fields))
            $fields = array();
        if (!isset($conditions))
            $conditions = array();
        if (!isset($path))
            $path = false;
        $fields = array_merge($fields, array(
            "{$model->alias}.id",
            "{$model->alias}.name",
            "{$model->alias}.level",
            "{$model->alias}.lft",
            "{$model->alias}.rght",
        ));
        $options = array(
            'fields'     => $fields,
            'conditions' => $conditions,
            'order'      => array("{$model->alias}.lft ASC"),
            'recursive'  => 0,
        );
        if ($path) {
            $records = $model->find('all', $options);
            foreach ($records as &$record) {
                $record[$model->alias]['path'] = $model->getpathstr(
                    $record[$model->alias][$model->primaryKey]
                );
            }
            unset($record);
            return $records;
        } else {
            return $model->find('all', $options);
        }
    }
    
    // 自分を含めた子孫を取得
    // @param object $model
    // @return array
    function ChildrenAndSelf(
        $model,
        $id,
        $direct    = false,
        $fields    = null,
        $orders    = null,
        $limit     = null,
        $page      = 1,
        $recursive = null
    )
    {
        $self = $model->findById($id);
        $records = $model->children($id, $direct);
        array_unshift($records, $self);
        return $records;
    }
    
    // パスをパンくずリスト形式の文字列で取得
    // @param object $model
    // @param integer $id
    // @param boolean $self
    // @return string
    function getpathstr($model, $id, $self = true)
    {
        $arr = $this->getpatharr($model, $id, $self);
        if (!is_array($arr))
            return '';
        return implode(' > ', $arr);
    }
    
    // パスを配列で取得
    // @param object $model
    // @param integer $id
    // @param boolean $self
    // @return array
    function getpatharr($model, $id, $self = true)
    {
        $records = $model->getPath($id);
        if (empty($records)) return array();
        if (!$self) array_pop($records);
        //App::import('Core', 'Set');
        return Set::classicExtract($records, "{n}.{$model->alias}.name");
    }
    
    // 対象レコードの階層構造における階層位置を計算して保存
    // @param object $model
    // @return void
    function setLevel($model)
    {
        $model->saveField('level', count($model->getPath()));
    }
    
    // 全レコードの階層構造における階層位置を計算して保存
    // @param object $model
    // @return void
    function setLevels($model)
    {
        $params = array(
            'fields' => array(
                "{$model->alias}.{$model->primaryKey}",
            ),
        );
        $records = $model->find('all', $params);
        foreach ($records as $record) {
            $model->create(false);
            $model->id = $record[$model->alias][$model->primaryKey];
            $model->setLevel();
        }
    }
    
    // ツリー構造を復元
    // @param object $model
    // @return void
    function restore($model, $field = null)
    {
        $model->recover();
        $model->setLevels();
        $model->create(false);
        if (empty($field)) {
            $model->reorder();
        } else {
            $model->reorder(array('field' => $field));
        }
    }
}
