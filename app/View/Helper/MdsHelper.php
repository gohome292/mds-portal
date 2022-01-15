<?php
class MdsHelper extends AppHelper
{
    // @param string $text
    // @return string
    function format_contact_address($text)
    {
        $text = str_replace("\r\n", "\n", $text);
        $lines = explode("\n", $text);
        $result = "<h3>" . h(trim($lines[0])) . "</h3>\n"
                . "<div class=\"side-box\">\n";
        unset($lines[0]);
        foreach ($lines as $line) {
            $result .= "<p>" . h(trim($line)) . "</p>\n";
        }
        $result .= "</div>\n";
        return $result;
    }
}
