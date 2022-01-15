<?php
class TabHelper extends AppHelper
{
    var $helpers = array(
        'Html',
        'Js',
    );
    
    // @param array $tabs
    function run($tabs)
    {
        echo "{$this->Html->script('/Iggy/js/tab')}\n"
           . "<div id=\"tabs\" class=\"tabs horizon\">\n"
           . "<ul id=\"tab_menu\">\n";
        foreach ($tabs as $tabkey => $tabname) {
            switch ($tabkey) {
            case 'search':
                echo "{$this->Html->script('/Iggy/js/search_tab')}\n";
                break;
            }
            echo "<li><a href=\"#{$tabkey}_area\" id=\"{$tabkey}_tab\">"
               . "{$tabname}</a></li>\n";
        }
        echo "</ul>\n";
    }
}
