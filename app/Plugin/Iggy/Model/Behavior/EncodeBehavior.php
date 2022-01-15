<?php
App::import('Vendor', 'Iggy.misconversion');

class EncodeBehavior extends ModelBehavior
{
    // @param object $model
    // @return boolean
    function beforeSave(&$model)
    {
        $model->data = misconversion($model->data);
        $model->data = mbo($model->data);
        return true;
    }
    
    // @param object $model
    // @param array $query
    // @return array
    function beforeFind(&$model, $query)
    {
        $query['callbacks'] = 'after';
        return mbo($query);
    }
    
    // @param object $model
    // @param array $result
    // @param boolean $primary
    // @return array
    function afterFind(&$model, $result, $primary)
    {
        return mbi($result);
    }
}
