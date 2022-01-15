<?php
class ActionSaveComponent extends Component
{
    function initialize(Controller $controller)
    {
        $this->controller = $controller;
    }
    function startup(Controller $controller)
    {
        $this->controller = $controller;
    }
    
    // @return boolean
    function run()
    {
        $_this = $this->controller;
        $_data = $_this->request->data;
        if (!empty($_this->model->belongsToTreeModel)) {
            foreach ($_this->model->belongsToTreeModel as $modelname) {
                unset($_data[$modelname]);
            }
        }
        // 作成者カラムあり and IDなし(INSERT)
        if ($_this->model->hasField('created_user_id')
        && empty($_data[$_this->model->alias][$_this->model->primaryKey])) {
            $_data[$_this->model->alias]['created_user_id'] =
                $_this->Auth->user('id');
        }
        // 更新者カラムあり
        if ($_this->model->hasField('modified_user_id')) {
            $_data[$_this->model->alias]['modified_user_id'] =
                $_this->Auth->user('id');
        }
        $_this->model->create(false);
        $_this->model->set($_data);
        // ツリー構造
        if (in_array('Tree', $_this->model->actsAs)) {
            $_this->model->setPrevRecord();
        }
        // トランザクション
        if (in_array('Iggy.Transaction', $_this->model->actsAs)) {
            $_this->model->begin();
        }
        $modelnames = array_keys($_data);
        // 保存対象モデルが複数
        if (count($modelnames) > 1) {
            $failure = false;
            $errors = $_this->model->invalidFields();
            // @exception
            if (!empty($errors)) {
                l($errors);
                $failure = true;
            }
            // 保存対象モデル数周回
            foreach ($modelnames as $modelname) {
                // 保存対象モデルがメインモデル
                if ($modelname == $_this->model->alias) continue;
                // 保存対象モデルのインスタンスがない
                if (!isset($_this->$modelname)) $_this->loadModel($modelname);
                // 保存対象データが複数レコード
                if (is_array( $_data[$modelname])) {
                if (isset($_data[$modelname][0])) {
                    // 保存対象データレコード数周回
                    foreach ($_data[$modelname] as $number => $data) {
                        $data = array($modelname => $data);
                        $_this->$modelname->create(false);
                        $_this->$modelname->set($data);
                        $_this->$modelname->loadValidate();
                        $errors = $_this->$modelname->invalidFields();
                        // Attachmentアップロード
                        if (isset($data[$modelname]['file']['error'])) {
                            // @exception
                            if ($data[$modelname]['file']['error'] == 1
                            || $data[$modelname]['file']['error'] == 2) {
                                $errors = array(
                                    'size' => 'ファイルサイズが大き過ぎます。',
                                );
                            }
                        }
                        // @exception
                        if (!empty($errors)) {
                            l($errors);
                            $failure = true;
                            $_this->set(
                                'errors_' . $_this->$modelname->table
                                . "_{$number}",
                                $errors
                            );
                        }
                    }
                // 保存対象データが単一レコード
                } else {
                    $data = array($modelname => $_data[$modelname]);
                    // ツリー構造
                    if (in_array('Tree', $_this->model->actsAs)) {
                        unset($data[$modelname]['path']);
                    }
                    $_this->$modelname->create(false);
                    $_this->$modelname->set($data);
                    $_this->$modelname->loadValidate();
                    $errors = $_this->$modelname->invalidFields();
                    // @exception
                    if (!empty($errors)) {
                        l($errors);
                        $failure = true;
                        $_this->set(
                            'errors_' . $_this->$modelname->table,
                            $errors
                        );
                    }
                }
                }
            }
            // @exception
            if ($failure) {
                // トランザクション
                if (in_array('Iggy.Transaction', $_this->model->actsAs)) {
                    $_this->model->rollback();
                }
                return false;
            }
            $_this->model->saveAll(
                $_data,
                array(
                    'validate' => false,
                    'atomic'   => false,
                )
            );
        // 保存対象モデルが単一
        } else {
            // @exception
            if (!$_this->model->save(null)) {
                // トランザクション
                if (in_array('Iggy.Transaction', $_this->model->actsAs)) {
                    $_this->model->rollback();
                }
                return false;
            }
        }
        $id = $_this->model->id;
        $prev_record = $_this->model->prev_record;
        $alias = $_this->model->alias;
        // ツリー構造の所属モデルあり
        if (!empty($_this->model->belongsToTreeModel)) {
            App::import('Core', 'Inflector');
            // ツリー構造の所属モデル数周回
            foreach ($_this->model->belongsToTreeModel as $modelname) {
                $u_modelname = Inflector::underscore($modelname);
                // ツリー構造の所属モデルの頂点親カラムあり
                if ($_this->model->hasField("top_{$u_modelname}_id")) {
                    $_this->model->recursive = -1;
                    $_treemodel_id = $_this->model->field("{$u_modelname}_id");
                    // 所属モデル指定なし
                    if (empty($_treemodel_id)) {
                        $_this->model->create(false);
                        $_this->model->id = $id;
                        $_this->model->saveField(
                            "top_{$u_modelname}_id",
                            NULL
                        );
                    // 所属モデル指定あり
                    } else {
                        // 保存前と今のツリー構造の所属モデルIDが異なる
                        if (empty($prev_record)
                        ||
                        $prev_record[$alias]["{$u_modelname}_id"]
                        != $_treemodel_id) {
                            $_this->model->$modelname->recursive = -1;
                            $_this->model->$modelname->create(false);
                            $_this->model->$modelname->id = $_treemodel_id;
                            $top_parent_id = $_this->model->$modelname->field(
                                'top_parent_id'
                            );
                            $_this->model->create(false);
                            $_this->model->id = $id;
                            $_this->model->saveField(
                                "top_{$u_modelname}_id",
                                $top_parent_id
                            );
                        }
                    }
                }
            }
        }
        // ツリー構造
        if (in_array('Tree', $_this->model->actsAs)) {
            $_this->model->setLevels();
            //$fieldnames = array_keys($_this->model->_schema);
            //if (in_array('sort', $fieldnames)) {
            // 順番カラムあり
            if ($_this->model->hasField('sort')) {
                $_data =& $_this->model->data;
                if (empty($prev_record)
                || $prev_record[$alias]['sort'] != $_data[$alias]['sort']) {
                    $_this->model->create(false);
                    $_this->model->reorder(array('field' => 'sort'));
                }
            }
            // 頂点親カラムあり
            if ($_this->model->hasField('top_parent_id')) {
                $_this->model->recursive = -1;
                $_this->model->create(false);
                $_this->model->id = $id;
                $_parent_id = $_this->model->field('parent_id');
                // 保存前の親と今の親が異なる
                if (empty($prev_record)
                || $prev_record[$alias]['parent_id'] != $_parent_id) {
                    // 親IDがNULL→自分が頂点親
                    if (empty($_parent_id)) {
                        $top_parent_id = $id;
                    // 親IDあり
                    } else {
                        $_this->model->create(false);
                        $_this->model->id = $_parent_id;
                        $top_parent_id = $_this->model->field('top_parent_id');
                    }
                    // 保存前の頂点親と今の頂点親が異なる
                    if (empty($prev_record)
                    ||
                    $prev_record[$alias]['top_parent_id']
                    != $top_parent_id) {
                        $_this->model->create(false);
                        $_this->model->id = $id;
                        $_this->model->saveField(
                            'top_parent_id',
                            $top_parent_id
                        );
                        $records = $_this->model->children();
                        // 子孫あり
                        if (!empty($records)) {
                            // 子孫数周回
                            foreach ($records as $record) {
                                $_this->model->create(false);
                                $_this->model->id =
                                    $record[$alias][$_this->model->primaryKey];
                                $_this->model->saveField(
                                    'top_parent_id',
                                    $top_parent_id
                                );
                            }
                        }
                        // 所有モデルあり
                        if (!empty($_this->model->hasMany)) {
                            //App::import('Core', 'Set');
                            App::import('Core', 'Inflector');
                            $ids = array($id);
                            // 子孫あり
                            if (!empty($records)) {
                                $ids = array_merge(
                                    $ids,
                                    Set::classicExtract(
                                        $records,
                                        "{n}.{$alias}"
                                        . ".{$_this->model->primaryKey}"
                                    )
                                );
                            }
                            $u_alias = Inflector::underscore($alias);
                            // 所有モデル数周回
                            foreach ($_this->model->hasMany as
                            $modelname => $var) {
                                $_this->model->$modelname->recursive = -1;
                                $_this->model->$modelname->create(false);
                                $_this->model->$modelname->updateAll(
                                    array(
                                        "{$modelname}.top_{$u_alias}_id"
                                            => $top_parent_id,
                                    ),
                                    array(
                                        "{$modelname}.{$u_alias}_id" => $ids,
                                    )
                                );
                            }
                        }
                    }
                }
            // 頂点親カラムなし
            } else {
                $_this->model->create(false);
                $_this->model->id = $id;
            }
        }
        // トランザクション
        if (in_array('Iggy.Transaction', $_this->model->actsAs)) {
            $_this->model->commit();
        }
        return true;
    }
}
