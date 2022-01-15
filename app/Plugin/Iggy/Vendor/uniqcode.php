<?php
// 一意な文字列を返す
// @return string
function uniqcode()
{
    return md5(uniqid(mt_rand(), true));
}
