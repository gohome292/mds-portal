<?php
// @param integer $size
// @return string
function filesize_format($size)
{
    $unit = 'B';
    $precision = 0;
    if ($size > 1024) {
        $size = $size / 1024;
        $unit = 'KB';
        $precision = 0;
    }
    if ($size > 1024) {
        $size = $size / 1024;
        $unit = 'MB';
        $precision = 1;
    }
    $size = round($size, $precision);
    return number_format($size, $precision) . $unit;
}
