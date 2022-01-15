<?php
class NewHelper extends AppHelper
{
    // @param string $date
    // @param integer $newdays
    // @return boolean
    function is($date, $newdays)
    {
        $compare_time = strtotime("-{$newdays} day");
        $target_time = strtotime($date);
        if ($compare_time < $target_time) {
            return true;
        }
        return false;
    }
    
    // @param string $date
    // @param integer $newdays
    // @return string
    function display($date, $newdays = 7)
    {
        if ($this->is($date, $newdays)) {
            return '<span class="new">New</span>';
        }
        return '';
    }
}
