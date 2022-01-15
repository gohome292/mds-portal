<?php echo $this->element('menu_index'); ?>
<div class="hr"><hr /></div>
<table>
    <tr>
        <!--<th>対象年月</th>-->
        <td><?php echo $this->Form->input(
            'year_month',
            array('type' => 'select', 
              'options' => $year_months,
              'selected' => $this->request->data['year_month'],
              'empty' => false,
              'label' => false,    // labelを出力しない
        )); ?></td>
        <td id="customer_organization_name">&nbsp;</td>
    </tr>
</table>
<table id="frame">
    <tr>
        <td class="top"><div id="left_area" style="display: none;">
        <?php
        if (!empty($customer_organizations)) {
            echo $this->Tree->run($customer_organizations, array(
                'refer' => true,
                'controller' => true,
            ));
        }
        ?>
        </div><!-- #left_area --></td>
        <td class="top"><div id="right_area" class="list">
        <?php if ($this->session->read('Auth.User.group_id') < 3) {
         echo $this->Form->input(
            'AccesslogYYYYId',
            array('type' => 'select', 
              'options' => $AccesslogYears,
              'selected' => $AccesslogYYYYId,
              'empty' => false, 
              'style' => 'width:80px',
              'label' => false,    // labelを出力しない
              'div' => false       // divで囲わない
            ));
         echo $this->Form->button(
            '操作ログ取得',
            array(
                'id' => 'log_output',
                'type'  => 'button',
            )); }?>
        </div><!-- #right_area --></td>
    </tr>
</table>
<?php echo $this->Form->hidden('customer_organization_id'); ?>
