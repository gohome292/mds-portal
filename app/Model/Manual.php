<?php
class Manual extends AppModel
{
    var $actsAs = array(
        'Iggy.BasicValidation',
        'Iggy.AppValidation',
        'Containable',
    );
    var $belongsTo = array(
	    'CustomerOrganization' => array(
            'className'  => 'CustomerOrganization',
            'foreignKey' => 'customer_organization_id',
        ),
	    'DriverManualType' => array(
            'className'  => 'DriverManualType',
            'foreignKey' => 'driver_manual_type_id',
        ),
    );
    var $hasMany = array(
        'Attachment' => array(
            'className'  => 'Attachment',
            'foreignKey' => 'foreign_key',
            'conditions' => array(
                'model' => 'Manual',
            ),
            'dependent'  => true,
        ),
    );
    
    function loadValidate()
    {
        $valid = array(
            'customer_organization_id' => 'required | inListDB',
            'driver_manual_type_id' => 'required | inListDB',
            'type' => 'required | maxLen[250]',
            'category' => 'required | maxLen[250]',
        );
        $this->setValidate($valid);
    }
}
