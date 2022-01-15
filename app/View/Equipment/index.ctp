<?php echo $this->element('mds.nav');
echo $this->Html->script('equipment/index'); ?>
<!-- MAIN --><div id="MAIN" class="clear" role="main">
<div id="MENU" role="complementary" style="display: none;">
    <!-- 対象選択 -->
    <?php
    echo $this->Tree->run($customer_organizations, array(
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
