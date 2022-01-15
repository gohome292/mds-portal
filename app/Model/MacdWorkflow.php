<?php
class MacdWorkflow extends AppModel
{
    var $actsAs = array(
        'Iggy.BasicValidation',
        'Iggy.AppValidation',
        'Containable',
    );
    var $belongsTo = array(
        'User' => array(
            'className'  => 'User',
            'foreignKey' => 'applied_user_id',
        ),
    );
    
    var $hasMany = array(
        'Attachment' => array(
            'className'  => 'Attachment',
            'foreignKey' => 'foreign_key',
            'conditions' => array(
                'model' => 'MacdWorkflow',
            ),
            'dependent'  => true,
        ),
    );

    function loadValidate()
    {
        $valid = array(
            'customer_organization_id' => 'required | inListDB',
            'applied_title' => 'required',
        );
        $this->setValidate($valid);
    }
}
