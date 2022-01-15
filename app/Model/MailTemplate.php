<?php
class MailTemplate extends AppModel
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
                'model' => 'MailTemplate',
            ),
            'dependent'  => true,
        ),
    );
    
    function loadValidate()
    {
        $valid = array(
            'customer_organization_id' => 'inListDB',
            'title' => 'required | maxLen[80]',
            'body'  => 'required',
        );
        $this->setValidate($valid);
    }
}
