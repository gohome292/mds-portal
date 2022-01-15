<?php
// 入力用文字コード変換
// @param string or array $var
// @return string or array
function mbi_shell($var)
{
    if (substr(PHP_OS, 0, 3) == 'WIN') {
        return mbi($var);
    } else {
        return $var;
    }
}

// 出力用文字コード変換
// @param string or array $var
// @return string or array
function mbo_shell($var)
{
    if (substr(PHP_OS, 0, 3) == 'WIN') {
        return mbo($var);
    } else {
        return $var;
    }
}
