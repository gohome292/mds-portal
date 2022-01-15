<?php
class MssqlBehavior extends ModelBehavior
{
    var $conn;
    
    // @param object $model
    // @return void
    function connect(&$model)
    {
        if (!empty($this->conn)) return;
        
        $db = new DATABASE_CONFIG;
        $db = $db->{$model->useDbConfig};
        
        $this->conn = mssql_connect(
            $db['host'],
            $db['login'],
            $db['password']
        );
        mssql_select_db($db['database'], $this->conn);
    }
    
    // @param object $model
    // @param string $name ストアドプロシージャ名
    // @param array $records 2次元配列
    //  - array $record
    //   - string "param_name"
    //   - mixed "var" 変数参照渡し
    //   - const "type"
    //   - boolean "is_output"
    // @return boolean
    function doStoredProcedure(&$model, $name, $records)
    {
        $this->connect($model);
        
        $stmt = mssql_init(mbo($name));
        
        foreach ($records as $record) {
            if (is_null($record['var'])) {
                mssql_bind(
                    $stmt,
                    mbo($record['param_name']),
                    $record['var'],
                    $record['type'],
                    $record['is_output'],
                    true // is_null
                );
            } else {
                if (!$record['is_output']) {
                    $record['var'] = mbo($record['var']);
                }
                mssql_bind(
                    $stmt,
                    mbo($record['param_name']),
                    $record['var'],
                    $record['type'],
                    $record['is_output']
                );
            }
        }
        
        return mssql_execute($stmt);
    }
}
