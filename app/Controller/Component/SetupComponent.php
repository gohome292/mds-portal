<?php
App::import('Vendor', 'Iggy.fgetcsv');

class SetupComponent extends Component
{
    function initialize(Controller $controller)
    {
        $this->controller = $controller;
    }
    
    // 権限設定
    // @return void
    function setAcl()
    {
        $_this = $this->controller;
        $acl_data = fgetyml('acl');
        foreach ($acl_data as $modelname => $aros) {
            if (!isset($_this->$modelname)) $_this->loadModel($modelname);
            foreach ($aros as $aro_id => $acos) {
                $_this->$modelname->id = $aro_id;
                foreach ($acos as $method => $aco_names) {
                    foreach ($aco_names as $aco_name) {
                        if ($aco_name == '*') {
                            $_this->Acl->$method(
                                $_this->$modelname,
                                'controllers'
                            );
                        } else {
                            $_this->Acl->$method(
                                $_this->$modelname,
                                "controllers/{$aco_name}"
                            );
                        }
                    }
                }
            }
        }
    }
    
    // 対象テーブルデータを空にする
    // @param string $tablename
    // @return void
    function makeEmpty($tablename)
    {
        $_this = $this->controller;
        switch (Configure::read('App.driver')) {
        case 'mysql':
            $_this->model->query("TRUNCATE TABLE {$tablename};");
            break;
        case 'postgres':
            $_this->model->query("TRUNCATE TABLE {$tablename};");
            $_this->model->query(
                "ALTER SEQUENCE {$tablename}_id_seq restart with 1;"
            );
            break;
        default:
            break;
        }
    }
    
    // テーブル名リスト作成
    // @return array
    function getTableNames()
    {
        $_this = $this->controller;
        App::import('Core', 'Set');
        $tablenames = array();
        switch (Configure::read('App.driver')) {
        case 'mysql':
            $tables = $_this->model->query('SHOW TABLES;');
            $tablenames = Set::classicExtract(
                $tables,
                '{n}.TABLE_NAMES.Tables_in_' . Configure::read('App.database')
            );
            break;
        case 'postgres':
            $tables = $_this->model->query(
                'SELECT tablename FROM pg_tables '
                . "WHERE NOT tablename LIKE 'pg%' "
                . "AND schemaname='public' ORDER BY tablename;"
            );
            $tablenames = Set::classicExtract($tables, '{n}.0.tablename');
            break;
        default:
            break;
        }
        return $tablenames;
    }
    
    // 存在する全てのコントローラとそのアクションをACOとして保存
    // @return void
    function buildAcl()
    {
        $_this = $this->controller;
        $log = array();
        $aco =& $_this->Acl->Aco;
        $root = $aco->node('controllers');
        if (!$root) {
            $aco->create(array(
                'parent_id' => null,
                'model'     => null,
                'alias'     => 'controllers',
            ));
            $root = $aco->save();
            $root['Aco']['id'] = $aco->id; 
            $log[] = 'Created Aco node for controllers';
        } else {
            $root = $root[0];
        }
        App::import('Core', 'File');
        $Controllers = Configure::listObjects('controller');
        $appIndex = array_search('App', $Controllers);
        if ($appIndex !== false ) {
            unset($Controllers[$appIndex]);
        }
        $baseMethods = get_class_methods('Controller');
        $baseMethods[] = __FUNCTION__;
        // app/controllers 中の、どのコントローラか調べる
        foreach ($Controllers as $ctrlName) {
            App::import('Controller', $ctrlName);
            $ctrlclass = "{$ctrlName}Controller";
            $methods = get_class_methods($ctrlclass);
            // コントローラノードを探し、作成する
            $controllerNode = $aco->node("controllers/{$ctrlName}");
            if (!$controllerNode) {
                $aco->create(array(
                    'parent_id' => $root['Aco']['id'],
                    'model'     => null,
                    'alias'     => $ctrlName,
                ));
                $controllerNode = $aco->save();
                $controllerNode['Aco']['id'] = $aco->id;
                $log[] = "Created Aco node for {$ctrlName}";
            } else {
                $controllerNode = $controllerNode[0];
            }
            // 親コントローラとプライベートのアクションを削除する
            foreach ($methods as $k => $method) {
                if (strpos($method, '_', 0) === 0) {
                    unset($methods[$k]);
                    continue;
                }
                if (in_array($method, $baseMethods)) {
                    unset($methods[$k]);
                    continue;
                }
                $methodNode = $aco->node(
                    "controllers/{$ctrlName}/{$method}"
                );
                if (!$methodNode) {
                    $aco->create(array(
                        'parent_id' => $controllerNode['Aco']['id'],
                        'model'     => null,
                        'alias'     => $method,
                    ));
                    $methodNode = $aco->save();
                    $log[] = "Created Aco node for {$method}";
                }
            }
        }
    }
    
    // @param string $modelname
    // @return array
    function setData($modelname)
    {
        $_this =& $this->controller;
        App::import('Vendor', 'Iggy.convert');
        if (!isset($_this->$modelname)) $_this->loadModel($modelname);
        $filename = TMP . 'setup' . DS . $_this->$modelname->table . '.csv';
        $handle = fopen($filename, 'r');
        $count_success = $count_failure = 0;
        $logname = 'setup_' . $_this->$modelname->table;
        $fieldnames = fgetcsv_reg($handle, 4096);
        // IDは自動採番なので除外
        array_shift($fieldnames);
        $line_number = 0;
        while ($record = fgetcsv_reg($handle, 4096)) {
            $line_number++;
            // IDは自動採番なので値を除外
            array_shift($record);
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
                    '[%s]Line(%s)%s',
                    $modelname,
                    $line_number,
                    '項目数が違います。入力項目数：' . count($record)
                );
                err(mbo($log), $logname);
                $count_failure++;
                continue;
            }
            $record = mbi($record);
            $record = array_trim($record);
            $record = array_nullval($record);
            $record = array_combine($fieldnames, $record);
            $data = array($modelname => $record);
            $_this->$modelname->create(false);
            $_this->$modelname->set($data);
            // ツリー構造
            if (in_array('Tree', $_this->$modelname->actsAs)) {
                $_this->$modelname->setPrevRecord();
            }
            if ($_this->$modelname->validates()) {
                if ($modelname == 'User') {
                    $_this->$modelname->password = $_this->Auth->password(
                        $data[$modelname]['password']
                    );
                }
                $_this->$modelname->save(null, false);
                $count_success++;
            // @exception
            } else {
                $log = sprintf(
                    '[%s]Line(%s)%s[%s]',
                    $modelname,
                    $line_number,
                    var_export(
                        $_this->validateErrors($_this->$modelname),
                        true
                    ),
                    var_export($data, true)
                );
                err(mbo($log), $logname);
                $count_failure++;
                continue;
            }
        }
        // ツリー構造
        if (in_array('Tree', $_this->$modelname->actsAs)) {
            $_this->$modelname->setLevels();
            $fieldnames = array_keys($_this->$modelname->_schema);
            if (in_array('sort', $fieldnames)) {
                $_prev_record =& $_this->$modelname->prev_record;
                $_data =& $_this->$modelname->data;
                $_alias =& $_this->$modelname->alias;
                if (empty($_prev_record)
                || $_prev_record[$_alias]['sort'] != $_data[$_alias]['sort']) {
                    $_this->$modelname->create(false);
                    $_this->$modelname->reorder(array('field' => 'sort'));
                }
            }
        }
        $result = array(
            'count_success' => $count_success,
            'count_failure' => $count_failure,
        );
        $horizontal = str_repeat('-', 5);
        return $result;
    }
}
