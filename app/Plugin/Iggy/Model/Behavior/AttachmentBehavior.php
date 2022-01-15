<?php
App::import('Vendor', 'Iggy.uniqcode');
App::import('Vendor', 'Iggy.path');

class AttachmentBehavior extends ModelBehavior
{
    var $exchange = false;
    
    // @define UPLOADS
    // @param object $model
    // @return boolean
    function beforeValidate(Model $model, $options = Array())
    {
        $data = $model->data[$model->alias];
        // FTP等でアップロード済み
        if (!empty($data['uploaded'])) {
            $model->data[$model->alias]['extension'] = pathinfo(
                $model->data[$model->alias]['tmp_name'],
                PATHINFO_EXTENSION
            );
            $model->data[$model->alias]['size'] = filesize(
                $model->data[$model->alias]['tmp_name']
            );
            return;
        // アップロード済みファイルの割当
        } elseif (!empty($data['attach'])) {
            $attachment = $model->findById($data['attach']);
            $model->data = array(
                $model->alias => array(
                    'model'       => $data['model'],
                    'basename'    => $attachment[$model->alias]['basename'],
                    'extension'   => $attachment[$model->alias]['extension'],
                    'size'        => $attachment[$model->alias]['size'],
                    'alternative' => $attachment[$model->alias]['alternative'],
                    'identifier'  => $data['identifier'],
                ),
            );
            return;
        }
        if (!empty($data['id'])) $model->id = $data['id'];
        if (!empty($data['remove'])) {
            $model->delete();
            return false;
        }
        if (isset($data['file'])) {
           $file = $data['file'];
           if (!isset($file['error'])) return false;
           // ファイルサイズが大き過ぎる
           if ($file['error'] == 1 || $file['error'] == 2) return false;
           // アップロード無し
           if ($file['error'] == 4 && $file['size'] == 0) return false;
           // @exception
           if (!is_uploaded_file($file['tmp_name'])) return false;
           $model->data = array(
               $model->alias => array(
                   'extension' => pathinfo($file['name'], PATHINFO_EXTENSION),
                   'size'      => $file['size'],
               ),
           );
        }
        return;
    }
    
    // @define UPLOADS
    // @param object $model
    // @return boolean
    function beforeSave(Model $model, $options = Array())
    {
        $data = $model->data[$model->alias];
        // FTP等でアップロード済み
        if (!empty($data['uploaded'])) {
            $pathinfo = path($data['tmp_name']);
            $data['size'] = filesize($data['tmp_name']);
            if (empty($data['alternative'])) {
                $data['alternative'] = pathinfo(
                    $data['tmp_name'],
                    PATHINFO_FILENAME
                );
            }
        // アップロード済みファイルの割当
        } elseif (!empty($data['attach'])) {
            $attachment = $model->findById($data['attach']);
            $model->data = array(
                $model->alias => array(
                    'model'       => $data['model'],
                    'foreign_key' => $data['foreign_key'],
                    'basename'    => $attachment[$model->alias]['basename'],
                    'extension'   => $attachment[$model->alias]['extension'],
                    'size'        => $attachment[$model->alias]['size'],
                    'alternative' => $attachment[$model->alias]['alternative'],
                    'identifier'  => $data['identifier'],
                    'created'     => $attachment[$model->alias]['created'],
                    'modified'    => $data['modified'],
                ),
            );
            $this->exchange = true;
            return true;
        } else {
            if (!isset($data['file'])) return false;
            $file = $data['file'];
            if (!isset($file['error'])) return false;
             // ファイルサイズが大き過ぎる
            if ($file['error'] == 1 || $file['error'] == 2) return false;
            // アップロードなし
            if ($file['error'] == 4 && $file['size'] == 0) return false;
            $pathinfo = path($file['name']);
            $data['size'] = $file['size'];
            $data['tmp_name'] = $file['tmp_name'];
            $data['alternative'] = $pathinfo[0];
        }
        _default($data['foreign_key'], NULL);
        $model->data = array(
            $model->alias => array(
                'model'       => $data['model'],
                'foreign_key' => $data['foreign_key'],
                'basename'    => uniqcode() . ".{$pathinfo[1]}",
                'extension'   => $pathinfo[1],
                'size'        => $data['size'],
                'alternative' => $data['alternative'],
                'identifier'  => $data['identifier'],
                'modified'    => $data['modified'],
            ),
        );
        if (!empty($data['created'])) {
            $model->data[$model->alias]['created'] = $data['created'];
        }
        
        // アップロードなし(FTP等でアップロード済み)
        if (!empty($data['uploaded'])) {
            return rename(
                $data['tmp_name'],
                UPLOADS . $model->data[$model->alias]['basename']
            );
        }
        if (!move_uploaded_file(
            $data['tmp_name'],
            UPLOADS . $model->data[$model->alias]['basename']
        )) return false;
        
        if (!chmod(
            UPLOADS . $model->data[$model->alias]['basename'],
            0777
        )) return false;
        
        return true;
    }
    
    // @param object $model
    // @return void
    function afterSave(Model $model, $created, $options = Array())
    {
        $this->unlink($model);
        
        if (!$this->exchange) return;
        
        $model->prev_record = false;
        $conditions = array(
            "{$model->alias}.basename" =>
                $model->data[$model->alias]['basename'],
            "{$model->alias}.model" => 'Attachment',
        );
        $model->deleteAll($conditions);
        $model->prev_record = true;
        $this->exchange = false;
    }
    
    // @param object $model
    // @return void
    function afterDelete(Model $model, $options = Array())
    {
        $this->unlink($model);
    }
    
    // @param object $model
    // @return void
    function unlink($model)
    {
        if (empty($model->prev_record)) return;
        $filename = UPLOADS . $model->prev_record[$model->alias]['basename'];
        if (is_writable($filename)) unlink($filename);
    }
    
    // @param object $model
    // @return array
    function getOptions($model)
    {
        $params = array(
            'fields' => array(
                "{$model->alias}.id",
                "{$model->alias}.extension",
                "{$model->alias}.alternative",
                "{$model->alias}.size",
                "{$model->alias}.created",
            ),
            'conditions' => array(
                "{$model->alias}.model =" => $model->alias,
            ),
            'order' => array(
                "{$model->alias}.created" => 'ASC',
            ),
        );
        App::import('Vendor', 'Iggy.filesize_format');
        $records = $model->find('all', $params);
        $attachments = array();
        foreach ($records as $record) {
            $attachments[$record[$model->alias]['id']] =
                $record[$model->alias]['alternative'] . '.'
              . $record[$model->alias]['extension'] . ' '
              . '[' . filesize_format($record[$model->alias]['size']) . '] '
              . '[' . $record[$model->alias]['created'] . ']';
        }
        return $attachments;
    }
}
