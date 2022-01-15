<?php
// 日時情報を日本語に変換する
// @param mixed $datetime
// @param string $mode
//        'DT' => 'Y/m/d H:i',
//        'D'  => 'Y/m/d',
//        'T'  => 'H:i',
//        'JDT' => 'Y年n月j日 H時i分',
//        'JD'  => 'Y年n月j日',
//        'JT'  => 'H時i分',
// @return string
function datetime_format($datetime, $mode = 'JD')
{
    if (empty($mode)) return $datetime;
    
    $formats = array(
        'DT' => 'Y/m/d H:i',
        'D'  => 'Y/m/d',
        'T'  => 'H:i',
        'JDT' => 'Y年n月j日 H時i分',
        'JD'  => 'Y年n月j日',
        'JT'  => 'H時i分',
    );
    
    if (!is_numeric($datetime)) $datetime = strtotime($datetime);
    
    return date($formats[$mode], $datetime);
}
