<?php echo $this->element('menu_index'); ?>
<div class="hr"><hr /></div>
<table>
    <tr>
        <td id="space"></td>
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
        <td class="top"><div id="right_area" class="list"></div><!-- #right_area --></td>
    </tr>
</table>
<?php echo $this->Form->hidden('customer_organization_id'); ?>
