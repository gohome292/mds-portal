<?php
// 空文字をNULLに置換
// @param array $var
// @return array
function array_nullval($var)
{
    if (is_array($var)) return array_map(__FUNCTION__, $var);
    return nullval($var);
}

// 空文字をNULLに置換
// @param string $var
// @return string
function nullval($var)
{
    if ($var === '') return null;
    return $var;
}

// @param array $var
// @return array
function array_trim($var)
{
    if (is_array($var)) return array_map(__FUNCTION__, $var);
    return trim($var);
}
