<?php
class AttachmentHelper extends AppHelper
{
    var $helpers = array(
        'Form',
        'Js',
        'Html',
        'Session',
        'Iggy.Thumbnail',
    );
    var $number = 0;
    var $valids = array();
    var $_data;
    var $maxwidth = 200;
    var $maxheight = 150;
    
    function beforeRender($viewFile)
    {
        $this->valids = fgetyml('attachments_valids');
        App::import('Core', 'Set');
        $this->setData($this->request->data);
        App::import('Vendor', 'Iggy.filesize_format');
        App::import('Vendor', 'Iggy.datetime_format');
    }
    
    // @param array $record
    // @return void
    function setData($record)
    {
        if (empty($record)) return;
        $this->_data = Hash::combine(
            $record,
            'Attachment.{n}.identifier',
            'Attachment'
        );
    }
    
    // @param string $identifier
    // @return boolean
    function is($identifier)
    {
        if (empty($this->_data[$identifier])) return false;
        return true;
    }
    
    // @param string $model
    // @param string $identifier
    // @param array $options
    // boolean "existing"
    // boolean "comment"
    // array "attachments"
    // @return void
    function input($model, $identifier = '*', $options = array())
    {
        extract($options, EXTR_SKIP);
        if (!isset($existing))
            $existing = true;
        if (!isset($comment))
            $comment = true;
        if (!isset($attachments))
            $attachments = array();
        if ($this->number === 0) {
            echo $this->Html->script('/Iggy/js/attachment');
        }
        if (!empty($this->_data[$identifier]['id'])) {
            $id = $this->_data[$identifier]['id'];
        } else {
            $id = '';
        }
        echo $this->Form->input(
            'Attachment.' . $this->number . '.file',
            array(
                'type'          => 'file',
                'class'         => 'attachment_files',
                'identifier'    => $identifier,
                'label' => false,    // labelを出力しない
                'div' => false       // divで囲わない
            )
        );
        if (!empty($id)) {
            echo '<div>' . $this->Form->hidden(
                "Attachment.{$this->number}.id",
                array('value' => $id)
            ) . '</div>';
            if ($existing) {
                echo '<div class="small">';
                $this->link($identifier);
                $this->size($identifier);
                $this->modified($identifier);
                echo '</div>';
                echo $this->Form->checkbox(
                    "Attachment.{$this->number}.remove",
                    array(
                        'class'         => 'attachment_removes',
                        'identifier'    => $identifier,
                        'label' => false,    // labelを出力しない
                        'div' => false       // divで囲わない
                    )
                );
                echo '<span class="comment">添付ファイル削除</span>';
            }
        }
        if ($comment) {
            echo sprintf(
                '<div class="comment">指定できるファイルの拡張子は、<br />'
                . '[%s]です。</div>',
                implode(', ', $this->valids[$model][$identifier]['extensions'])
            );
            echo sprintf(
                '<div class="comment">指定できるファイルの最大サイズは、'
                . '%sです。</div>',
                $this->valids[$model][$identifier]['size']
            );
        }
        if (!empty($attachments)) {
            echo '<div class="attach">';
            echo $this->Form->select(
                "Attachment.{$this->number}.attach",
                $attachments,
                null,
                array(
                    'class'      => 'attachment_attaches',
                    'identifier' => $identifier,
                )
            );
            echo '</div>';
        }
        echo $this->Form->hidden(
            "Attachment.{$this->number}.model",
            array('value' => $model)
        );
        echo $this->Form->hidden(
            "Attachment.{$this->number}.identifier",
            array('value' => $identifier)
        );
        //$view =& ClassRegistry::getObject('view');
        //$errors = $view->getVar("errors_attachments_{$this->number}");
        if (isset($this->_View->viewVars["errors_attachments_{$this->number}"])) {
            $errors = $this->_View->viewVars["errors_attachments_{$this->number}"];
            $error_fields = array_keys($errors);
            $error = null;
            if (in_array('extension', $error_fields)) {
                $error = '不正なファイルが指定されました。';
            } elseif (in_array('size', $error_fields)) {
                $error = 'ファイルサイズが大き過ぎます。';
            }
            if (!empty($error)) {
                echo "<div class=\"failure\">{$error}</div>";
            }
        }
        $this->number++;
    }
    
    // @param string $identifier
    // @param array $options
    // boolean "extension"
    // @return void
    function link($identifier, $options = array())
    {
        if (empty($this->_data[$identifier])) return;
        extract($options, EXTR_SKIP);
        if (!isset($extension))
            $extension = true;
        $data = $this->_data[$identifier];
        $filename = $data['alternative'];
        if ($extension) $filename .= ".{$data['extension']}";
        echo $this->Html->link(
            "{$filename}",
            array('plugin' => 'iggy', 'controller' => 'Attachments', 'action' => 'download', "{$data['id']}")
        );
    }
    
    // @param string $identifier
    // @return void
    function image($identifier)
    {
        if (empty($this->_data[$identifier])) return;
        $data =& $this->_data[$identifier];
        echo $this->Html->image(
            "/Iggy/attachments/download/{$data['id']}/0",
            array(
                'extension' => $data['extension'],
            )
        );
    }
    
    // @param string $identifier
    // @param array $options
    // integer "maxwidth"
    // integer "maxheight"
    // @return void
    function thumbnail($identifier, $options = array())
    {
        if (empty($this->_data[$identifier])) return;
        $data =& $this->_data[$identifier];
        extract($options, EXTR_SKIP);
        _default($maxwidth, $this->maxwidth);
        _default($maxheight, $this->maxheight);
        echo $this->Html->link(
            $this->Html->image(
                "/Iggy/attachments/download/{$data['id']}/0",
                array(
                    'width' => $this->Thumbnail->getWidth(
                        $data['basename'],
                        $maxwidth,
                        $maxheight
                    )
                )
            ),
            "/Iggy/attachments/download/{$data['id']}/0",
            array(
                'escape'    => false,
                'target'    => '_blank',
                'extension' => $data['extension'],
            )
        );
    }
    
    // @param string $identifier
    // @param array $options
    // string "unit"
    // boolean "format"
    // @return void
    function size($identifier, $options = array())
    {
        extract($options, EXTR_SKIP);
        if (!isset($unit))
            $unit = 'MB';
        if (!isset($format))
            $format = true;
        if (empty($this->_data[$identifier])) return;
        $data =& $this->_data[$identifier];
        if ($format) echo '&nbsp;[';
        echo filesize_format($data['size'], $unit);
        if ($format) echo ']';
    }
    
    // @param string $identifier
    // @param array $options
    // string "mode" => "DT" or "D" or "T"
    // boolean "format"
    // @return void
    function modified($identifier, $options = array())
    {
        extract($options, EXTR_SKIP);
        if (!isset($mode))
          $mode = null;
        if (!isset($format))
          $format = true;
        if (empty($this->_data[$identifier])) return;
        $data =& $this->_data[$identifier];
        if ($format) echo '&nbsp;[';
        echo datetime_format($data['modified'], $mode);
        if ($format) echo ']';
    }
}
