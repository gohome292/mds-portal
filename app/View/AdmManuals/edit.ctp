<?php 
if(empty($this->request->data['Manual']['id'])){
    echo $this->element('menu_edit');
} else {
    $id = $this->request->data['Manual']['id'];
    echo $this->element('menu_edit_remove', compact('id'));
}

?>
<div class="hr"><hr /></div>
<?php echo $this->Form->create('AdmManuals', array('type' => 'file')); ?>
<div class="list">
<table>
    <tr>
        <th>分類選択</th>
        <td colspan="3"><?php echo $this->Form->select(
	            'Manual.driver_manual_type_id',
	            $type_list,
	            array(
	                'tabindex' => '1',
                    'empty'    => false,)
	        ); ?></td>
    </tr>
    <tr>
        <th>タイプ<span class="required_mark">*</span></th>
        <td colspan="3"><?php echo $this->Form->input('Manual.type', array('size' => '90', 'label' => false, 'div' => false)); ?></td>
    </tr>
    <tr>
        <th>カテゴリ<span class="required_mark">*</span></th>
        <td colspan="3"><?php echo $this->Form->input('Manual.category', array('size' => '90', 'label' => false, 'div' => false)); ?></td>
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
        <td><?php $this->Attachment->input('Manual', 'file1', array('comment' => false)); ?></td>
        <th>ファイル１１</th>
        <td><?php $this->Attachment->input('Manual', 'file11', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>ファイル２</th>
        <td><?php $this->Attachment->input('Manual', 'file2', array('comment' => false)); ?></td>
        <th>ファイル１２</th>
        <td><?php $this->Attachment->input('Manual', 'file12', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>ファイル３</th>
        <td><?php $this->Attachment->input('Manual', 'file3', array('comment' => false)); ?></td>
        <th>ファイル１３</th>
        <td><?php $this->Attachment->input('Manual', 'file13', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>ファイル４</th>
        <td><?php $this->Attachment->input('Manual', 'file4', array('comment' => false)); ?></td>
        <th>ファイル１４</th>
        <td><?php $this->Attachment->input('Manual', 'file14', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>ファイル５</th>
        <td><?php $this->Attachment->input('Manual', 'file5', array('comment' => false)); ?></td>
        <th>ファイル１５</th>
        <td><?php $this->Attachment->input('Manual', 'file15', array('comment' => false)); ?></td>
    </tr>
        <tr>
        <th>ファイル６</th>
        <td><?php $this->Attachment->input('Manual', 'file6', array('comment' => false)); ?></td>
        <th>ファイル１６</th>
        <td><?php $this->Attachment->input('Manual', 'file16', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>ファイル７</th>
        <td><?php $this->Attachment->input('Manual', 'file7', array('comment' => false)); ?></td>
        <th>ファイル１７</th>
        <td><?php $this->Attachment->input('Manual', 'file17', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>ファイル８</th>
        <td><?php $this->Attachment->input('Manual', 'file8', array('comment' => false)); ?></td>
        <th>ファイル１８</th>
        <td><?php $this->Attachment->input('Manual', 'file18', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>ファイル９</th>
        <td><?php $this->Attachment->input('Manual', 'file9', array('comment' => false)); ?></td>
        <th>ファイル１９</th>
        <td><?php $this->Attachment->input('Manual', 'file19', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>ファイル１０</th>
        <td><?php $this->Attachment->input('Manual', 'file10', array('comment' => false)); ?></td>
        <th>ファイル２０</th>
        <td><?php $this->Attachment->input('Manual', 'file20', array('comment' => false)); ?></td>
    </tr>
    <tr>
        <th>コメント</th>
        <td colspan="3"><?php echo $this->Form->input('Manual.comment', array('cols'=>110, 'label' => false, 'div' => false)); ?></td>
    </tr>
    <?php echo $this->Form->hidden('Manual.id'); ?>
    <?php echo $this->Form->hidden('Manual.customer_organization_id'); ?>
<?php echo $this->element('edit_common_area',array('colspan' => '4')); ?>
</table>
</div><!-- .list -->
<?php echo $this->Form->end(); ?>
