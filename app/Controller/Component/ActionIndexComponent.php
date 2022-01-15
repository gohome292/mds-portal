<?php
class ActionIndexComponent extends Component
{
    var $components = array(
        'Iggy.PaginationRecall',
        'Iggy.SearchRecall',
        'ActionCommon',
        'Paginator',
    );
    
    function initialize(Controller $controller)
    {
        $this->controller = $controller;
        App::import('Vendor', 'Iggy.time_push');
    }
    
    // @param array $option ex)"User.name" => "LIKE"
    // @return void
    function run($option = array())
    {
        $_this = $this->controller;
        if (empty($_this->paginate)) {
            $_this->paginate = fgetyml("paginate_{$_this->model->table}");
        } else {
            $_this->paginate = array_merge(
                $_this->paginate,
                fgetyml("paginate_{$_this->model->table}")
            );
        }
        $_this->Paginator->settings = $_this->paginate;
        if (!empty($options)) $_this->helpers[] = 'Form';
        $_this->helpers[] = 'Iggy.Menu';
        $_this->helpers[] = 'Iggy.Cycle';
        $_this->set(
            'fieldnames',
            fgetyml("fieldnames_{$_this->request->controller}")
        );
        $this->ActionCommon->setMenu();
        $this->ActionCommon->setAcl(array('view', 'add', 'edit', 'remove'));
        $_this->set('records', $this->getPage($option));
    }
    
    // 1ページ分一覧データ取得
    // @param array $option ex)"User.name" => "LIKE"
    // @return array
    function getPage($option = array())
    {
        $_this = $this->controller;
        $this->SearchRecall->run();
        $data = $_this->request->data;
        if (isset($_this->Security)) unset($data['_Token']);
        $conditions = $this->postConditions($data, $option);
        if (!empty($_this->paginate['conditions'])) {
            $conditions = array_merge(
                $_this->paginate['conditions'],
                $conditions
            );
        }
        if (!empty($_this->model->belongsToTreeModel)) {
            $records = $_this->Paginator->paginate($_this->model->alias, $conditions);
            foreach ($records as $record) {
                foreach ($_this->model->belongsToTreeModel as $modelname) {
                    $record[$modelname]['path'] =
                        $_this->model->$modelname->getpathstr(
                            $record[$modelname][
                                $_this->model->$modelname->primaryKey
                            ]
                        );
                }
            }
            unset($record);
            return $records;
        }
         return $_this->Paginator->paginate($_this->model->alias, $conditions);
    }
    
    // 一覧データ取得
    // @param array $fields
    // @param array $option ex)"User.name" => "LIKE"
    // @param array $order
    // @return array
    function getData($fields, $option = array(), $order = array())
    {
        $_this = $this->controller;
        $this->SearchRecall->run();
        $data = $_this->request->data;
        if (isset($_this->Security)) unset($data['_Token']);
        $params = array(
            'fields'     => $fields,
            'conditions' => $this->postConditions($data, $option),
            'order'      => $order,
        );
        return $_this->model->find('all', $params);
    }
    
    // 検索データ加工
    // @param array $data
    // @param array $option ex)"User.name" => "LIKE"
    // @return array
    function postConditions($data = array(), $option = array()) {
        $_this = $this->controller;
        $cond = array();
        App::import('Core', 'Sanitize');
        foreach ($option as $searchfieldname => $searchtype) {
            $modelname = $_this->model->alias;
            if (strpos($searchfieldname, '.')) {
                list($modelname, $searchfieldname) =
                    explode('.', $searchfieldname);
            }
            if (is_array($searchtype)) {
                $fieldnames = array_shift(array_values($searchtype));
                $searchtype = strtoupper(key($searchtype));
            }
            $and = array();
            switch ($searchtype) {
            // 全文検索
            case 'ALL':
                $value = $data[$modelname][$searchfieldname];
                if (empty($value)) {
                    unset(
                        $option[$searchfieldname],
                        $data[$modelname][$searchfieldname]
                    );
                    continue;
                }
                $value = str_replace('　', ' ', $value);
                $value = Sanitize::stripWhitespace($value);
                $values = explode(' ', $value);
                $and = array();
                foreach ($values as $value) {
                    $value = "%{$value}%";
                    $or = array(
                        'or' => array(),
                    );
                    foreach ($fieldnames as $fieldname) {
                        $or['or'][] = array(
                            "{$fieldname} LIKE" => $value,
                        );
                    }
                    $and[] = $or;
                }
                unset(
                    $option[$searchfieldname],
                    $data[$modelname][$searchfieldname]
                );
                break;
            // 日時範囲検索
            case 'DATETIME_BETWEEN':
                $value1 = $data[$modelname]["{$searchfieldname}1"];
                $value2 = $data[$modelname]["{$searchfieldname}2"];
                if (empty($value1) && empty($value2)) {
                    unset(
                        $option[$searchfieldname],
                        $data[$modelname]["{$searchfieldname}1"],
                        $data[$modelname]["{$searchfieldname}2"]
                    );
                    continue;
                // 開始条件なし->終了条件のみ指定
                } elseif (empty($value1)) {
                    $value2 = Sanitize::stripWhitespace($value2);
                    $and = array(
                        "{$searchfieldname} <=" => time_push($value2, true),
                    );
                    unset($data[$modelname]["{$searchfieldname}1"]);
                // 終了条件なし->開始条件のみ指定
                } elseif (empty($value2)) {
                    $value1 = Sanitize::stripWhitespace($value1);
                    $and = array(
                        "{$searchfieldname} >=" => time_push($value1),
                    );
                    unset($data[$modelname]["{$searchfieldname}2"]);
                } else {
                    $value1 = Sanitize::stripWhitespace($value1);
                    $value2 = Sanitize::stripWhitespace($value2);
                    $and = array(
                        "{$searchfieldname} BETWEEN ? AND ?" => array(
                            time_push($value1),
                            time_push($value2, true),
                        ),
                    );
                }
                // OriginalのpostConditionsで検索条件を作らない
                unset(
                    $option[$searchfieldname],
                    $data[$modelname]["{$searchfieldname}1"],
                    $data[$modelname]["{$searchfieldname}2"]
                );
                break;
            // その他
            default:
                if (isset($data[$modelname][$searchfieldname])) {
                if (_empty($data[$modelname][$searchfieldname])) {
                    unset(
                        $option[$searchfieldname],
                        $data[$modelname][$searchfieldname]
                    );
                    continue;
                }
                }
                break;
            }
            if (empty($and)) continue;
            if (isset($cond['and'])) {
                $cond['and'] = array_merge($cond['and'], $and);
            } else {
                $cond['and'] = $and;
            }
        }
        if (empty($data) || empty($option)) return $cond;
        $cond = array_merge($_this->postConditions($data, $option), $cond);
        return $cond;
    }
}
