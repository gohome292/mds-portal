<?php
// @param string $mode "alpha" or "digit" or not appoint
// @param array $options
// @return array
function ktai_attr($mode = 'mbstring', $options = array())
{
    switch ($mode) {
    // 半角英字(英数字)モード
    case 'alpha':
        $options['istyle'] = '3';
        $options['format'] = '*x';
        $options['mode']   = 'alphabet';
        break;
    // 半角数字モード
    case 'digit':
        $options['istyle'] = '4';
        $options['format'] = '*N';
        $options['mode']   = 'numeric';
        break;
    // 全角かなモード
    default:
        $options['istyle'] = '1';
        $options['format'] = '*M';
        $options['mode']   = 'hiragana';
        break;
    }
    return $options;
}
