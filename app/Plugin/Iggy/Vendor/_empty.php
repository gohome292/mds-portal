<?php
// @param mixed $var
// @return boolean
function _empty($var)
{
    if ($var === '' || is_null($var) || $var === array()) return true;
    return false;
}
