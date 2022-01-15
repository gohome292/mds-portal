<?php echo $this->Html->script('elements/edit');
echo $this->element('menu_edit'); ?>
<div class="hr"><hr/></div>
<?php echo $this->Form->create('AdmInformation', array('type' => 'file'));
echo $this->Form->input('Information.id'); ?>
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />
<div class="list">
<table>
    <tr>
        <th><?php echo $fieldnames['customer_organization_id']; ?><span class="required_mark">*</span></th>
        <td><?php echo $this->Form->input('Information.customer_organization_id', 
            array('options' => $customer_organizations, 'empty' => true, 'label' => false, 'div' => false,)); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['level']; ?></th>
        <td><?php echo $this->Form->input('Information.level', array('label' => false, 'div' => false,)); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['regular']; ?></th>
        <td class="regular"><?php
        echo $this->Form->checkbox(
            'Information.regular',
            array('1' => '1'),
            _default(@$this->request->data['Information']['regular'], '')
        );
        ?><span class="comment">チェックすると「お役立ち情報」に表示されます。</span></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['title']; ?><span class="required_mark">*</span></th>
        <td><?php echo $this->Form->text('Information.title'); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['content']; ?><span class="required_mark">*</span></th>
        <td><?php echo $this->Form->input('Information.content', array('label' => false, 'div' => false,)); ?></td>
    </tr>
    <tr>
        <th>添付1</th>
        <td><div class="comment">指定できるファイルの拡張子は、<br />[ppt, doc, xls, pptx, docx, xlsx, pdf, tiff, zip, lzh, cab, exe]です。<br />一度の登録作業で指定できるファイルの最大サイズは、<br />添付1～添付5の合計で<?php echo ini_get('upload_max_filesize'); ?>Bです。<br /><br /><?php echo ini_get('upload_max_filesize'); ?>Bを超えるファイルは事前にRITSへ送付してください。<br />送付いただいたファイルはリストボックスから選択できるようになります。</div>
        <?php $this->Attachment->input(
            'Information',
            'file1',
            array(
                'comment' => false,
                'attachments' => $attachments,
            )
        ); ?></td>
    </tr>
    <tr>
        <th>添付2</th>
        <td><?php $this->Attachment->input(
            'Information',
            'file2',
            array(
                'comment' => false,
                'attachments' => $attachments,
            )
        ); ?></td>
    </tr>
    <tr>
        <th>添付3</th>
        <td><?php $this->Attachment->input(
            'Information',
            'file3',
            array(
                'comment' => false,
                'attachments' => $attachments,
            )
        ); ?></td>
    </tr>
    <tr>
        <th>添付4</th>
        <td><?php $this->Attachment->input(
            'Information',
            'file4',
            array(
                'comment' => false,
                'attachments' => $attachments,
            )
        ); ?></td>
    </tr>
    <tr>
        <th>添付5</th>
        <td><?php $this->Attachment->input(
            'Information',
            'file5',
            array(
                'comment' => false,
                'attachments' => $attachments,
            )
        ); ?></td>
    </tr>
<?php echo $this->element('edit_common_area'); ?>
</table>
</div><!-- .list -->
<?php echo $this->Form->end(); ?>
