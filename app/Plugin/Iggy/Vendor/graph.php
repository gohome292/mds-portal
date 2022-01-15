<?php
App::import('Vendor', 'Iggy.pchart/pdata');
App::import('Vendor', 'Iggy.pchart/pchart');
define('FONTS', TMP . 'fonts' . DS);

// @param string $type
// @param array $params
// @return void
function graph($type, $params)
{
    $method = "graph_{$type}";
    App::import('Vendor', "Iggy.{$method}");
    $method($params);
}
