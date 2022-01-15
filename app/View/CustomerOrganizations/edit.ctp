<?php
echo $this->Html->script('elements/edit');
if (isset($this->request->data['CustomerOrganization']['id'])) {
    $id = $this->request->data['CustomerOrganization']['id'];
} else {
    $id = 0;
}
echo $this->element('menu_edit_remove', compact('id')); ?>
<div class="hr"><hr /></div>
<?php echo $this->Form->create('CustomerOrganization');
echo $this->Form->input('id');?>
<div class="list">
<table>
    <tr>
        <th><?php echo $fieldnames['parent_id']; ?></th>
        <td id="ReferCustomerOrganization"><?php
        echo $this->Form->text('path', array('readonly' => 'true'));
        echo $this->Form->button('参照...', array('type' => null));
        echo $this->Form->hidden('parent_id');
        ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['name']; ?><span class="required_mark">*</span></th>
        <td><?php echo $this->Form->input('name',array('label' => false, 'div' => false)); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['sort']; ?><span class="required_mark">*</span></th>
        <td><?php echo $this->Form->input(
            'sort',
            array(
                'class'     => 'numeric',
                'maxlength' => '9',
                'label' => false, 'div' => false
            )
        ); ?></td>
    </tr>
    <?php if (empty($this->request->data['CustomerOrganization']['parent_id']) && !empty($this->request->data['CustomerOrganization']['id'])) {?>
    <tr>
        <th>表示メニュー</th>
        <td><table border="0" width="100%">
          <tr><td>
          <?php
          echo $this->Form->hidden('CustomerNav.id');
          echo $this->Form->input('CustomerNav.documents', array( 
            'type' => 'checkbox', 
            'div' => false,
            'label' => '報告書',    // チェックボックスのラベル
          ));
          echo '</td><td>';
          echo $this->Form->input( 'CustomerNav.equipments', array( 
            'type' => 'checkbox', 
            'div' => false,
            'label' => '機器情報',    // チェックボックスのラベル
          ));
          echo '</td><td></td></tr><tr><td>';
          echo $this->Form->input('CustomerNav.drivers', array( 
            'type' => 'checkbox', 
            'div' => false,
            'label' => 'ドライバー',    // チェックボックスのラベル
          ));
          echo '</td><td>';
          echo $this->Form->input('CustomerNav.manuals', array( 
            'type' => 'checkbox', 
            'div' => false,
            'label' => 'マニュアル',    // チェックボックスのラベル
          ));
          echo '</td><td>';
          echo $this->Form->input('CustomerNav.macd_workflows', array( 
            'type' => 'checkbox', 
            'div' => false,
            'label' => '月次報告',    // チェックボックスのラベル
          ));
        ?></td></tr></table></td>
    </tr>
    <tr>
        <th>開始年月</th>
        <td><?php echo $this->Form->input('CustomerNav.start_year_month', 
          array('label' => false, 'div' => false));?></td>
    </tr>
    <?php } ?>
<?php echo $this->element('edit_common_area'); ?>
</table>
</div><!-- .list -->
<?php echo $this->Form->end(); ?>
