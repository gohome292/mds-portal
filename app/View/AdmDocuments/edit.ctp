<?php echo $this->Html->script('elements/edit');
echo $this->element('menu_edit'); ?>
<div class="hr"><hr /></div>
<?php echo $this->Form->create('AdmDocument', array('type' => 'file'));
echo $this->Form->input('Document.id'); ?>
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />
<div class="list">
<table>
    <?php if(empty($this->request->named['customer_organization_id']) &&
      empty($this->request->data['Document']['id'])) { ?>
    <tr>
        <th><?php echo $fieldnames['year_month']; ?></th>
        <td><?php
        echo $this->Form->input('Document.year_month', array('div' => false, 'label' => false));
        ?></td>
    </tr>
    <tr id="customer_organization">
        <th><?php echo $fieldnames['customer_organization_id']; ?></th>
        <td id="ReferCustomerOrganization"><?php
        echo $this->Form->text('Document.path', array('size' => 60, 'div' => false, 'label' => false));
        echo $this->Form->button('参照...', array('type' => null, 'id'=> 'browser'));
        echo $this->Form->hidden('Document.customer_organization_id');
        if (!empty($customer_error)) {
            echo "<div class=\"failure\">{$customer_error}</div>";
        }
        ?></td>
    </tr>
    <tr>
        <th>メールで公開する</th>
        <td><?php
          echo $this->Form->input('Document.open_flag', array( 
            'type' => 'checkbox', 
            'checked' => 'true',
            'label' => '　チェックするとメールで報告書を公開します。', 
          ));
          ?></td>
    </tr>
    <?php }else{?>
    <tr>
        <th><?php echo $fieldnames['year_month']; ?></th>
        <td><?php
        echo substr($this->request->data['Document']['year_month'], 0, 4) . '年' . intval(substr($this->request->data['Document']['year_month'], 4, 2)) . '月';
        echo $this->Form->hidden('Document.year_month');
        ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['customer_organization_id']; ?></th>
        <td><?php
        echo $customer_organization_path;
        echo $this->Form->hidden('Document.customer_organization_id');
        if(empty($this->request->data['Document']['id'])) {
            echo $this->Form->hidden('Document.open_flag', array('value'=>'0'));
        } 
        ?></td>
    </tr>
    <?php if(!empty($this->request->data['Document']['id'])) { ?>
    <tr>
        <th>メールで公開する</th>
        <td><?php
          if ($this->request->data['Document']['open_flag'] == 0) {
              echo '公開済';
              echo $this->Form->hidden('Document.open_flag');
          } elseif ($this->request->data['Document']['open_flag'] == 2) {
              echo '公開待ち';
              echo $this->Form->hidden('Document.open_flag');
          } else {
              echo $this->Form->input('Document.open_flag', array( 
                'type' => 'checkbox', 
                'checked' => 'true',
                'label' => '　チェックするとメールで報告書を公開します。', 
                'div' => false       // divで囲わない
              ));
          }
          ?></td>
    </tr>
    <?php } ?>
    <?php } ?>
    <tr>
        <th>添付1</th>
        <td><div class="comment">指定できるファイルの拡張子は、<br />[ppt, doc, xls, pptx, docx, xlsx, pdf, tiff, zip, lzh, cab]です。<br />
        一度の登録作業で指定できるファイルの最大サイズは、<br />添付1～添付5の合計で
        <?php echo ini_get('upload_max_filesize'); ?>Bです。</div>
        <?php $this->Attachment->input('Document', 'file1', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>添付2</th>
        <td><?php $this->Attachment->input('Document', 'file2', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>添付3</th>
        <td><?php $this->Attachment->input('Document', 'file3', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>添付4</th>
        <td><?php $this->Attachment->input('Document', 'file4', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>添付5</th>
        <td><?php $this->Attachment->input('Document', 'file5', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['comment']; ?></th>
        <td><?php echo $this->Form->input('Document.comment', array(
                        'label' => false,    // labelを出力しない
                        'div' => false       // divで囲わない
                       )); ?></td>
    </tr>
<?php echo $this->element('edit_common_area'); ?>
</table>
</div><!-- .list -->
<?php echo $this->Form->end(); ?>
