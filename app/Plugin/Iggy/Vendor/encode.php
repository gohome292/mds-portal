<?php
// 文字列を暗号化する
// @param mixed $var
// @return string
function encode($var)
{
    return base64_encode(serialize($var));
}

// 暗号化された文字列を復号化する
// @param string $var
// @return mixed
function decode($var)
{
    return unserialize(base64_decode($var));
}
