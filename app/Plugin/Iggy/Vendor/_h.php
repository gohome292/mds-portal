<?php
// <>のみ無害化する
// @param string $text
// @return string
function _h($text)
{
    $searches = array('<', '>');
    $replaces = array('&lt;', '&gt;');
    return str_replace($searches, $replaces, $text);
}
