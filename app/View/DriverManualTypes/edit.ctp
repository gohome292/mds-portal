<?php 
echo $this->Html->script('driver_manual_types/edit');
echo $this->Html->script('elements/edit');
if(empty($id)){
    echo $this->element('menu_edit');
} else {
    echo $this->element('menu_edit_remove', compact('id'));
}
 ?>
<div class="list">
<div class="hr"><hr /></div>
<?php echo $this->Form->create('DriverManualType');
 ?>
<table>
    <tr>
        <th><?php echo '分類'; ?><span class="required_mark">*</span></th>
        <td><?php
        echo $this->Form->input('driver_manual_type', array('div' => false, 'label' => false)); ?></td>
        </tr>
<?php echo $this->element('edit_common_area'); ?>
</table>
<?php echo $this->Form->hidden('id'); ?>
<?php echo $this->Form->hidden('customer_organization_id'); ?>
<?php echo $this->Form->hidden('driver_manual_id'); ?>
</div><!-- .list -->
<?php echo $this->Form->end(); ?>
