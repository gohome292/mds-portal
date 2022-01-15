<?php
// @param string $filename
// @return array
function path($filename)
{
    if (!preg_match(
        '/^(.+?)\.([0-9a-zA-Z]{1,4})$/',
        $filename,
        $matches
    )) return array('', '');
    return array($matches[1], $matches[2]);
}
