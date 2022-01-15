<?php
class DateFormatComponent extends Component
{
    function convert(&$data, $fieldname)
    {
        $var = $data[$fieldname]['date'] . ' '
             . $data[$fieldname]['hour'] . ':'
             . sprintf('%02d', $data[$fieldname]['min']) . ':00';
        $data[$fieldname] = $var;
    }
}
