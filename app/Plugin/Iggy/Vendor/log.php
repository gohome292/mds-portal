<?php
// ユーザ用ログ保存
// @param mixed $var ログ内容
// @param string $name ログ名
// @return void
function log_for_guest($var, $name)
{
    _log($var, array('name' => "guest_{$name}", 'extension' => 'txt'));
}

// トレースログ保存
// @define boolean DEBUG デバグモード
// @param mixed $var ログ内容
// @param array or string $options
// string "name" ログ識別子
// boolean "delete" 削除指定
// @return void
function l($var = '', $options = array())
{
    if (!DEBUG) return;
    if (is_string($options)) $options = array('name' => $options);
    $trace = debug_backtrace();
    $options['debug'] = array_shift($trace);
    _log($var, $options);
}

// エラーログ保存
// @param mixed $var ログ内容
// @param string $name ログ名
// @return void
function err($var, $name = '')
{
    if (!empty($name)) $name .= '_';
    $trace = debug_backtrace();
    _log(
        $var,
        array(
            'name'   => "err_{$name}" . date('Ymd'),
            'delete' => false,
            'debug'  => array_shift($trace),
        )
    );
}

// ログ保存
// @define string LOGS ログディレクトリ
// @param mixed $var ログ内容
// @param array $options
// string "name" ログ名
// boolean "delete" 削除指定
// array "debug" デバグ情報
// array "extension" ログファイル拡張子
// @return void
function _log($var = '', $options = array())
{
    static $started;
    if (!is_array($started)) $started = array();
    extract($options, EXTR_SKIP);
    if (!isset($name))
        $name = 'trace';
    if (!isset($delete))
        $delete = true;
    if (!isset($debug))
        $debug = null;
    if (!isset($extension))
        $extension = 'log';
    $logname = LOGS . "{$name}.{$extension}";
    // ログ名毎に最初だけ処理される
    if (!isset($started[$name])) {
        if ($delete && is_writable($logname)) unlink($logname);
        $started[$name] = true;
    }
    if (!is_string($var)) $var = var_export($var, true);
    if (!empty($debug)) {
        $text = sprintf(
            '%s%s[%s](%s)[%s]%s',
            str_repeat('-', 60) . "\n",
            date('H:i:s'),
            basename($debug['file']),
            $debug['line'],
            round(memory_get_usage(true) / 1024 / 1024, 2) . 'MB',
            "\n{$var}"
        ) . "\n";
    } else {
        $var = str_replace('array', '', $var);
        $text = "{$var}\n";
    }
    $text = str_replace("\n", "\r\n", $text);
    file_put_contents($logname, mbo($text), FILE_APPEND);
}

// デバグモードのとき、見やすい形式でデータを画面出力する
// @define boolean DEBUG デバグモード
// @param mixed $var
// @return void
function d($var = '')
{
    if (!DEBUG) return;
    $trace = debug_backtrace();
    $debug = array_shift($trace);
    $text = sprintf(
        '<pre>%s%s[%s](%s)[%s]%s</pre>',
        str_repeat('-', 60) . "\n",
        date('H:i:s'),
        basename($debug['file']),
        $debug['line'],
        round(memory_get_usage(true) / 1024 / 1024, 2) . 'MB',
        "\n" . htmlspecialchars(var_export($var, true), ENT_QUOTES)
    );
    echo $text;
}
