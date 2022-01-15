<?php echo $this->Html->script('elements/edit');
echo $this->element('menu_edit'); ?>
<div class="hr"><hr /></div>
<?php echo $this->Form->create('Attachment');
echo $this->Form->hidden('uploaded', array('value' => '1')); ?>
<div class="list">
<table>
    <tr>
        <th><?php echo $fieldnames['basename']; ?></th>
        <td><?php echo $this->Form->select('tmp_name', $tmp_names); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['alternative']; ?><span class="required_mark">*</span></th>
        <td><?php echo $this->Form->input('alternative'); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['model']; ?></th>
        <td>Attachment<?php echo $this->Form->hidden('model', array('value' => 'Attachment')); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['identifier']; ?></th>
        <td>attachment<?php echo $this->Form->hidden('identifier', array('value' => 'attachment')); ?></td>
    </tr>
<?php echo $this->element('edit_common_area'); ?>
</table>
</div><!-- .list -->
<?php echo $this->Form->end(); ?>
