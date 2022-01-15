<?php echo $this->element('menu_index'); ?>
<div class="hr"><hr /></div>
<table id="frame">
    <tr>
    <td valign="top">
        <div id="left_area" style="display: none;">
            <?php foreach ($customer_organizations as $k => $v) {
              echo $this->Html->link($v, '/adm_manuals/index/' .$k);
              echo '<br/>';
            } ?>
        </div>
    </td>
    <td valign="top">
        <table>
            <tr>
                <td id='manual_title'>分類情報編集 <?php
                if(!empty($this->request->data['customer_organization_id'])){
                    echo $customer_organizations[$this->request->data['customer_organization_id']];
                }?></td>
            </tr>
            <tr>
                <td id='type_lists'>
                    <?php echo $this->Form->input(
                        'type_list', array(
                            'type' => 'select',
                            'options' => $type_list,
                            'selected' => $driver_manual_type_id,
                            'label' => false,    // labelを出力しない
                            'div' => false,      // divで囲わない
                            'empty' => false,)
                    ); ?>
                    <?php echo $this->Form->button('追加', array('type' => 'button', 'id' => 'type_list_add'));
                    echo $this->Form->button('編集', array('type' => 'button', 'id' => 'type_list_edit'));
                    echo $this->Form->button('削除', array('type' => 'button', 'id' => 'type_list_remove')); ?>
                 </td>
            </tr>
            <tr>
                <td id='manual_title'>マニュアル一覧</td>
            </tr>
            <tr>
            	<td class="top">
            	<div id="right_area" class="list"></div><!-- #right_area -->
            	</td>
            </tr>
        </table>
    </td>
    </tr>
</table>
<?php echo $this->Form->hidden('customer_organization_id'); ?>
<?php echo $this->Form->hidden('driver_manual_id'); ?>
