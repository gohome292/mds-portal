<?php
class DisableBehavior extends ModelBehavior
{
    var $_settings = array(
        'field' => 'disabled',
        'find' => true,
        'enabled' => true,
    );
    
    function setup(&$model, $config = array())
    {
        $this->_settings = array_merge($this->_settings, $config);
    }
    
    // @param object $model
    // @return boolean
    function beforeDelete(&$model, $cascade = true)
    {
        if (!$this->_settings['enabled']
        || !$model->hasField($this->_settings['field'])) {
            return true;
        }
        $this->disable($model);
        return false;
    }
    
    // @param object $model
    // @return boolean
    function disable(&$model)
    {
        return $model->saveField($this->_settings['field'], true);
    }
    
    // @param object $model
    // @return boolean
    function enable(&$model)
    {
        return $model->saveField($this->_settings['field'], false);
    }
    
    // @param object $model
    // @param array $query
    // @return array
    function beforeFind(&$model, $query)
    {
        if (!$this->_settings['find'] || !$this->_settings['enabled']) {
            return $query;
        }
        $field = "{$model->alias}.{$this->_settings['field']}";
        if (empty($query['conditions'])) {
            $query['conditions'] = array($field => false);
        } elseif (is_array($query['conditions'])) {
            $query['conditions'][$field] = false;
        }
        return $query;
    }
}
