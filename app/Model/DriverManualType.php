<?php
class DriverManualType extends AppModel
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
    );
    var $hasMany = array(

    );
    
    function loadValidate()
    {
        $valid = array(
            'customer_organization_id' => 'required | inListDB',
            'driver_manual_id' => 'required',
            'driver_manual_type' => 'required | maxLen[250]',
        );
        $this->setValidate($valid);
    }
}
