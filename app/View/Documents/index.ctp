<?php echo $this->element('mds.nav');
echo $this->Html->script('documents/index');
echo $this->Html->css('documents/index'); ?>
<!-- MAIN --><div id="MAIN" class="clear" role="main">
<div id="MENU" role="complementary" style="display: none;">
    <?php echo $this->Form->input(
        'year_month',
        array('type' => 'select',
        'options' => $year_months,
        'selected' => $this->request->data['year_month'],
        'tabindex' => '1',
        'empty' => false,
        'style' => 'font-size: 16pt; margin-top:2px',
        'label' => false,    // labelを出力しない
    )); ?>
    <!-- 対象選択 -->
    <?php
    echo $this->tree->run($customer_organizations, array(
        'refer' => true,
        'controller' => true,
    ));
    ?>
</div>
<div id="CONTENT">
    <!-- 結果表示 -->
    
</div>
<!-- /MAIN --></div>
<?php echo $this->Form->hidden('customer_organization_id'); ?>
