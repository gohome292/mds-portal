<?php
class DateFormHelper extends AppHelper
{
    var $helpers = array(
        'Form',
    );
    
    // @param string $name
    // @param string $value
    function input($name, $value = '')
    {
        _default($value, date('Y-m-d H:i:s'));
        $is_match = preg_match(
            '/^(\d{4}\-\d{2}\-\d{2}) (\d{2}):(\d{2}):\d{2}$/',
            $value,
            $matches
        );
        $date   = str_replace('-', '/', $matches[1]);
        $hour   = $matches[2];
        $minute = $matches[3];
        echo $this->Form->text("{$name}.date", array(
            'value'    => $date,
            'readonly' => true,
            'div'      => false,
        ));
        echo $this->Form->hour(
            $name,
            true,
            $hour,
            array('div' => false)
        );
        echo $this->Form->minute(
            $name,
            $minute,
            array('div' => false)
        );
    }
}
