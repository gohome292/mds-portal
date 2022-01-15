<?php
class Menu extends AppModel
{
    var $actsAs = array(
        'Tree',
        'Iggy.TreeAssist',
    );
    var $belongsTo = array(
        'Parent' => array(
            'className'  => 'Menu',
            'foreignKey' => 'parent_id',
        ),
    );
}
