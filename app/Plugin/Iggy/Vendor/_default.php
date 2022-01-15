<?php
App::import('Vendor', 'Iggy._empty');

// @param mixed(not array) $var
// @param mixed $default
// @return mixed
function _default($var, $default)
{
    if (!isset($var) || _empty($var)) return $default;
    return $var;
}
