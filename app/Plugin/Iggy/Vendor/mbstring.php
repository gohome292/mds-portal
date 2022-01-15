<?php
// 入力用文字コード変換
// @param string or array $var
// @param string $from_encoding
// @return string or array
function mbi($var)
{
    if (is_array($var)) return array_map(__FUNCTION__, $var);
    return mb_convert_encoding(
        $var,
        mb_internal_encoding(),
        'SJIS'
    );
}

// 出力用文字コード変換
// @param string or array $var
// @param string $to_encoding
// @return string or array
function mbo($var)
{
    if (is_array($var)) return array_map(__FUNCTION__, $var);
    return mb_convert_encoding(
        $var
        ,'SJIS'
        ,mb_internal_encoding()
    );
}
