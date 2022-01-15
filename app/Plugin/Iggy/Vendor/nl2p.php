<?php
// 改行をpタグ区切りにする
// @param string $text
// @return string
function nl2p($text)
{
    $nl = "\n\n";
    $text = "<p>{$text}</p>";
    return nl2br(str_replace($nl, '</p><p>', $text));
}
