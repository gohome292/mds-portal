<?php
App::import('Vendor', 'Iggy.spyc/spyc');

// @param string $filename
// @return array
function fgetyml($filename)
{
    $filename = TMP . 'yamls' . DS . "{$filename}.yml";
    return Spyc::YAMLLoad($filename);
}

// @param string $filename
// @param array $array
// @return integer or boolean
function fputyml($filename, $array)
{
    $filename = TMP . 'yamls' . DS . "{$filename}.yml";
    return file_put_contents($filename, Spyc::YAMLDump($array));
}
