<?php
class MailHistory extends AppModel
{
    var $actsAs = array(
        'Iggy.BasicValidation',
        'Iggy.AppValidation',
        'Containable',
        'Iggy.Transaction',
    );
    var $belongsTo = array(
        'CustomerOrganization',
        'cUser' => array(
            'className'  => 'User',
            'foreignKey' => 'user_id',
        ),
        'mUser' => array(
            'className'  => 'User',
            'foreignKey' => 'modified_user_id',
        ),
    );
    var $hasMany = array(
        'MailHistoryDetail',
    );
}
