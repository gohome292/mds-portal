<?php
class RichTextBehavior extends ModelBehavior
{
    // @param object $model
    // @param string $fieldname
    // @param string $target
    function setTargetOfLink(&$model, $fieldname, $target = '_blank')
    {
        $var =& $model->data[$model->alias][$fieldname];
        $var = preg_replace(
            '/<a href="(.+?)">/i',
            '<a href="$1" target="' . $target . '">',
            $var
        );
    }
}
