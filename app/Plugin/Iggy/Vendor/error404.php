<?php
// 不正アクセス時にページが存在しない旨のページを表示する
// @param string $text
// @return void
function error404($text = '')
{
    header("HTTP/1.1 404 Not Found");
    err("[ClientIP:{$_SERVER['REMOTE_ADDR']}]{$text}", 'http404');
    die;
}
