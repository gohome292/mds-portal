<?php if (Configure::read('App.screen.compact')):
echo $this->Html->css('elements/compact.edit');
endif;
echo $this->element('mds.nav');
echo $this->Html->script('macd_workflows/edit');
echo $this->Html->css('macd_workflows/edit');
echo $this->Form->create('MacdWorkflow', array('type' => 'file'));
echo $this->Form->input('MacdWorkflow.id');
echo $this->element('css');
?>
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />
<div align="center">
<table width="900px">
    <tr>
        <th align="left" width="10%" style="font-size: nomal">報告日：</th>
        <td align="left" width="20%" style="font-size: nomal" ><?php 
            if (!empty($this->request->data['MacdWorkflow']['applied']) &&
                substr($this->request->data['MacdWorkflow']['applied'], 0, 4) > '0000') {
                echo substr($this->request->data['MacdWorkflow']['applied'], 0, 4) . '年' . intval(substr($this->request->data['MacdWorkflow']['applied'], 5, 2)) . '月' . intval(substr($this->request->data['MacdWorkflow']['applied'], 8, 2)) . '日';
                echo $this->Form->hidden('MacdWorkflow.applied');
            } ?>
        </td>
        <th align="left" width="10%" style="font-size: nomal">報告者：</th>
        <td align="left" width="30%" style="font-size: nomal"><?php 
            echo h($username);
            echo $this->Form->hidden('MacdWorkflow.applied_user_id');
            echo $this->Form->hidden('MacdWorkflow.applied');
            echo $this->Form->hidden('MacdWorkflow.customer_organization_id');?>
        </td>
        <td align="right" width="30%">
        <?php if (!empty($template)) {
          echo '<span class="submit">' . $this->Form->button('報告書テンプレートのダウンロード', array('id' => 'templateDl', 'type' => 'button', 'value' => $template['Attachment']['id'])) . '</span>'; 
        }?>
        </td>
    </tr>
