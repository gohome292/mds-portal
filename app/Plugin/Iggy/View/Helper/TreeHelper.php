<?php
class TreeHelper extends AppHelper
{
    var $helpers = array(
        'Html',
    );
    
    // @param array $records
    // @param array $params
    // boolean "controller"
    // string "action"
    // boolean "refer"
    // @return void
    function run($records, $params = array())
    {
        if (!empty($params['action'])) App::import('Core', 'Inflector');
        $modelname = key($records[0]);
        $name = strtolower($modelname);
        $prev_level = 1;
        echo "<div id=\"tree_{$name}_area\">";
        if (!empty($params['controller'])) {
            echo "<div id=\"tree_{$name}_control\">"
               . '<a href="javascript:void(0);">全て閉じる</a>'
               . ' | <a href="javascript:void(0);">全て開く</a>'
               . "</div><!-- #tree_{$name}_control -->";
        }
        echo "<ul id=\"tree_{$name}\">";
        foreach ($records as $record) {
            if ($prev_level > $record[$modelname]['level']) {
                echo str_repeat(
                    '</ul></li>',
                    ($prev_level - $record[$modelname]['level'])
                );
            }
            echo '<li>';
            if (!empty($record[$modelname]['link'])) {
                echo $this->Html->link(
                    $record[$modelname]['name'],
                    $record[$modelname]['link'],
                    array('escape' => false)
                );
            } elseif (!empty($params['action'])) {
                echo $this->Html->link(
                    $record[$modelname]['name'],
                      '/' . Inflector::tableize($modelname)
                    . "/{$params['action']}"
                    . '/' . $record[$modelname]['id'],
                    array('escape' => false)
                );
            } elseif (!empty($params['refer'])) {
                $options = array(
                    'escape' => false,
                    'id' => $record[$modelname]['id'],
                );
                if (!empty($record[$modelname]['path'])) {
                    $options['path'] = $record[$modelname]['path'];
                }
                echo $this->Html->link(
                    $record[$modelname]['name'],
                    'javascript: void(0);',
                    $options
                );
            } else {
                echo $record[$modelname]['name'];
            }
            $_rght =& $record[$modelname]['rght'];
            $_lft =& $record[$modelname]['lft'];
            if (1 < ($_rght - $_lft)) {
                echo '<ul>';
            } elseif (($_rght - $_lft) === 1) {
                echo '</li>';
            }
            $prev_level = $record[$modelname]['level'];
        }
        echo str_repeat('</ul></li>', ($record[$modelname]['level'] - 1))
           . "</ul><!-- #tree_{$name} --></div><!-- #tree_{$name}_area -->";
        if (!empty($this->request->refer)) {
            $id = $this->_View->viewVars['id'];
            echo "\n<input type=\"hidden\" id=\"hidden\" value=\"{$id}\" />";
        }
    }
}
