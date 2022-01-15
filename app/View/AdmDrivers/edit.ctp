<?php 
if(empty($this->request->data['Driver']['id'])){
    echo $this->element('menu_edit');
} else {
    $id = $this->request->data['Driver']['id'];
    echo $this->element('menu_edit_remove', compact('id'));
}

?>
<div class="hr"><hr /></div>
<?php echo $this->Form->create('AdmDrivers', array('type' => 'file')); ?>
<div class="list">
<table>
    <tr>
        <th>分類選択</th>
        <td colspan="3"><?php echo $this->Form->select(
	            'Driver.driver_manual_type_id',
	            $type_list,
	            array(
	                'tabindex' => '1',
                    'empty'    => false,)
	        ); ?></td>
    </tr>
    <tr>
        <th>設置場所<span class="required_mark">*</span></th>
        <td colspan="3"><?php echo $this->Form->input('Driver.place', array('size' => '101', 'div' => false, 'label' => false)); ?></td>
    </tr>
    <tr>
        <th>機器管理番号<span class="required_mark">*</span></th>
        <td colspan="3"><?php echo $this->Form->input('Driver.kiki',  array('cols'=>125, 'div' => false, 'label' => false)); ?></td>
    </tr>
    <tr>
    <td></td>
    <td colspan="3">
        <div class="comment">指定できるファイルの拡張子は[ppt, doc, xls, pptx, docx, xlsx, pdf, tiff, zip, lzh, cab, exe]です。<br />
                             一度の登録作業で指定できるファイルの最大サイズは、合計で<?php echo ini_get('upload_max_filesize'); ?>Bです。</div>
    </td>
    </tr>
    <tr>
        <th>ファイル１</th>
        <td><?php $this->Attachment->input('Driver', 'file1', array('comment' => false)); ?></td>
        <th>ファイル１１</th>
        <td><?php $this->Attachment->input('Driver', 'file11', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>ファイル２</th>
        <td><?php $this->Attachment->input('Driver', 'file2', array('comment' => false)); ?></td>
        <th>ファイル１２</th>
        <td><?php $this->Attachment->input('Driver', 'file12', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>ファイル３</th>
        <td><?php $this->Attachment->input('Driver', 'file3', array('comment' => false)); ?></td>
        <th>ファイル１３</th>
        <td><?php $this->Attachment->input('Driver', 'file13', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>ファイル４</th>
        <td><?php $this->Attachment->input('Driver', 'file4', array('comment' => false)); ?></td>
        <th>ファイル１４</th>
        <td><?php $this->Attachment->input('Driver', 'file14', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>ファイル５</th>
        <td><?php $this->Attachment->input('Driver', 'file5', array('comment' => false)); ?></td>
        <th>ファイル１５</th>
        <td><?php $this->Attachment->input('Driver', 'file15', array('comment' => false)); ?></td>
    </tr>
        <tr>
        <th>ファイル６</th>
        <td><?php $this->Attachment->input('Driver', 'file6', array('comment' => false)); ?></td>
        <th>ファイル１６</th>
        <td><?php $this->Attachment->input('Driver', 'file16', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>ファイル７</th>
        <td><?php $this->Attachment->input('Driver', 'file7', array('comment' => false)); ?></td>
        <th>ファイル１７</th>
        <td><?php $this->Attachment->input('Driver', 'file17', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>ファイル８</th>
        <td><?php $this->Attachment->input('Driver', 'file8', array('comment' => false)); ?></td>
        <th>ファイル１８</th>
        <td><?php $this->Attachment->input('Driver', 'file18', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>ファイル９</th>
        <td><?php $this->Attachment->input('Driver', 'file9', array('comment' => false)); ?></td>
        <th>ファイル１９</th>
        <td><?php $this->Attachment->input('Driver', 'file19', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>ファイル１０</th>
        <td><?php $this->Attachment->input('Driver', 'file10', array('comment' => false)); ?></td>
        <th>ファイル２０</th>
        <td><?php $this->Attachment->input('Driver', 'file20', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>コメント</th>
        <td colspan="3"><?php echo $this->Form->input('Driver.comment', array('cols'=>124, 'div' => false, 'label' => false)); ?></td>
    </tr>
    <?php echo $this->Form->hidden('Driver.id'); ?>
    <?php echo $this->Form->hidden('Driver.customer_organization_id'); ?>
<?php echo $this->element('edit_common_area',array('colspan' => '4', 'div' => false, 'label' => false)); ?>
</table>
</div><!-- .list -->
<?php echo $this->Form->end(); ?>
