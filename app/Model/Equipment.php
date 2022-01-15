<?php
class Equipment extends AppModel
{
    var $actsAs = array(
        'Iggy.BasicValidation',
        'Iggy.AppValidation',
        'Containable',
    );
    var $belongsTo = array(
        'CustomerOrganization',
    );
    var $hasMany = array(
        'Attachment' => array(
            'className'  => 'Attachment',
            'foreignKey' => 'foreign_key',
            'conditions' => array(
                'model' => 'Equipment',
            ),
            'dependent'  => true,
        ),
    );
    
    function loadValidate()
    {
        $valid = array(
            'customer_organization_id' => 'required | inListDB',
        );
        $this->setValidate($valid);
    }
}