</table>
<div class="list">
<table width="900px">
    <tr>
        <th width="30%">タイトル<span class="required_mark">*</span></th>
        <td align="left">
        <?php if($this->session->read('Auth.User.group_id') == 3 && 
                (empty($this->request->data['MacdWorkflow']['status']) || $this->request->data['MacdWorkflow']['status'] == 1)){
            echo $this->Form->input('applied_title', array('type' => 'text', 'size' => '150', 'div' => false, 'label' => false));
        } else {
            echo $this->Form->input('applied_title', array('type' => 'text', 'size' => '150', 'readonly' => true, 'div' => false, 'label' => false ));
        } ?>
        </td>
    </tr>
    <tr>
        <th>報告書</th>
        <td align="left"><?php
            if($this->session->read('Auth.User.group_id') == 3 && 
                (empty($this->request->data['MacdWorkflow']['status']) || $this->request->data['MacdWorkflow']['status'] == 1)){ ?>
                <div class="fileInput">
                <?php $this->Attachment->input('MacdWorkflow', 'aplForm1', array('comment' => false, 'extension' => false));
            } else {
                for ($j = 1; $j <= 5; $j++):
                    if ($this->Attachment->is("aplForm{$j}")) {
                        echo '<li>';
                        $this->Attachment->link("aplForm{$j}", array('extension' => false));
                        $this->Attachment->size("aplForm{$j}");
                        echo '</li>';
                    }
                endfor;
            }
        ?></div>
        </td>
    </tr>
    <tr>
        <th>添付ファイル</th>
        <td align="left"><?php 
            if($this->session->read('Auth.User.group_id') == 3 && 
                (empty($this->request->data['MacdWorkflow']['status']) || $this->request->data['MacdWorkflow']['status'] == 1) ||
              $this->session->read('Auth.User.group_id') == 4 && 
                ($this->request->data['MacdWorkflow']['status'] == 2 || $this->request->data['MacdWorkflow']['status'] == 3)){ ?>
                <div class="fileInput">
                <?php $this->Attachment->input('MacdWorkflow', 'attach1', array('comment' => false, 'extension' => false)); 
                    $this->Attachment->input('MacdWorkflow', 'attach2', array('comment' => false, 'extension' => false));
                    echo '<br/>';
                    $this->Attachment->input('MacdWorkflow', 'attach3', array('comment' => false, 'extension' => false));
                    echo '<br/>';
                    $this->Attachment->input('MacdWorkflow', 'attach4', array('comment' => false, 'extension' => false));
                    echo '<br/>';
                    $this->Attachment->input('MacdWorkflow', 'attach5', array('comment' => false, 'extension' => false));?></div>
        <div class="comment">指定できるファイルの拡張子は、[ppt, doc, xls, pptx, docx, xlsx, pdf, tiff, zip, lzh, cab]です。<br/>
            一度の登録作業で指定できるファイルの最大サイズは、合計で<?php echo ini_get('upload_max_filesize'); ?>Bです。</div>                    
                <?php } else {
                for ($j = 1; $j <= 5; $j++):
                    if ($this->Attachment->is("attach{$j}")) {
                        echo '<li>';
                        $this->Attachment->link("attach{$j}", array('extension' => false));
                        $this->Attachment->size("attach{$j}");
                        echo '</li>';
                    }
                endfor;
            }?>
        </td>
    </tr>
    <tr>
        <th>ステータス</th>
        <td align="left"><?php
            if(empty($this->request->data['MacdWorkflow']['status'])){
                $this->request->data['MacdWorkflow']['status'] = 1;
            }
            echo h(getStatusName(trim($this->request->data['MacdWorkflow']['status'])));
            echo $this->Form->hidden('MacdWorkflow.status');
            ?>
        </td>
    </tr>
    <tr>
        <th>コメント</th>
        <td align="left">
        <?php if($this->session->read('Auth.User.group_id') == 3 && 
                (empty($this->request->data['MacdWorkflow']['status']) || $this->request->data['MacdWorkflow']['status'] == 1)){
            echo $this->Form->input('comment', array('type' => 'textarea', 'cols'=>148, 'rows'=>6, 'div' => false, 'label' => false ));
        } elseif($this->session->read('Auth.User.group_id') == 4 && 
                   ($this->request->data['MacdWorkflow']['status'] == 2 || $this->request->data['MacdWorkflow']['status'] == 3)){
            echo $this->Form->input('comment', array('type' => 'textarea', 'cols'=>148, 'rows'=>6, 'div' => false, 'label' => false ));
        } else {
            echo $this->Form->input('comment', array('type' => 'textarea', 'cols'=>148, 'rows'=>6, 'readonly' => true, 'div' => false, 'label' => false ));
        } ?>
        </td>
    </tr>
    <tr>
        <td align="left" colspan="2"><span class="comment"><span class="required_mark">*</span>必須項目<span></td>
    </tr>
</table></div>
<br/><br/>
<table width="900px"  border="0">
  <?php if ($this->session->read('Auth.User.group_id') == 4) {?>
     <tr>
        <td align="right">
        <?php if ($this->request->data['MacdWorkflow']['status'] == 2) {
               echo $this->Form->button('受　付', array('type' => 'submit', 'name' => 'act', 'value' => 'reciept')); 
               echo '　';
            } elseif ($this->request->data['MacdWorkflow']['status'] == 3) {
               echo $this->Form->button('完　了', array('type' => 'submit', 'name'  => 'act', 'value' => 'finish'));
               echo '　';
            }
            echo $this->Form->button('一覧に戻る', array('id' => 'return_btn', 'type' => 'reset')); 
         ?>
        </td>
    </tr>
  <?php } elseif ($this->session->read('Auth.User.group_id') == 3) {?> 
    <tr>
        <td align="right">
        <?php if (empty($this->request->data['MacdWorkflow']['status']) || $this->request->data['MacdWorkflow']['status'] == 1) {
               echo $this->Form->button('保　存', array('type' => 'submit', 'name'  => 'act', 'value' => 'save'));
               echo '　';
               //if ($this->request->data['MacdWorkflow']['status'] == 1) {
                   echo $this->Form->button('提　出', array('type' => 'submit', 'name'  => 'act', 'value' => 'request'));
                   echo '　';
               //}
            }
            echo $this->Form->button('一覧に戻る', array('id' => 'return_btn', 'type' => 'reset')); 
        ?>
        </td>
    </tr>
  <?php } else {?>
    <tr>
        <td align="right">
        <?php echo $this->Form->button('一覧に戻る', array('id' => 'return_btn', 'type' => 'reset')); ?>
        </td>
    </tr>
  <?php } ?>
</table>
</div><!-- .list -->
<?php echo $this->Form->end(); ?>

