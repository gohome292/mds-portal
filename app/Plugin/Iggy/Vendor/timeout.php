<?php
// PHPのタイムアウトまでの時間を設定する
// @param integer $seconds
// @return void
function timeout($seconds = 300) // ApacheDefaultTimeout 300seconds
{
    ini_set(
        'max_execution_time',
        $seconds
    );
    ini_set(
        'max_input_time',
        $seconds
    );
}
