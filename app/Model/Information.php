<?php
class Information extends AppModel
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
                'model' => 'Information',
            ),
            'dependent'  => true,
        ),
    );
    
    function loadValidate()
    {
        $valid = array(
            'customer_organization_id' => 'required | inListDB',
            'level'   => 'required | numeric | range[0,10]',
            'title'   => 'required | maxLen[40]',
            'content' => 'required',
        );
        $this->setValidate($valid);
    }
}
