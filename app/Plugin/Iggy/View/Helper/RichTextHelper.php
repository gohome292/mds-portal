<?php
class RichTextHelper extends AppHelper
{
    // @param string $var
    // @param string $target
    // @return return
    function setTarget($var, $target = '_blank')
    {
        return preg_replace(
            '/<a href="(.+?)">/i',
            '<a href="$1" target="' . $target . '">',
            $var
        );
    }
    
    // @param string $var
    // @param array $options
    // @return return
    function createLink($var, $options = array('target' => '_blank'))
    {
        $text = '';
        foreach ($options as $attr_name => $attr_val) {
            $text .= " {$attr_name}=\"{$attr_val}\"";
        }
        return preg_replace(
            '/(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)/i',
            '<a href="$1$2"' . $text . '>$1$2</a>',
            $var
        );
    }
}
