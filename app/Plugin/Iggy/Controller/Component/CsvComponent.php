<?php
App::import('Vendor', 'Iggy.fgetcsv');
App::import('Vendor', 'Iggy.convert');

class CsvComponent extends Component
{
    function initialize(Controller $controller)
    {
        $this->controller = $controller;
    }
    
    // @param string $filename 読み込み先ファイルパス
    // @param string $modelname
    // @param array $fieldnames カラム名
    // @param array $options
    // integer "skip" 先頭何行を無視するか指定
    // @return array
    function import($filename, $modelname, $fieldnames, $options = array())
    {
        $_this = $this->controller;
        if (!isset($_this->$modelname)) $_this->loadModel($modelname);
        $_model =& $_this->$modelname;
        
        extract($options, EXTR_SKIP);
        _default($skip, 0);
        $logname = "csvimp[{$_model->table}]u" . $_this->Auth->user('id');
        
        $count_success = $count_failure = $line = 0;
        $handle = fopen($filename, 'r');
        while ($record = fgetcsv_reg($handle)) {
            $line++;
            if ($skip >= $line) continue;
            $record = mbi($record);
            $record = array_trim($record);
            // 挿入する行(レコード)の列(カラム)数が少ない
            if (count($record) < count($fieldnames)) {
                $diff = count($fieldnames) - count($record);
                $record = array_merge(
                    $record,
                    array_fill(0, $diff, null)
                );
            }
            // 項目数が違う @exception
            if (count($record) != count($fieldnames)) {
                $log = sprintf(
                    '%s行目:%s',
                    $line,
                    '項目数が違います。入力項目数：' . count($record)
                );
                log_for_guest($log, $logname);
                $count_failure++;
                continue;
            }
            $record = array_combine($fieldnames, $record);
            $record = array_nullval($record);
            $data = array($modelname => $record);
            $_model->create(false);
            if ($_model->save($data)) {
                $count_success++;
            // @exception
            } else {
                $log = sprintf(
                    '%s行目:%s',
                    $line,
                    var_export($_this->validateErrors($_model), true)
                );
                log_for_guest($log, $logname);
                $count_failure++;
                continue;
            }
        }
        close($handle);
        if (in_array('Tree', $_model->actsAs)) $_model->setLevels();
        
        return compact('count_success', 'count_failure');
    }
    
    // @param string $filename 出力先ファイルパス
    // @param array $records
    // @param array $fieldnames カラム名
    // @param array $options
    // array "mode"
    // array "titles"
    // @return void
    function export($filename, $records, $fieldnames, $options = array())
    {
        $_this =& $this->controller;
        extract($options, EXTR_SKIP);
        _default($mode, 'w');
        $handle = fopen($filename, $mode);
        if (!empty($titles)) fputcsv($handle, $titles);
        foreach ($records as $record) {
            $line = array();
            foreach ($fieldnames as $fieldname) {
                list($model, $field) = explode('.', $fieldname);
                $line[] = $record[$model][$field];
            }
            fputcsv($handle, $line);
        }
        close($handle);
    }
}
