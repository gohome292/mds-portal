<?php echo $this->Html->script('elements/edit');
echo $this->element('menu_edit'); ?>
<div class="hr"><hr /></div>
<?php echo $this->Form->create('AdmEquipment', array('type' => 'file'));
echo $this->Form->input('Equipment.id'); ?>
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />
<div class="list">
<table>
    <tr>
        <th><?php echo $fieldnames['customer_organization_id']; ?></th>
        <td><?php
        echo $customer_organization_path;
        echo $this->Form->hidden('Equipment.customer_organization_id');
        ?></td>
    </tr>
    <tr>
        <th>機器配置図</th>
        <td><div class="comment">指定できるファイルの拡張子は、<br />[ppt, doc, xls, pptx, docx, xlsx, pdf, tiff, zip, lzh, cab]です。<br />一度の登録作業で指定できるファイルの最大サイズは、<br />機器配置図・機器台帳の合計で<?php echo ini_get('upload_max_filesize'); ?>Bです。</div><?php $this->Attachment->input('Equipment', 'file1', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>機器台帳</th>
        <td><?php $this->Attachment->input('Equipment', 'file2', array('comment' => false)); ?></td>
    </tr>
<?php echo $this->element('edit_common_area'); ?>
</table>
</div><!-- .list -->
<?php echo $this->Form->end(); ?>
