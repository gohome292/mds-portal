<?php
class Group extends AppModel
{
    var $actsAs = array(
        'Iggy.BasicValidation',
        'Iggy.AppValidation',
        'Containable',
        'Acl' => array('requester'),
    );
    var $hasMany = array(
        'User',
    );
    
    function save($data = null, $validate = true, $fieldList = array())
    {
        if (parent::save($data, $validate, $fieldList)) {
            $conditions = array(
                'model'       => $this->name,
                'foreign_key' => $this->id,
            );
            App::import('Component', 'Acl');
            $Aro = new Aro;
            $Aro->id = $Aro->field('id', $conditions);
            $Aro->saveField('alias', "{$this->name}::{$this->id}");
            return true;
        }
        return false;
    }
    
    function parentNode()
    {
        return null;
    }
}
