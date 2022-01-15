<?php
class CustomerOrganization extends AppModel
{
    var $actsAs = array(
        'Iggy.BasicValidation',
        'Iggy.AppValidation',
        'Tree',
        'Iggy.TreeAssist',
        'Iggy.Transaction',
        'Containable',
    );
    var $belongsTo = array(
        'Parent' => array(
            'className'  => 'CustomerOrganization',
            'foreignKey' => 'parent_id',
        ),
        'TopParent' => array(
            'className'  => 'CustomerOrganization',
            'foreignKey' => 'top_parent_id',
        ),
    );
    var $hasMany = array(
        'User',
    );
    
    function loadValidate()
    {
        $valid = array(
            'parent_id' => 'inListDB[CustomerOrganization]',
            'name'      => 'required | maxLen[40]',
            'sort'      => 'required | numeric | range[0,100000000]',
        );
        $this->setValidate($valid);
    }
}
