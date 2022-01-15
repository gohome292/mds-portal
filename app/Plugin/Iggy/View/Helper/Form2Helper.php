<?php
class Form2Helper extends AppHelper
{
    var $helpers = array(
        'Form',
    );
    
    function beforeRender()
    {
        App::import('Core', 'Inflector');
    }
    
    // @param string $name
    // @param array or boolean $options
    function input($name, $options = array())
    {
        preg_match('/^(.+?)_id$/', $name, $matches);
        $tablename = Inflector::tableize($matches[1]);
        $view =& ClassRegistry::getObject('view');
        $_options = array(
            'type'    => 'select',
            'options' => $view->getVar($tablename),
        );
        if (is_array($options)) {
            $_options = array_merge($_options, $options);
        } elseif($options === true) {
            $_options['empty'] = '-';
        }
        echo $this->Form->input($name, $_options);
    }
}
